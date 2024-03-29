<?php
namespace RKW\RkwSurvey\Domain\Model;

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

use Madj2k\CoreExtended\Utility\GeneralUtility;
use RKW\RkwSurvey\Domain\Repository\QuestionRepository;
use RKW\RkwSurvey\Domain\Repository\SurveyResultRepository;
use RKW\RkwSurvey\Domain\Repository\QuestionResultRepository;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\Generic\QueryResult;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * Evaluator
 *
 * @author Christian Dilger <c.dilger@addorange.de>
 * @copyright RKW Kompetenzzentrum
 * @package RKW_RkwSurvey
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class Evaluator
{


    /**
     * @var \RKW\RkwSurvey\Domain\Repository\SurveyResultRepository
     * @TYPO3\CMS\Extbase\Annotation\Inject
     */
    protected SurveyResultRepository $surveyResultRepository;


    /**
     * @var \RKW\RkwSurvey\Domain\Repository\QuestionResultRepository
     * @TYPO3\CMS\Extbase\Annotation\Inject
     */
    protected QuestionResultRepository $questionResultRepository;


    /**
     * @var \RKW\RkwSurvey\Domain\Repository\QuestionRepository
     * @TYPO3\CMS\Extbase\Annotation\Inject
     */
    protected QuestionRepository $questionRepository;


    /**
     * @var array
     */
    protected array $colors = [];


    /**
     * @var array
     */
    protected array $labels = [];


    /**
     * @var \RKW\RkwSurvey\Domain\Model\SurveyResult|null
     */
    protected ?SurveyResult $surveyResult = null;


    /**
     * @var \RKW\RkwSurvey\Domain\Model\Question|null
     */
    protected ?Question $groupByQuestion = null;


    /**
     * Constructor
     *
     * @param SurveyResult $surveyResult
     *  @todo class should only be used via objectManager. Thus manually loading of repositories should not be needed.
     */
    public function __construct(SurveyResult $surveyResult)
    {
        $this->surveyResult = $surveyResult;
        $this->colors = [
            'me' => '#d63f11', // $color-primary
            'my-region' => '#792400', //    #009fee' - $color-blue
            'all-regions' => '#7c7c7b', // #fdc500' - $color-webcheck-yellow
            'gem' => '#4a4a49', // #94c119' - $color-webcheck-green
        ];

        $this->labels = [
            'weighting' => [
                'low' => LocalizationUtility::translate('tx_rkwsurvey_domain_model_evaluator.question.weighting.labels.low', 'RkwSurvey'),
                'neutral' => LocalizationUtility::translate('tx_rkwsurvey_domain_model_evaluator.question.weighting.labels.neutral', 'RkwSurvey'),
                'high' => LocalizationUtility::translate('tx_rkwsurvey_domain_model_evaluator.question.weighting.labels.high', 'RkwSurvey'),
            ],
        ];

        /** @var \TYPO3\CMS\Extbase\Object\ObjectManager $objectManager */
        $objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(ObjectManager::class);

        if (!$this->surveyResultRepository) {
            $this->surveyResultRepository = $objectManager->get(SurveyResultRepository::class);
            trigger_error(__CLASS__ . ': Please use the ObjectManager to load this class.', E_USER_DEPRECATED);
        }

        if (!$this->questionResultRepository) {
            $this->questionResultRepository = $objectManager->get(QuestionResultRepository::class);
            trigger_error(__CLASS__ . ': Please use the ObjectManager to load this class.', E_USER_DEPRECATED);
        }

        if (!$this->questionRepository) {
            $this->questionRepository = $objectManager->get(QuestionRepository::class);
            trigger_error(__CLASS__ . ': Please use the ObjectManager to load this class.', E_USER_DEPRECATED);
        }

        $this->setGroupedByQuestion();
    }


    /**
     * setGroupedByQuestion
     *
     * @return void
     *  @todo this is method should not be part of a model
     */
    protected function setGroupedByQuestion(): void
    {
        $survey = $this->surveyResult->getSurvey();
        $this->groupByQuestion = $this->questionRepository->findOneByGroupedByAndSurvey($survey);
    }


    /**
     * containsGroupedByQuestion
     *
     * @return bool
     *  @todo this is method should not be part of a model
     */
    public function containsGroupedByQuestion(): bool
    {
        return (bool) $this->groupByQuestion;
    }


    /**
     * getSurveyResultUidsGroupedByQuestion
     *
     * @return array
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     *  @todo this is method should not be part of a model
     */
    public function getSurveyResultUidsGroupedByQuestion(): array
    {
        $survey = $this->surveyResult->getSurvey();
        if ($this->groupByQuestion) {
            $myGroupByQuestionResult = $this->questionResultRepository->findByQuestionAndSurveyResult(
                $this->groupByQuestion,
                $this->surveyResult
            );

            $surveyResults = $this->surveyResultRepository->findBySurveyAndQuestionAndAnswerAndFinished(
                $survey,
                $this->groupByQuestion,
                $myGroupByQuestionResult->getAnswer(),
                $finished = 1
            );

        } else {
            $surveyResults = $this->surveyResultRepository->findBySurveyAndFinished($survey);
        }

        $surveyResultUids = [];
        foreach ($surveyResults as $surveyResult) {
            $surveyResultUids[] = $surveyResult->getUid();
        }

        return $surveyResultUids;
    }


    /**
     * getGroupByQuestionAnswer
     *
     * @return mixed|null
     *  @todo mixed return type is evil
     *  @todo this is method should not be part of a model
     */
    public function getGroupByQuestionAnswer()
    {

        if ($this->groupByQuestion) {

            $result = $this->questionResultRepository->findByQuestionAndSurveyResult(
                $this->groupByQuestion,
                $this->surveyResult
            );

            /**  @todo this is barely readable */
            return $this->parseStringToArray(
                $result->getQuestion()->getAnswerOption(),
                PHP_EOL
            )[((int) $result->getAnswer() - 1)];

        }
        return null;

    }


    /**
     * @return mixed|string
     *  @todo mixed return type is evil; Gründungsökosystem Braunschweig - seriously?
     *  @todo this is method should not be part of a model
     */
    public function getTitleByGroupByQuestionAnswer()
    {
        return ($this->getGroupByQuestionAnswer())
            ? GeneralUtility::trimExplode('(', $this->getGroupByQuestionAnswer(), true)[0]
            : 'Gründungsökosystem Braunschweig';
    }


    /**
     *
     * @param $topics
     * @param null         $scope
     * @return array
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     *  @todo parameter-type not defined. What is topics? QueryResultInterface? ObjectStorage?
     *  @todo this is method should not be part of a model. Maybe a ViewHelper!
     */
    public function getAverageResultByTopics($topics, $scope = null): array
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
                    $answers = array_filter($answers, static function ($x) {
                        return $x !== '';
                    });

                    $averageOnQuestion[] = $this->getAverage($answers);

                }

                /**  @todo: $averageOnQuestion may be undefined here  */
                $averageOnTopic[$topic->getUid()] = $this->getAverage($averageOnQuestion);

            }

        }

        return $averageOnTopic;
    }

    /**
     * renderBars
     *
     * @param array $charts
     * @return string
     *  @todo this is method should not be part of a model. Maybe a ViewHelper!
     *  @todo do not mix frontend-stuff with PHP. This is no WordPress ;-) Use a partial/template. Maybe with StandaloneView
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
     * prepareDonuts
     *
     * @return array
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     *  @todo this is method should not be part of a model.  Maybe a ViewHelper!
     */
    public function prepareDonuts(): array
    {
        $donuts = [];

        $surveyQuestions = $this->surveyResult->getSurvey()->getQuestion();

        /** @var Question $question */
        foreach ($surveyQuestions as $question) {

            //  use question only if it is of type scale
            if ($question->getType() !== 3) {
                continue;
            }

            $slug = GeneralUtility::slugify('donuts-' . $question->getQuestion(), '_');

            $donuts[$slug] = [
                'question' => $question->getQuestion(),
            ];

            $surveyResultUids = $this->getSurveyResultUidsGroupedByQuestion();

            //  single_region
            $questionResults = $this->questionResultRepository->findByQuestionAndSurveyResultUids($question, $surveyResultUids);

            $donuts = $this->collectData($questionResults, $donuts, $slug, $key = 'single_region', $title = $this->getTitleByGroupByQuestionAnswer());

//            //    Ostdeutschland = all regions
//            $questionResults = $this->questionResultRepository->findByQuestion($question);
//            $donuts = $this->collectData($questionResults, $donuts, $slug, $key = 'all_regions', $title = 'Alle 12 Regionen');

            /**  @todo no hard-coded texts in PHP! */
            //  Deutschland = GEM
            $donuts[$slug]['data']['benchmark']['title'] = 'GEM-Expertenbefragung Deutschland';

            if ($question->getBenchmark()) {

                //  get benchmark from question = GEM
                /**  @todo line too long */
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
     * renderDonuts
     *
     * @param array $charts
     * @return string
     *  @todo this is method should not be part of a model. Maybe a ViewHelper!
     *  @todo do not mix frontend-stuff with PHP. This is no WordPress ;-) Use a partial/template. Maybe with StandaloneView
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
     * prepareBars
     *
     * @return array
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     *  @todo this is method should not be part of a model. Maybe a ViewHelper!
     */
    public function prepareBars(): array
    {

        /**  @todo no german comments please  */
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
                'name' => $this->getTitleByGroupByQuestionAnswer(),
                'data' => array_values($this->getAverageResultByTopics($topics, $scope = 'single_region')),
            ],
            [
                'name' => 'Alle 12 Regionen',
                'data' => array_values($this->getAverageResultByTopics($topics, $scope = null)),
            ],
        ];

        return $this->buildBar($topicNames, $title, $series, $bars);

    }


    /**
     * prepareChart
     *
     * @return array
     *  @todo this is method should not be part of a model. Maybe a ViewHelper!
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

        /**  @todo no german comments please  */
        //  mit 0 auffüllen, wenn bisher weniger Antworten als Labels Fragen zur Verfügung stehen
        if (($fill = count($benchmarkValues) - count($individualValues)) > 0) {
            for ($i = 1; $i === $fill; $i++) {
                $individualValues[] = 0;
            }
        }

        return [
            'labels' => $questionShortNames,
            'values' => [
                'benchmark'  => $benchmarkValues,
                'individual' => $individualValues,
            ],
        ];
    }


    /**
     * renderChart
     *
     * @param array $chart
     * @return string
     *  @todo do not mix frontend-stuff with PHP. This is no WordPress ;-) Use a partial/template. Maybe with StandaloneView
     *  @todo this is method should not be part of a model. Maybe a ViewHelper!
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
                    decimalsInFloat: 0,
                    min: 0,
                    max: 10,
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
     * @param array $donuts
     * @param string $slug
     * @param string $key
     * @param string $title
     * @return array $donuts
     *  @todo this is method should not be part of a model. Maybe a ViewHelper!
     */
    public function collectData(
        QueryResultInterface $questionResults,
        array $donuts,
        string $slug,
        string $key,
        string $title
    ): array  {
        //  group the values
        $evaluation = [
            $key   => [
                'low'     => [],
                'neutral' => [],
                'high'    => [],
            ],
        ];

        foreach ($questionResults as $result) {

            $answer = (int)$result->getAnswer();

            if ($answer < 5) {
                $evaluation[$key]['low'][] = $result;
            }

            if ($answer === 5) {
                $evaluation[$key]['neutral'][] = $result;
            }

            if ($answer > 5) {
                $evaluation[$key]['high'][] = $result;
            }

        }

        $donuts[$slug]['data'][$key]['title'] = $title;
        $donuts[$slug]['data'][$key]['participations'] = $questionResults->count();

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


    /**
     * getAverage
     *
     * @param array $data
     * @return float
     *  @todo this is method should not be part of a model. Maybe a ViewHelper!
     */
    protected function getAverage(array $data): float
    {
        return array_sum($data) / count($data);
    }


    /**
     * buildBar
     *
     * @param array  $topicNames
     * @param string $title
     * @param array  $series
     * @param array $bars
     * @return array
     *   @todo this is method should not be part of a model. Maybe a ViewHelper!
     */
    protected function buildBar(array $topicNames, string $title, array $series, array $bars): array
    {
        $slug = GeneralUtility::slugify('bar-' . $title, '_');

        $bars[$slug] = [
            'topics' => $topicNames,
            'title'  => $title,
            'series' => $series,
        ];

        return $bars;
    }


    /**
     * Parses strings to arrays
     *
     * @param string $data
     * @param string $delimiter
     * @param bool   $checkFloat
     * @return array
     *  @todo this is method should not be part of a model. Maybe a ViewHelper!
     */
    public function parseStringToArray(string $data, string $delimiter = '|', bool $checkFloat = false): array
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
        }

        return $parsedData;
    }

}
