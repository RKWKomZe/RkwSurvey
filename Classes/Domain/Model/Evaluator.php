<?php

namespace RKW\RkwSurvey\Domain\Model;

use TYPO3\CMS\Core\Utility\GeneralUtility;
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
 * Question
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
     * questionResultRepository
     *
     * @var \RKW\RkwSurvey\Domain\Repository\QuestionResultRepository
     * @inject
     */
    protected $questionResultRepository;

    public function __construct()
    {
        if (!$this->questionResultRepository) {
            $objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
            $this->questionResultRepository = $objectManager->get(QuestionResultRepository::class);
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
    public function getQuestionResultsWithSameFirstAnswer(): array
    {
        $myFirstQuestion = $this->surveyResult->getQuestionResult()->current();    //  @todo: How could it be that this does not work?
        $myFirstQuestion = $this->surveyResult->getQuestionResult()->toArray()[0];    //  @todo: How to identify grouping by as it does not have to be always the first question?

        $allQuestionResultsByQuestion = $this->questionResultRepository->findByQuestionAndAnswer($myFirstQuestion->getQuestion(), $myFirstQuestion->getAnswer());

        $surveyResultUids = [];

        foreach ($allQuestionResultsByQuestion as $questionResult) {
            $surveyResultUids[] = $questionResult->getSurveyResult()->getUid();
        }

        return array($myFirstQuestion, $surveyResultUids);
    }

    /**
     * @param              $topics
     * @param null         $scope
     * @return array
     */
    public function getAverageResultByTopics($topics, $scope = null)
    {

        foreach ($topics as $topic) {

            $average = [];

            if ($topic->getQuestions()->count() > 0) {

                foreach ($topic->getQuestions() as $question) {

                    if ($scope) {
                        //  filter questionResults to my region only
                        //  this must be dynamic by a groupBy attribute or similar
                        list($myFirstQuestion, $surveyResultUids) = $this->getQuestionResultsWithSameFirstAnswer();
                        $questionResults = $this->questionResultRepository->findByQuestionAndSurveyResultUids($question, $surveyResultUids);
                    } else {
                        $questionResults = $this->questionResultRepository->findByQuestion($question);
                    }

                    $answers = [];
                    foreach ($questionResults as $questionResult) {
                        $answers[] = (int)$questionResult->getAnswer();
                    }

                    //  Was ist der Wert von "weiß ich nicht"?
                    $answers = array_filter($answers, function ($x) {
                        return $x !== '';
                    });

                    //  average on question
                    $average[] = array_sum($answers) / count($answers);

                }

                //  average on topic
                $results[] = array_sum($average) / count($average);

            }

        }

        return $results;
    }

    /**
     * @param array        $charts
     * @return string
     */
    public function renderBars(array $charts): string
    {

        $script = '';

        foreach ($charts as $chartIdentifier => $comparison) {

            $identifier = $chartIdentifier;

            $script .= '
                
                var options_' . $identifier . ' = {
                    chart: {
                        type: \'bar\'
                    },
                    series: ' . json_encode($comparison['series']) . ',
                    plotOptions: {
                        bar: {
                            horizontal: false
                        }
                    },
                    legend: {
                        show: false
                    },
                    dataLabels: {
                        enabled: false
                    },
                    tooltip: {
                        enabled: false,
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
    public function prepareDonuts(): array // @todo: Make this dynamic somehow
    {

        $donuts = [];

        $surveyQuestions = $this->surveyResult->getSurvey()->getQuestion();

        foreach ($surveyQuestions as $question) {

            //  use question only if it is a scale
            if ($question->getType() !== 3) {
                continue;
            }

            $slug = $this->slugify($question->getQuestion(), '_');

            $donuts[$slug] = [
                'question' => $question->getQuestion(),
            ];

            //  group the values
            $evaluation = [
                'my-region'   => [
                    'low'     => [],
                    'neutral' => [],
                    'high'    => [],
                ],
                'all-regions' => [
                    'low'     => [],
                    'neutral' => [],
                    'high'    => [],
                ],
            ];

            list($myFirstQuestion, $surveyResultUids) = $this->getQuestionResultsWithSameFirstAnswer($this->surveyResult);
            //  my-region
            $questionResults = $this->questionResultRepository->findByQuestionAndSurveyResultUids($question, $surveyResultUids);
            $donuts = $this->collectData($questionResults, $evaluation, $myFirstQuestion, $donuts, $slug, $key = 'my_region', $title = 'Meine Region');

            //  Ostdeutschland = all regions
            $questionResults = $this->questionResultRepository->findByQuestion($question);
            $donuts = $this->collectData($questionResults, $evaluation, $myFirstQuestion, $donuts, $slug, $key = 'all_regions', $title = 'Alle Regionen');

            //  Deutschland = GEM
            $donuts[$slug]['data']['benchmark']['region'] = 'Bundesweit (GEM)';

            //  get benchmark from question = GEM
            //  @todo: Need all three values per question (low, neutral, high) from GEM - must be put to question
            $donuts[$slug]['data']['benchmark']['evaluation']['series'] = [rand(0, 100), rand(0, 100), rand(0, 100)];
            $donuts[$slug]['data']['benchmark']['evaluation']['labels'] = ['low', 'neutral', 'high'];

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

                $identifier = $chartIdentifier . '_' . $comparisonIdentifier;

                $script .= '
                    
                    var options_' . $identifier . ' = {
                        chart: {
                            type: \'donut\'
                        },
                        series: ' . json_encode($comparison['evaluation']['series']) . ',
                        labels: ' . json_encode($comparison['evaluation']['labels']) . ',
                        plotOptions: {
                            pie: {
                                donut: {
                                    labels: {
                                        show: true
                                    }
                                }
                            }
                        },
                        legend: {
                            show: false
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

        return $script;

    }

    /**
     * @return array
     */
    public function prepareBars(): array // @todo: Make this dynamic somehow
    {

        //  get the topics -> muss dynamisch über Zuordnung im Fragebogen zu Themen erfolgen
        $topics = $this->surveyResult->getSurvey()->getTopics();

        $topicNames = [
            'P/I',
            'T',
            'F',
        ];

        $bars = [];

        //  my-region vs. all-regions
        //  extract name from topics
        $title = 'Meine Region/Alle Regionen';
        $slug = $this->slugify($title, '_');
        $bars[$slug]['topics'] = $topicNames;
        $bars[$slug]['title'] = $title;
        $bars[$slug]['series'] = [
            [
                'name' => 'Meine Region',
                'data' => $this->getAverageResultByTopics($topics, $scope = 'my-region'),
            ],
            [
                'name' => 'Alle Regionen',
                'data' => $this->getAverageResultByTopics($topics, $scope = null),
            ],
        ];

        //  my-region vs. gem -> muss die benchmarks holen
        $title = 'Meine Region/GEM';
        $slug = $this->slugify($title, '_');

        $bars[$slug]['topics'] = $topicNames;
        $bars[$slug]['title'] = $title;
        $bars[$slug]['series'] = [
            [
                'name' => 'Meine Region',
                'data' => $this->getAverageResultByTopics($topics, $scope = 'my-region'),
            ],
            [
                'name' => 'GEM',
                'data' => [9, 6, 3],
            ],
        ];

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
            //  cast answer to int, if question is of marked as benchmark
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

        return '
            
            var options = {
                chart: {
                    type: \'radar\'
                },
                series: [
                    {
                        name: "Ihr Wert",
                        data: ' . json_encode($chart['values']['individual']) . ',
                    },
                    {
                        name: "GEM",
                        data: ' . json_encode($chart['values']['benchmark']) . ',
                    },
                    {
                        data: [0, 0, 11, 11],
                    },
                    {
                        data: [11, 0, 0, 11],
                    },
                    {
                        data: [11, 11, 0, 0],
                    },
                    {
                        data: [0, 11, 11, 0],
                    }
                ],
                labels: ' . json_encode($chart['labels']) . '
            }
            
            var chart = new ApexCharts(document.querySelector(\'#chart_' . $this->surveyResult->getUid() . '\'), options);

            chart.render(); 
                
        ';

    }

    /**
     * @param \TYPO3\CMS\Extbase\Persistence\Generic\QueryResult $questionResults
     * @param array                                              $evaluation
     * @param                                                    $myFirstQuestion
     * @param array                                              $donuts
     * @param string                                             $slug
     * @param key                                                $key
     * @param title                                              $title
     * @return array              $donuts
     */
    public function collectData(\TYPO3\CMS\Extbase\Persistence\Generic\QueryResult $questionResults, array $evaluation, $myFirstQuestion, array $donuts, string $slug, string $key, string $title): array
    {
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

        //  show results for each question
        //  meine Region, Ostdeutschland, Deutschland

        //  my-region
        $myFirstQuestionAnswerOptions = GeneralUtility::trimExplode(PHP_EOL, $myFirstQuestion->getQuestion()->getAnswerOption(), true);
        $donuts[$slug]['data'][$key]['region'] = $myFirstQuestionAnswerOptions[((int)$myFirstQuestion->getAnswer() - 1)];
        $donuts[$slug]['data'][$key]['region'] = $title;

        $donuts[$slug]['data'][$key]['evaluation']['low'] = (isset($evaluation[$key]['low'])) ? count($evaluation[$key]['low']) : 0;
        $donuts[$slug]['data'][$key]['evaluation']['neutral'] = (isset($evaluation[$key]['neutral'])) ? count($evaluation[$key]['neutral']) : 0;
        $donuts[$slug]['data'][$key]['evaluation']['high'] = (isset($evaluation[$key]['high'])) ? count($evaluation[$key]['high']) : 0;

        //  @todo: improve this whole aggregation process
        $donuts[$slug]['data'][$key]['evaluation']['series'] = [$donuts[$slug]['data'][$key]['evaluation']['low'], $donuts[$slug]['data'][$key]['evaluation']['neutral'], $donuts[$slug]['data'][$key]['evaluation']['high']];
        $donuts[$slug]['data'][$key]['evaluation']['labels'] = ['low', 'neutral', 'high'];

        unset($donuts[$slug]['data'][$key]['evaluation']['low']);
        unset($donuts[$slug]['data'][$key]['evaluation']['neutral']);
        unset($donuts[$slug]['data'][$key]['evaluation']['high']);

        return $donuts;
    }

    public function slugify($string, $separator = '-')
    {

        $slug = strtolower($string);

        $slug = str_replace('ä', 'ae', $slug);
        $slug = str_replace('ä', 'ae', $slug);
        $slug = str_replace('ö', 'oe', $slug);
        $slug = str_replace('ü', 'ue', $slug);
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

}