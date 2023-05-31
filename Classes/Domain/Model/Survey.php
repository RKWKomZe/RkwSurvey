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

/**
 * Survey
 *
 * @author Maximilian Fäßler <maximilian@faesslerweb.de>
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Rkw Kompetenzzentrum
 * @package RKW_RkwSurvey
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class Survey extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    /**
     * name
     *
     * @var string
     */
    protected $name = '';

    /**
     * starttext
     *
     * @var string
     */
    protected $starttext = '';

    /**
     * endtext
     *
     * @var string
     */
    protected $endtext = '';

    /**
     * starttime
     *
     * @var integer
     */
    protected $starttime = '';

    /**
     * endtime
     *
     * @var integer
     */
    protected $endtime = '';

    /**
     * accessRestricted
     *
     * @var boolean
     */
    protected $accessRestricted = '';

    /**
     * topics
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\RKW\RkwSurvey\Domain\Model\Topic>
     */
    protected $topics;

    /**
     * question
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\RKW\RkwSurvey\Domain\Model\Question>
     */
    protected $question = null;

    /**
     * questionContainer
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\RKW\RkwSurvey\Domain\Model\QuestionContainer>
     */
    protected $questionContainer = null;

    /**
     * admin
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\RKW\RkwSurvey\Domain\Model\BackendUser>
     */
    protected $admin = null;

    /**
     * token
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\RKW\RkwSurvey\Domain\Model\Token>
     */
    protected $token = null;

    /**
     * type
     *
     * @var integer
     */
    protected $type = 0;

    /**
     * __construct
     */
    public function __construct()
    {
        //Do not remove the next line: It would break the functionality
        $this->initStorageObjects();
    }

    /**
     * Initializes all ObjectStorage properties
     * Do not modify this method!
     * It will be rewritten on each save in the extension builder
     * You may modify the constructor of this class instead
     *
     * @return void
     */
    protected function initStorageObjects()
    {
        $this->topics = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $this->question = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $this->questionContainer = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $this->admin = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $this->token = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
    }

    /**
     * Returns the name
     *
     * @return string $name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the name
     *
     * @param string $name
     * @return void
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Returns the type
     *
     * @return int $type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Sets the type
     *
     * @param int $type
     * @return void
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * Returns the starttext
     *
     * @return string $starttext
     */
    public function getStarttext()
    {
        return $this->starttext;
    }

    /**
     * Sets the starttext
     *
     * @param string $starttext
     * @return void
     */
    public function setStarttext($starttext)
    {
        $this->starttext = $starttext;
    }

    /**
     * Returns the endtext
     *
     * @return string $endtext
     */
    public function getEndtext()
    {
        return $this->endtext;
    }

    /**
     * Sets the endtext
     *
     * @param string $endtext
     * @return void
     */
    public function setEndtext($endtext)
    {
        $this->endtext = $endtext;
    }

    /**
     * Returns the starttime
     *
     * @return integer $starttime
     */
    public function getStarttime()
    {
        return $this->starttime;
    }

    /**
     * Sets the starttime
     *
     * @param integer $starttime
     * @return void
     */
    public function setStarttime($starttime)
    {
        $this->starttime = $starttime;
    }

    /**
     * Returns the endtime
     *
     * @return integer $endtime
     */
    public function getEndtime()
    {
        return $this->endtime;
    }

    /**
     * Sets the endtime
     *
     * @param integer $endtime
     * @return void
     */
    public function setEndtime($endtime)
    {
        $this->endtime = $endtime;
    }

    /**
     * Returns the accessRestricted
     *
     * @return boolean $accessRestricted
     */
    public function getAccessRestricted()
    {
        return $this->accessRestricted;
    }

    /**
     * Sets the accessRestricted
     *
     * @param boolean $accessRestricted
     * @return void
     */
    public function setAccessRestricted($accessRestricted)
    {
        $this->accessRestricted = $accessRestricted;
    }

    /**
     * Check the access restriction
     *
     * @return boolean $accessRestricted
     */
    public function isAccessRestricted()
    {
        return $this->accessRestricted;
    }

    /**
     * Returns the topics
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\RKW\RkwSurvey\Domain\Model\Topic> $topics
     */
    public function getTopics()
    {
        return $this->topics;
    }

    /**
     * Sets the topics
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\RKW\RkwSurvey\Domain\Model\Topic> $topics
     * @return void
     */
    public function setTopics($topics)
    {
        $this->topics = $topics;
    }

    /**
     * Adds a Question
     *
     * @param \RKW\RkwSurvey\Domain\Model\Question $question
     * @return void
     */
    public function addQuestion(\RKW\RkwSurvey\Domain\Model\Question $question)
    {
        $this->question->attach($question);
    }

    /**
     * Removes a Question
     *
     * @param \RKW\RkwSurvey\Domain\Model\Question $questionToRemove The Question to be removed
     * @return void
     */
    public function removeQuestion(\RKW\RkwSurvey\Domain\Model\Question $questionToRemove)
    {
        $this->question->detach($questionToRemove);
    }

    /**
     * Returns the question
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\RKW\RkwSurvey\Domain\Model\Question> $question
     */
    public function getQuestion()
    {
        return $this->question;
    }

    /**
     * Sets the question
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\RKW\RkwSurvey\Domain\Model\Question> $question
     * @return void
     */
    public function setQuestion(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $question)
    {
        $this->question = $question;
    }

    /**
     * Adds a QuestionContainer
     *
     * @param \RKW\RkwSurvey\Domain\Model\QuestionContainer $questionContainer
     * @return void
     */
    public function addQuestionContainer(\RKW\RkwSurvey\Domain\Model\QuestionContainer $questionContainer)
    {
        $this->questionContainer->attach($questionContainer);
    }

    /**
     * Removes a QuestionContainer
     *
     * @param \RKW\RkwSurvey\Domain\Model\QuestionContainer $questionContainerToRemove The QuestionContainer to be removed
     * @return void
     */
    public function removeQuestionContainer(\RKW\RkwSurvey\Domain\Model\QuestionContainer $questionContainerToRemove)
    {
        $this->question->detach($questionContainerToRemove);
    }

    /**
     * Returns the questionContainer
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\RKW\RkwSurvey\Domain\Model\QuestionContainer> $questionContainer
     */
    public function getQuestionContainer()
    {
        return $this->questionContainer;
    }

    /**
     * Sets the questionContainer
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\RKW\RkwSurvey\Domain\Model\QuestionContainer> $questionContainer
     * @return void
     */
    public function setQuestionContainer(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $questionContainer)
    {
        $this->questionContainer = $questionContainer;
    }

    /**
     * Adds a BackendUser
     *
     * @param \RKW\RkwSurvey\Domain\Model\BackendUser $admin
     * @return void
     */
    public function addAdmin($admin)
    {
        $this->admin->attach($admin);
    }

    /**
     * Removes a BackendUser
     *
     * @param \RKW\RkwSurvey\Domain\Model\BackendUser $adminToRemove
     * @return void
     */
    public function removeAdmin($adminToRemove)
    {
        $this->admin->detach($adminToRemove);
    }

    /**
     * Returns the admin
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\RKW\RkwSurvey\Domain\Model\BackendUser> admin
     */
    public function getAdmin()
    {
        return $this->admin;
    }

    /**
     * Sets the admin
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\RKW\RkwSurvey\Domain\Model\BackendUser> $admin
     * @return void
     */
    public function setAdmin(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $admin)
    {
        $this->admin = $admin;
    }

    /**
     * Adds a token
     *
     * @param \RKW\RkwSurvey\Domain\Model\Token $token
     * @return void
     */
    public function addToken(\RKW\RkwSurvey\Domain\Model\Token $token)
    {
        $this->token->attach($token);
    }

    /**
     * Removes a Token
     *
     * @param \RKW\RkwSurvey\Domain\Model\Token $tokenToRemove The Token to be removed
     * @return void
     */
    public function removeToken(\RKW\RkwSurvey\Domain\Model\Token $tokenToRemove)
    {
        $this->token->detach($tokenToRemove);
    }

    /**
     * Returns the token
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\RKW\RkwSurvey\Domain\Model\Token> $token
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Sets the token
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\RKW\RkwSurvey\Domain\Model\Token> $token
     * @return void
     */
    public function setToken(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $token)
    {
        $this->token = $token;
    }

    /**
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage
     */
    public function getBenchmarkQuestions()
    {
        $benchmarkQuestions = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        foreach ($this->getQuestion() as $question) {
            if ($question->getBenchmark()) {
                $benchmarkQuestions->attach($question);
            }
        }

        return $benchmarkQuestions;
    }


    /**
     * Service function. Get question count
     * @return int
     */
    public function getQuestionCountTotal()
    {
        if ($this->type == 2) {
            $questionTotalCount = 0;
            /** @var Question $container */
            foreach ($this->getQuestionContainer() as $container) {
                $questionTotalCount += $container->getQuestion()->count();
            }
            return $questionTotalCount;
        } else {
            return $this->getQuestion()->count();
        }
    }

}
