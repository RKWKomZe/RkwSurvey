<?php
namespace RKW\RkwSurvey\ViewHelpers;

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
use Madj2k\DrSeo\Utility\SlugUtility;
use RKW\RkwSurvey\Domain\Model\Question;
use RKW\RkwSurvey\Domain\Model\SurveyResult;
use RKW\RkwSurvey\Domain\Repository\QuestionRepository;
use RKW\RkwSurvey\Domain\Repository\QuestionResultRepository;
use RKW\RkwSurvey\Domain\Repository\SurveyResultRepository;
use TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException;
use TYPO3\CMS\Extbase\Persistence\Generic\QueryResult;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;


/**
 * Class EvaluationViewHelper
 *
 * @author Christian Dilger <c.dilger@addorange.de>
 * @copyright RKW Kompetenzzentrum
 * @package RKW_RkwSurvey
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class EvaluationViewHelper extends AbstractViewHelper
{


    /**
     * @var bool
     */
    protected $escapeOutput = false;


    /**
     * @var \RKW\RkwSurvey\Domain\Repository\SurveyResultRepository
     * @TYPO3\CMS\Extbase\Annotation\Inject
     */
    protected ?SurveyResultRepository $surveyResultRepository = null;


    /**
     * @var \RKW\RkwSurvey\Domain\Repository\QuestionResultRepository
     * @TYPO3\CMS\Extbase\Annotation\Inject
     */
    protected ?QuestionResultRepository $questionResultRepository = null;


    /**
     * @var \RKW\RkwSurvey\Domain\Model\SurveyResult|null
     */
    protected ?SurveyResult $surveyResult = null;


    /**
     * @var array
     */
    protected array $colors = [];


    /**
     * @var array
     */
    protected array $labels = [];


    /**
     * Initialize arguments.
     *
     * @return void
     * @throws \TYPO3Fluid\Fluid\Core\ViewHelper\Exception
     */
    public function initializeArguments(): void
    {
        parent::initializeArguments();

        $this->registerArgument(
            'surveyResult',
            SurveyResult::class,
            'The survey result object.',
            true
        );
    }


    /**
     * @return void
     * @throws \TYPO3\CMS\Extbase\Object\Exception
     */
    public function initialize(): void
    {
        parent::initialize();

        /** @var \RKW\RkwSurvey\Domain\Model\SurveyResult $surveyResult */
        $this->surveyResult = $this->arguments['surveyResult'];

        /** @var \TYPO3\CMS\Extbase\Object\ObjectManager $objectManager */
        $objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\ObjectManager::class);

        /** @var \RKW\RkwSurvey\Domain\Repository\SurveyResultRepository $surveyResultRepository */
        $this->surveyResultRepository = $objectManager->get(SurveyResultRepository::class);

        /** @var \RKW\RkwSurvey\Domain\Repository\QuestionResultRepository $questionResultRepository */
        $this->questionResultRepository = $objectManager->get(QuestionResultRepository::class);

        /** @var \RKW\RkwSurvey\Domain\Repository\QuestionRepository $questionRepository */
        $this->questionRepository = $objectManager->get(QuestionRepository::class);

        $this->colors = [
            '#d63f11', // $color-primary
            '#792400', // $color-blue
            '#7c7c7b', // $color-webcheck-yellow
            '#4a4a49', // $color-webcheck-green
        ];

        $this->labels = [
            'weighting' => [
                'low'     => LocalizationUtility::translate(
                    'tx_rkwsurvey_domain_model_evaluator.question.weighting.labels.low',
                    'RkwSurvey'
                ),
                'neutral' => LocalizationUtility::translate(
                    'tx_rkwsurvey_domain_model_evaluator.question.weighting.labels.neutral',
                    'RkwSurvey'
                ),
                'high'    => LocalizationUtility::translate(
                    'tx_rkwsurvey_domain_model_evaluator.question.weighting.labels.high',
                    'RkwSurvey'
                ),
            ],
        ];

    }


    /**
     * Render the survey result, if used as a benchmark
     *
     * @return string
     * @throws \TYPO3Fluid\Fluid\Core\Exception
     */
    public function render(): string
    {
        $this->templateVariableContainer->add('context', $this->prepareContext());
        $this->templateVariableContainer->add('summary', $this->prepareSummary());
        $this->templateVariableContainer->add('comparisons', $this->prepareComparisons());
        $this->templateVariableContainer->add('colors', json_encode($this->colors, JSON_THROW_ON_ERROR));

        return $this->renderChildren();
    }


    /**
     * prepareContext
     *
     * @return array
     * @throws \JsonException
     */
    protected function prepareContext(): array
    {

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
            $individualValues[] = (int) $result->getAnswer();
        }

        if ($this->surveyIsIncomplete($benchmarkValues, $individualValues)) {
            $individualValues = array_pad(
                $individualValues,
                count($benchmarkValues),
                0
            );
        }

        return [
            'series' => json_encode([
                [
                    'name' => "Ihr Wert (0 = schwach, 10 = stark)",
                    'data' => $individualValues,
                ],
                [
                    'name' => "GEM-Expertenbefragung Deutschland (0 = schwach, 10 = stark)",
                    'data' => $benchmarkValues,
                ]
            ], JSON_THROW_ON_ERROR),
            'colors' => json_encode([
                $this->colors[2],
                $this->colors[0]
            ], JSON_THROW_ON_ERROR),
            'xaxis' => json_encode([
                'categories' => $questionShortNames,
            ], JSON_THROW_ON_ERROR)
        ];
    }


    /**
     * prepareSummary
     *
     * @return array
     * @throws InvalidQueryException
     */
    protected function prepareSummary(): array
    {

        $topics = $this->surveyResult->getSurvey()->getTopics();

        $categories = [];
        foreach ($topics as $topic) {
            $categories[] = ($topic->getShortName()) ?: $topic->getName();
        }

        $series = [
            [
                'name' => 'Meine Werte',
                'data' => array_values($this->getAverageResultByTopics($topics, 'my_values')),
            ],
            [
                'name' => $this->surveyResult->getSurvey()->getName(),
                'data' => array_values($this->getAverageResultByTopics($topics, 'single_region')),
            ],
            [
                'name' => 'Alle 12 Regionen',
                'data' => array_values($this->getAverageResultByTopics($topics)),
            ],
        ];

        return [
            'colors' => json_encode($this->colors, JSON_THROW_ON_ERROR),
            'xaxis' => json_encode([
                'categories' => $categories,
            ], JSON_THROW_ON_ERROR),
            'series' => json_encode($series, JSON_THROW_ON_ERROR),
        ];

    }


    /**
     * prepareComparisons
     *
     * @return array
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     */
    protected function prepareComparisons(): array
    {
        $comparisons = [];

        $surveyQuestions = $this->surveyResult->getSurvey()->getQuestion();

        /** @var \RKW\RkwSurvey\Domain\Model\Question $question */
        foreach ($surveyQuestions as $question) {

            if ($question->getType() !== 3) {
                continue;
            }

            $slug = SlugUtility::slugify('comparisons-' . $question->getQuestion(), '_');

            $comparisons[$slug] = [
                'question' => $question->getQuestion(),
            ];

            //  single_region
            $questionResults = $this->questionResultRepository->findByQuestionAndSurveyResultUids($question, $this->getSurveyResultUids());

            $comparisons = $this->collectData(
                $questionResults,
                $comparisons,
                $slug,
                'single_region',
                $this->surveyResult->getSurvey()->getName()
            );

            if ($question->getBenchmark()) {
                $comparisons = $this->prepareBenchmark($question, $comparisons, $slug);
            } else {
                $comparisons[$slug]['data']['benchmark']['evaluation'] = null;
            }

        }

        return $comparisons;
    }


    /**
     * @param Question $question
     * @param array    $comparisons
     * @param string   $slug
     * @return array
     */
    protected function prepareBenchmark(Question $question, array $comparisons, string $slug): array
    {
        $comparisons[$slug]['data']['benchmark']['title'] = $question->getBenchmarkLabel();
        $comparisons[$slug]['data']['benchmark']['evaluation'] = [
            'colors' => json_encode($this->colors, JSON_THROW_ON_ERROR),
            'series' => json_encode($this->parseStringToArray(
                $question->getBenchmarkWeighting(),
                '|',
                true
            ), JSON_THROW_ON_ERROR),
            'labels' => json_encode([
                $this->labels['weighting']['low'],
                $this->labels['weighting']['neutral'],
                $this->labels['weighting']['high'],
            ], JSON_THROW_ON_ERROR),
        ];

        return $comparisons;
    }


    /**
     * @param QueryResult $questionResults
     * @param array                                              $comparisons
     * @param string                                             $slug
     * @param string                                             $key
     * @param string                                             $title
     * @return array $comparisons
     */
    protected function collectData(
        QueryResultInterface $questionResults,
        array                $comparisons,
        string               $slug,
        string               $key,
        string               $title
    ): array
    {
        $evaluation = [
            $key => [
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

        $comparisons[$slug]['data'][$key]['title'] = $title;
        $comparisons[$slug]['data'][$key]['participations'] = $questionResults->count();

        $comparisons[$slug]['data'][$key]['evaluation']['series'] = json_encode([
            count($evaluation[$key]['low']) ?? 0,
            count($evaluation[$key]['neutral']) ?? 0,
            count($evaluation[$key]['high']) ?? 0,
        ], JSON_THROW_ON_ERROR);

        $comparisons[$slug]['data'][$key]['evaluation']['labels'] = json_encode([
            $this->labels['weighting']['low'],
            $this->labels['weighting']['neutral'],
            $this->labels['weighting']['high'],
        ], JSON_THROW_ON_ERROR);

        $comparisons[$slug]['data'][$key]['evaluation']['colors'] = json_encode(
            $this->colors,
            JSON_THROW_ON_ERROR
        );

        return $comparisons;
    }


    /**
     *
     * @param ObjectStorage $topics
     * @param null  $scope
     * @return array
     * @throws InvalidQueryException
     */
    protected function getAverageResultByTopics($topics, $scope = null): array
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
                    } else {
                        if ($scope === 'single_region') {
                            $surveyResultUids = $this->getSurveyResultUids();
                            $questionResults = $this->questionResultRepository->findByQuestionAndSurveyResultUids($question, $surveyResultUids);
                        } else {    //  all regions
                            $surveyResults = $this->surveyResultRepository->findBySurveyAndFinished($this->surveyResult->getSurvey());
                            $surveyResultUids = [];
                            foreach ($surveyResults as $surveyResult) {
                                $surveyResultUids[] = $surveyResult->getUid();
                            }
                            $questionResults = $this->questionResultRepository->findByQuestionAndSurveyResultUids($question, $surveyResultUids);
                        }
                    }

                    $answers = [];
                    foreach ($questionResults as $questionResult) {
                        $answers[] = (int)$questionResult->getAnswer();
                    }

                    $answers = array_filter($answers, static function ($x) {
                        return $x !== '';
                    });

                    $averageOnQuestion[] = $this->getAverage($answers);

                }

                /**  @todo: $averageOnQuestion may be undefined here */
                $averageOnTopic[$topic->getUid()] = $this->getAverage($averageOnQuestion);

            }

        }

        return $averageOnTopic;
    }


    /**
     * getAverage
     *
     * @param array $data
     * @return float
     */
    protected function getAverage(array $data): float
    {
        return array_sum($data) / count($data);
    }


    /**
     * Parses strings to arrays
     *
     * @param string $data
     * @param string $delimiter
     * @param bool   $checkFloat
     * @return array
     */
    protected function parseStringToArray(
        string $data,
        string $delimiter = '|',
        bool $checkFloat = false
    ): array
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


    /**
     * getSurveyResultUidsGroupedByQuestion
     *
     * @return array
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     */
    public function getSurveyResultUids(): array
    {
        $survey = $this->surveyResult->getSurvey();
        $surveyResults = $this->surveyResultRepository->findBySurveyAndFinished($survey);

        $surveyResultUids = [];
        foreach ($surveyResults as $surveyResult) {
            $surveyResultUids[] = $surveyResult->getUid();
        }

        return $surveyResultUids;
    }


    /**
     * @param $benchmarkValues
     * @param $individualValues
     * @return bool
     */
    protected function surveyIsIncomplete($benchmarkValues, $individualValues): bool
    {
        return (count($benchmarkValues) - count($individualValues)) > 0;
    }

}
