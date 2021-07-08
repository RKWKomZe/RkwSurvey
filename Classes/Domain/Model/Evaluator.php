<?php

namespace RKW\RkwSurvey\Domain\Model;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use RKW\RkwSurvey\Domain\Repository\QuestionRepository;
use RKW\RkwSurvey\Domain\Repository\SurveyResultRepository;
use RKW\RkwSurvey\Domain\Repository\QuestionResultRepository;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

/**
 * Evaluator
 *
 * @author Christian Dilger <c.dilger@addorange.de>
 * @copyright Rkw Kompetenzzentrum
 * @package RKW_RkwSurvey
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */

class Evaluator
{

    /**
     * surveyResult
     *
     * @var \RKW\RkwSurvey\Domain\Model\SurveyResult
     */
    protected $surveyResult;

    /**
     * surveyResultRepository
     *
     * @var \RKW\RkwSurvey\Domain\Repository\SurveyResultRepository
     * @inject
     */
    protected $surveyResultRepository;

    /**
     * questionResultRepository
     *
     * @var \RKW\RkwSurvey\Domain\Repository\QuestionResultRepository
     * @inject
     */
    protected $questionResultRepository;

    /**
     * questionRepository
     *
     * @var \RKW\RkwSurvey\Domain\Repository\QuestionRepository
     * @inject
     */
    protected $questionRepository;

    /**
     * @var array
     */
    protected $colors = [];

    /**
     * @var array
     */
    protected $labels = [];

    public function __construct()
    {

        $this->colors = [
            'me' => '#d63f11', // $color-primary
            'my-region' => '#792400', //    #009fee' - $color-blue
            'all-regions' => '#7c7c7b', // #fdc500' - $color-webcheck-yellow
            'gem' => '#4a4a49', // #94c119' - $color-webcheck-green
        ];

        $this->labels = [
            'weighting' => [
                'low' => \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('tx_rkwsurvey_domain_model_evaluator.question.weighting.labels.low', 'RkwSurvey'),
                'neutral' => \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('tx_rkwsurvey_domain_model_evaluator.question.weighting.labels.neutral', 'RkwSurvey'),
                'high' => \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('tx_rkwsurvey_domain_model_evaluator.question.weighting.labels.high', 'RkwSurvey'),
            ]
        ];

        $objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');

        if (!$this->surveyResultRepository) {
            $this->surveyResultRepository = $objectManager->get(SurveyResultRepository::class);
        }

        if (!$this->questionResultRepository) {
            $this->questionResultRepository = $objectManager->get(QuestionResultRepository::class);
        }

        if (!$this->questionRepository) {
            $this->questionRepository = $objectManager->get(QuestionRepository::class);
        }
    }

    /**
     * @param SurveyResult $surveyResult
     */
    public function setSurveyResult(SurveyResult $surveyResult): void
    {
        $this->surveyResult = $surveyResult;
    }

    /**
     * @return array
     */
    public function getSurveyResultUidsGroupedByQuestion(): array
    {
        $survey = $this->surveyResult->getSurvey();
        $groupByQuestion = $this->questionRepository->findOneByGroupedByAndSurvey($survey);
        $myGroupByQuestionResult = $this->questionResultRepository->findByQuestionAndSurveyResult($groupByQuestion, $this->surveyResult);

        $surveyResults = $this->surveyResultRepository->findBySurveyAndQuestionAndAnswerAndFinished($survey, $groupByQuestion, $myGroupByQuestionResult->getAnswer(), $finished = 1);

        $surveyResultUids = [];
        foreach ($surveyResults as $surveyResult) {
            $surveyResultUids[] = $surveyResult->getUid();
        }

        return $surveyResultUids;
    }

    public function getGroupByQuestionAnswer()
    {

        $survey = $this->surveyResult->getSurvey();

        $question = $this->questionRepository->findOneByGroupedByAndSurvey($survey);

        $result = $this->questionResultRepository->findByQuestionAndSurveyResult($question, $this->surveyResult);

        return $this->parseStringToArray($result->getQuestion()->getAnswerOption(), PHP_EOL)[((int) $result->getAnswer() - 1)];

    }

    /**
     * @param              $topics
     * @param null         $scope
     * @return array
     */
    public function getAverageResultByTopics($topics, $scope = null)
    {

        $averageOnTopic = [];

        foreach ($topics as $topic) {

            $questionsByTopic = $topic->getQuestions();

            if ($questionsByTopic->count() > 0) {

                foreach ($questionsByTopic as $question) {

                    if ($question->getBenchmark() === false) {
                        continue;
                    }

                    $averageOnQuestion = [];

                    if ($scope === 'my_values') {
                        $questionResults = $this->questionResultRepository->findByQuestionAndSurveyResultUids($question, [$this->surveyResult->getUid()]);
                    } else if ($scope === 'single_region') {
                        $surveyResultUids = $this->getSurveyResultUidsGroupedByQuestion();
                        $questionResults = $this->questionResultRepository->findByQuestionAndSurveyResultUids($question, $surveyResultUids);
                    } else {    //  all regions
                        $surveyResults = $this->surveyResultRepository->findBySurveyAndFinished($this->surveyResult->getSurvey());
                        $surveyResultUids = [];
                        foreach ($surveyResults as $surveyResult) {
                            $surveyResultUids[] = $surveyResult->getUid();
                        }
                        $questionResults = $this->questionResultRepository->findByQuestionAndSurveyResultUids($question, $surveyResultUids);
                    }

                    $answers = [];
                    foreach ($questionResults as $questionResult) {
                        $answers[] = (int)$questionResult->getAnswer();
                    }

                    //  Was ist der Wert von "weiß ich nicht"? -> skipped = null
                    $answers = array_filter($answers, function ($x) {
                        return $x !== '';
                    });

                    $averageOnQuestion[] = $this->getAverage($answers);

                }

                $averageOnTopic[$topic->getUid()] = $this->getAverage($averageOnQuestion);

            }

        }

        return $averageOnTopic;
    }

    /**
     * @param array        $charts
     * @return string
     */
    public function renderBars(array $charts): string
    {

        $script = '';

        foreach ($charts as $identifier => $comparison) {

            $script .= '
                
                var options_' . $identifier . ' = {
                    chart: {
                        type: \'bar\'
                    },
                    colors: [
                        \'' . $this->colors['me'] . '\',
                        \'' . $this->colors['my-region'] . '\',
                        \'' . $this->colors['all-regions'] . '\',
                    ],
                    series: ' . json_encode($comparison['series']) . ',
                    plotOptions: {
                        bar: {
                            horizontal: false
                        }
                    },
                    legend: {
                        show: true
                    },
                    dataLabels: {
                        enabled: false,
                    },
                    tooltip: {
                        enabled: false,
                    },
                    yaxis: {
                        decimalsInFloat: 0,
                        min: 0,
                        max: 10,
                        labels: {
                            formatter: (value) => { 
                                if (value === 0) {
                                    return \'0 = schwach\'
                                }
                                if (value === 10) {
                                    return \'10 = stark\'
                                }
                                return value
                            },
                        }
                    },
                    xaxis: {
                        categories: ' . json_encode($comparison['topics']) . ',
                    }
                }
                
                var chart_' . $identifier . ' = new ApexCharts(document.querySelector(\'#' . $identifier . '\'), options_' . $identifier . ');

                chart_' . $identifier . '.render(); 
                
            ';

        }

        return $script;

    }

    /**
     * @return array
     */
    public function prepareDonuts(): array
    {

        $donuts = [];

        $surveyQuestions = $this->surveyResult->getSurvey()->getQuestion();

        $groupByTitle = GeneralUtility::trimExplode('(', $this->getGroupByQuestionAnswer(), true)[0];

        foreach ($surveyQuestions as $question) {

            //  use question only if it is of type scale
            if ($question->getType() !== 3) {
                continue;
            }

            $slug = $this->slugify('donuts-' . $question->getQuestion(), '_');

            $donuts[$slug] = [
                'question' => $question->getQuestion(),
            ];

            $surveyResultUids = $this->getSurveyResultUidsGroupedByQuestion();

            //  single_region
            $questionResults = $this->questionResultRepository->findByQuestionAndSurveyResultUids($question, $surveyResultUids);

            $donuts = $this->collectData($questionResults, $donuts, $slug, $key = 'single_region', $title = $groupByTitle);

//            //    Ostdeutschland = all regions
//            $questionResults = $this->questionResultRepository->findByQuestion($question);
//            $donuts = $this->collectData($questionResults, $donuts, $slug, $key = 'all_regions', $title = 'Alle 12 Regionen');

            //  Deutschland = GEM
            $donuts[$slug]['data']['benchmark']['title'] = 'GEM-Expertenbefragung Deutschland';

            if ($question->getBenchmark()) {

                //  get benchmark from question = GEM
                $donuts[$slug]['data']['benchmark']['evaluation']['series'] = $this->parseStringToArray($question->getBenchmarkWeighting(), $delimiter = '|', $checkFloat = true);
                $donuts[$slug]['data']['benchmark']['evaluation']['labels'] = [
                    $this->labels['weighting']['low'],
                    $this->labels['weighting']['neutral'],
                    $this->labels['weighting']['high'],
                ];

            } else {

                $donuts[$slug]['data']['benchmark']['evaluation'] = null;

            }

        }

        return $donuts;
    }

    /**
     * @param array        $charts
     * @return string
     */
    public function renderDonuts(array $charts): string
    {

        $script = '';

        foreach ($charts as $chartIdentifier => $comparisons) {

            foreach ($comparisons['data'] as $comparisonIdentifier => $comparison) {

                if ($comparison['evaluation']['series']) {

                    $identifier = $chartIdentifier . '_' . $comparisonIdentifier;

                    $script .= '
                        var options_' . $identifier . ' = {
                            chart: {
                                type: \'donut\'
                            },
                            colors: [
                                \'' . $this->colors['me'] . '\',
                                \'' . $this->colors['my-region'] . '\',
                                \'' . $this->colors['all-regions'] . '\',
                            ],
                            series: ' . json_encode($comparison['evaluation']['series']) . ',
                            labels: ' . json_encode($comparison['evaluation']['labels']) . ',
                            plotOptions: {
                                pie: {
                                    donut: {
                                        labels: {
                                            show: true,
                                            name: {
                                                show: false
                                            },
                                            value: {
                                                formatter: function (val, w) {
                                                    const total = w.globals.seriesTotals.reduce((acc, val) => acc + val, 0)
                                                    let percent = (100 * val) / total
                                                    return percent.toFixed(1) + \' %\'
                                                }
                                            }
                                        }
                                    }
                                }
                            },
                            legend: {
                                show: true,
                                position: \'bottom\'
                            },
                            dataLabels: {
                                enabled: false
                            },
                            tooltip: {
                                enabled: false,
                            }
                        }
                        
                        var chart_' . $identifier . ' = new ApexCharts(document.querySelector(\'#' . $identifier . '\'), options_' . $identifier . ');
    
                        chart_' . $identifier . '.render(); 
                        
                    ';

                }

            }

        }

        return $script;

    }

    /**
     * @return array
     */
    public function prepareBars(): array
    {

        //  get the topics -> muss dynamisch über Zuordnung im Fragebogen zu Themen erfolgen
        $topics = $this->surveyResult->getSurvey()->getTopics();

        $topicNames = [];

        foreach ($topics as $topic) {
            $topicNames[] = ($topic->getShortName()) ? $topic->getShortName() : $topic->getName();
        }

        $bars = [];

        //  single_region vs. all_regions
        $title = '';
        $series = [
            [
                'name' => 'Meine Werte',
                'data' => array_values($this->getAverageResultByTopics($topics, $scope = 'my_values')),
            ],
            [
                'name' => GeneralUtility::trimExplode('(', $this->getGroupByQuestionAnswer(), true)[0],
                'data' => array_values($this->getAverageResultByTopics($topics, $scope = 'single_region')),
            ],
            [
                'name' => 'Alle 12 Regionen',
                'data' => array_values($this->getAverageResultByTopics($topics, $scope = null)),
            ]
        ];

        $bars = $this->buildBar($topicNames, $title, $series, $bars);

        return $bars;

    }

    /**
     * @return array
     */
    public function prepareChart(): array
    {

        //  get only questions marked as benchmark
        $benchmarkQuestions = $this->surveyResult->getSurvey()->getBenchmarkQuestions();

        $benchmarkValues = [];
        $questionShortNames = [];
        foreach ($benchmarkQuestions as $question) {
            $benchmarkValues[] = $question->getBenchmarkValue();
            $questionShortNames[] = $question->getShortName();
        }

        $individualValues = [];
        //  filter to results matching a benchmark question
        foreach ($this->surveyResult->getBenchmarkQuestionResults() as $result) {
            //  cast answer to int, if question is marked as benchmark
            $individualValues[] = (int)$result->getAnswer();
        }

        //  mit 0 auffüllen, wenn bisher weniger Antworten als Labels Fragen zur Verfügung stehen
        if (($fill = count($benchmarkValues) - count($individualValues)) > 0) {
            for ($i = 1; $i === $fill; $i++) {
                $individualValues[] = 0;
            }
        }

        $chart = [
            'labels' => $questionShortNames,
            'values' => [
                'benchmark'  => $benchmarkValues,
                'individual' => $individualValues,
            ],
        ];

        return $chart;
    }

    /**
     * @param array        $chart
     * @return string
     */
    public function renderChart(array $chart): string
    {

        $labelColors = [];
        foreach ($chart['labels'] as $label) {
            $labelColors[] = '#505050';
        }

        return '
            
            var options = {
                chart: {
                    type: \'radar\'
                },
                colors: [
                    \'' . $this->colors['me'] . '\',
                    \'' . $this->colors['gem'] . '\',
                ],
                yaxis: {
                    min: 0,
                    max: 10,
                    decimalsInFloat: 0,
                },
                series: [
                    {
                        name: "Ihr Wert (0 = schwach, 10 = stark)",
                        data: ' . json_encode($chart['values']['individual']) . ',
                    },
                    {
                        name: "GEM-Expertenbefragung Deutschland (0 = schwach, 10 = stark)",
                        data: ' . json_encode($chart['values']['benchmark']) . ',
                    }
                ],
                xaxis: {
                    categories: ' . json_encode($chart['labels']) . ',
                    labels: {
                        style: {
                            colors: ' . json_encode($labelColors) . ',
                            fontSize: \'12px\'
                        }
                    }
                }
            }
            
            var chart = new ApexCharts(document.querySelector(\'#chart_' . $this->surveyResult->getUid() . '\'), options);

            chart.render(); 
                
        ';

    }

    /**
     * @param \TYPO3\CMS\Extbase\Persistence\Generic\QueryResult $questionResults
     * @param array                                              $donuts
     * @param string                                             $slug
     * @param string                                             $key
     * @param string                                             $title
     * @return array                                             $donuts
     */
    public function collectData(\TYPO3\CMS\Extbase\Persistence\Generic\QueryResult $questionResults, array $donuts, string $slug, string $key = '', string $title): array
    {
        //  group the values
        $evaluation = [
            $key   => [
                'low'     => [],
                'neutral' => [],
                'high'    => [],
            ],
        ];

        foreach ($questionResults as $result) {

            if ((int)$result->getAnswer() < 5) {
                $evaluation[$key]['low'][] = $result;
            }

            if ((int)$result->getAnswer() === 5) {
                $evaluation[$key]['neutral'][] = $result;
            }

            if ((int)$result->getAnswer() > 5) {
                $evaluation[$key]['high'][] = $result;
            }

        }

        $donuts[$slug]['data'][$key]['title'] = $title;
        $donuts[$slug]['data'][$key]['participations'] = (strlen($key) > 0) ? $questionResults->count() : '';

        $donuts[$slug]['data'][$key]['evaluation']['series'] = [
            count($evaluation[$key]['low']) ?? 0,
            count($evaluation[$key]['neutral']) ??  0,
            count($evaluation[$key]['high']) ??  0,
        ];
        $donuts[$slug]['data'][$key]['evaluation']['labels'] = [
            $this->labels['weighting']['low'],
            $this->labels['weighting']['neutral'],
            $this->labels['weighting']['high'],
        ];

        return $donuts;
    }

    public function slugify($string, $separator = '-')
    {

        $slug = strtolower($string);

        $slug = str_replace('ä', 'ae', $slug);
        $slug = str_replace('ä', 'ae', $slug);
        $slug = str_replace('ö', 'oe', $slug);
        $slug = str_replace('ü', 'ue', $slug);
        $slug = str_replace('ß', 'ss', $slug);
        $slug = str_replace('/', $separator, $slug);

        // Convert all dashes/underscores into separator
        $flip = $separator === '-' ? '_' : '-';

        $slug = preg_replace('![' . preg_quote($flip) . ']+!u', $separator, $slug);

        // Replace @ with the word 'at'
        $slug = str_replace('@', $separator . 'at' . $separator, $slug);

        // Remove all characters that are not the separator, letters, numbers, or whitespace.
        $slug = preg_replace('![^' . preg_quote($separator) . '\pL\pN\s]+!u', '', strtolower($slug));

        // Replace all separator characters and whitespace by a single separator
        $slug = preg_replace('![' . preg_quote($separator) . '\s]+!u', $separator, $slug);

        return trim($slug, $separator);
    }

    /**
     * @param array $data
     * @return float
     */
    protected function getAverage(array $data): float
    {
        return array_sum($data) / count($data);
    }

    /**
     * @param array  $topicNames
     * @param string $title
     * @param array  $series
     * @param array $bars
     * @return array
     */
    protected function buildBar(array $topicNames, string $title, array $series, array $bars): array
    {
        $slug = $this->slugify('bar-' . $title, '_');

        $bars[$slug] = [
            'topics' => $topicNames,
            'title'  => $title,
            'series' => $series
        ];

        return $bars;
    }

    /**
     * Parses strings to arrays
     *
     * @param string $data
     * @param string $delimiter
     * @param bool   $checkFloat
     * @return integer
     */
    public function parseStringToArray($data, $delimiter = '|', $checkFloat = false)
    {

        $parsedData = [];
        $strings = GeneralUtility::trimExplode($delimiter, $data, true);
        foreach ($strings as $string) {
            if ($checkFloat) {
                $parsedData[] = (float)str_replace(',', '.', $string);
            } else {
                $parsedData[] = addslashes($string);
            }
        }

        if (count($parsedData) < 1) {
            $parsedData = [];
            //===
        }

        return $parsedData;
        //===
    }

}