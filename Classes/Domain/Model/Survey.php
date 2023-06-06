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

use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * Survey
 *
 * @author Maximilian Fäßler <maximilian@faesslerweb.de>
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright RKW Kompetenzzentrum
 * @package RKW_RkwSurvey
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class Survey extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    /**
     * @var string
     */
    protected string $name = '';


    /**
     * @var string
     */
    protected string $starttext = '';


    /**
     * @var string
     */
    protected string $endtext = '';


    /**
     * @var int
     */
    protected int $starttime = 0;


    /**
     * @var int
     */
    protected int $endtime = 0;


    /**
     * @var bool
     */
    protected bool $accessRestricted = false;


    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\RKW\RkwSurvey\Domain\Model\Topic>|null
     */
    protected ?ObjectStorage $topics = null;


    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\RKW\RkwSurvey\Domain\Model\Question>|null
     */
    protected ?ObjectStorage $question = null;

    
    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\RKW\RkwSurvey\Domain\Model\QuestionContainer>|null
     */
    protected ?ObjectStorage $questionContainer = null;


    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\RKW\RkwSurvey\Domain\Model\BackendUser>|null
     */
    protected ?ObjectStorage $admin = null;


    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\RKW\RkwSurvey\Domain\Model\Token>|null
     */
    protected?ObjectStorage  $token = null;


    /**
     * @var int
     */
    protected int $type = 0;


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
    protected function initStorageObjects(): void
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
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }


    /**
     * Sets the name
     *
     * @param string $name
     * @return void
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }


    /**
     * Returns the type
     *
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }


    /**
     * Sets the type
     *
     * @param int $type
     * @return void
     */
    public function setType(int $type): void
    {
        $this->type = $type;
    }


    /**
     * Returns the starttext
     *
     * @return string
     */
    public function getStarttext(): string
    {
        return $this->starttext;
    }


    /**
     * Sets the starttext
     *
     * @param string $starttext
     * @return void
     */
    public function setStarttext(string $starttext): void
    {
        $this->starttext = $starttext;
    }


    /**
     * Returns the endtext
     *
     * @return string
     */
    public function getEndtext(): string
    {
        return $this->endtext;
    }


    /**
     * Sets the endtext
     *
     * @param string $endtext
     * @return void
     */
    public function setEndtext(string $endtext): void
    {
        $this->endtext = $endtext;
    }


    /**
     * Returns the starttime
     *
     * @return int $starttime
     */
    public function getStarttime(): int
    {
        return $this->starttime;
    }


    /**
     * Sets the starttime
     *
     * @param int $starttime
     * @return void
     */
    public function setStarttime(int $starttime): void
    {
        $this->starttime = $starttime;
    }


    /**
     * Returns the endtime
     *
     * @return int
     */
    public function getEndtime(): int
    {
        return $this->endtime;
    }


    /**
     * Sets the endtime
     *
     * @param int $endtime
     * @return void
     */
    public function setEndtime(int $endtime): void
    {
        $this->endtime = $endtime;
    }


    /**
     * Returns the accessRestricted
     *
     * @return bool
     */
    public function getAccessRestricted(): bool
    {
        return $this->accessRestricted;
    }


    /**
     * Sets the accessRestricted
     *
     * @param bool $accessRestricted
     * @return void
     */
    public function setAccessRestricted(bool $accessRestricted): void
    {
        $this->accessRestricted = $accessRestricted;
    }


    /**
     * Check the access restriction
     *
     * @return bool
     */
    public function isAccessRestricted(): bool
    {
        return $this->accessRestricted;
    }


    /**
     * Returns the topics
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\RKW\RkwSurvey\Domain\Model\Topic>
     */
    public function getTopics(): ObjectStorage
    {
        return $this->topics;
    }


    /**
     * Sets the topics
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\RKW\RkwSurvey\Domain\Model\Topic> $topics
     * @return void
     */
    public function setTopics(ObjectStorage $topics): void
    {
        $this->topics = $topics;
    }


    /**
     * Adds a Question
     *
     * @param \RKW\RkwSurvey\Domain\Model\Question $question
     * @return void
     */
    public function addQuestion(Question $question): void
    {
        $this->question->attach($question);
    }


    /**
     * Removes a Question
     *
     * @param \RKW\RkwSurvey\Domain\Model\Question $questionToRemove The Question to be removed
     * @return void
     */
    public function removeQuestion(Question $questionToRemove): void
    {
        $this->question->detach($questionToRemove);
    }


    /**
     * Returns the question
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\RKW\RkwSurvey\Domain\Model\Question>
     */
    public function getQuestion(): ObjectStorage
    {
        return $this->question;
    }


    /**
     * Sets the question
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\RKW\RkwSurvey\Domain\Model\Question> $question
     * @return void
     */
    public function setQuestion(ObjectStorage $question): void
    {
        $this->question = $question;
    }

    
    /**
     * Adds a QuestionContainer
     *
     * @param \RKW\RkwSurvey\Domain\Model\QuestionContainer $questionContainer
     * @return void
     */
    public function addQuestionContainer(QuestionContainer $questionContainer): void
    {
        $this->questionContainer->attach($questionContainer);
    }

    
    /**
     * Removes a QuestionContainer
     *
     * @param \RKW\RkwSurvey\Domain\Model\QuestionContainer $questionContainerToRemove The QuestionContainer to be removed
     * @return void
     */
    public function removeQuestionContainer(QuestionContainer $questionContainerToRemove): void
    {
        $this->question->detach($questionContainerToRemove);
    }
    

    /**
     * Returns the questionContainer
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\RKW\RkwSurvey\Domain\Model\QuestionContainer> $questionContainer
     */
    public function getQuestionContainer(): void
    {
        return $this->questionContainer;
    }

    
    /**
     * Sets the questionContainer
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\RKW\RkwSurvey\Domain\Model\QuestionContainer> $questionContainer
     * @return void
     */
    public function setQuestionContainer(ObjectStorage $questionContainer): void
    {
        $this->questionContainer = $questionContainer;
    }
    

    /**
     * Adds a BackendUser
     *
     * @param \RKW\RkwSurvey\Domain\Model\BackendUser $admin
     * @return void
     */
    public function addAdmin(BackendUser $admin): void
    {
        $this->admin->attach($admin);
    }


    /**
     * Removes a BackendUser
     *
     * @param \RKW\RkwSurvey\Domain\Model\BackendUser $adminToRemove
     * @return void
     */
    public function removeAdmin(BackendUser $adminToRemove): void
    {
        $this->admin->detach($adminToRemove);
    }


    /**
     * Returns the admin
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\RKW\RkwSurvey\Domain\Model\BackendUser>
     */
    public function getAdmin(): ObjectStorage
    {
        return $this->admin;
    }


    /**
     * Sets the admin
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\RKW\RkwSurvey\Domain\Model\BackendUser> $admin
     * @return void
     */
    public function setAdmin(ObjectStorage $admin): void
    {
        $this->admin = $admin;
    }


    /**
     * Adds a token
     *
     * @param \RKW\RkwSurvey\Domain\Model\Token $token
     * @return void
     */
    public function addToken(Token $token): void
    {
        $this->token->attach($token);
    }


    /**
     * Removes a Token
     *
     * @param \RKW\RkwSurvey\Domain\Model\Token $tokenToRemove The Token to be removed
     * @return void
     */
    public function removeToken(Token $tokenToRemove): void
    {
        $this->token->detach($tokenToRemove);
    }


    /**
     * Returns the token
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\RKW\RkwSurvey\Domain\Model\Token> $token
     */
    public function getToken(): ObjectStorage
    {
        return $this->token;
    }


    /**
     * Sets the token
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\RKW\RkwSurvey\Domain\Model\Token> $token
     * @return void
     */
    public function setToken(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $token): void
    {
        $this->token = $token;
    }


    /**
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage
     */
    public function getBenchmarkQuestions(): ObjectStorage
    {
        $benchmarkQuestions = new ObjectStorage();
        foreach ($this->getQuestion() as $question) {
            if ($question->getBenchmark()) {
                $benchmarkQuestions->attach($question);
            }
        }

        return $benchmarkQuestions;
    }


    /**
     * Get question count
     *
     * @return int
     */
    public function getQuestionCountTotal(): int
    {
        if ($this->type == 2) {
            $questionTotalCount = 0;
            /** @var \RKW\RkwSurvey\Domain\Model\Question $container */
            foreach ($this->getQuestionContainer() as $container) {
                $questionTotalCount += $container->getQuestion()->count();
            }
            return $questionTotalCount;
        } else {
            return $this->getQuestion()->count();
        }
    }

}
