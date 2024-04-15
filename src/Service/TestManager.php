<?php

declare(strict_types=1);

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\DTO\ResultDTO;
use App\Entity\Result;
use App\Entity\Answer;
use App\Entity\Question;
use App\Contract\TestManagerInterface;
use App\Repository\QuestionRepository;

class TestManager implements TestManagerInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        protected readonly QuestionRepository $questionRepository
    ) {}

    public function initTest(Request $request): bool
    {
        $questions = $this->questionRepository->findAll();
        $questionIds = array_map(fn($question) => $question->getId(), $questions);
        if (count($questionIds) === 0) {
            return false;
        }

        shuffle($questionIds);

        $this->startSession($request->getSession(), $questionIds);

        return true;
    }

    public function findQuestion(SessionInterface $session): ?Question
    {
        $currentQuestion = $session->get('currentQuestion', 0);
        /** @var null|Question $question */
        $question = $this->questionRepository->find($currentQuestion);

        return $question;
    }

    public function processAnswer(Request $request): bool
    {
        $correctAnswers = $request->getSession()->get('correctAnswers', []);
        $wrongAnswers = $request->getSession()->get('wrongAnswers', []);
        $questionIds = $request->getSession()->get('questionIds', []);
        $nextCurrentQuestion = array_shift($questionIds);
        $question = $this->findQuestion($request->getSession());

        $resultDTO = $this->checkAnswers($request, $question);
        if ($resultDTO->isCorrect()) {
            $correctAnswers[] = $this->createTextForDisplay($question, $resultDTO->getCorrectAnswers());
        } else {
            $wrongAnswers[] = $this->createTextForDisplay($question, $resultDTO->getWrongAnswers());
        }

        $this->saveResult($request->getSession()->getId(), $question, $resultDTO);

        $this->updateSession(
            $request->getSession(),
            $correctAnswers,
            $wrongAnswers,
            $nextCurrentQuestion,
            $questionIds
        );

        return $nextCurrentQuestion !== null;
    }

    public function getCorrectAnswers(Request $request): array
    {
        return $request->getSession()->get('correctAnswers', []);
    }

    public function getWrongAnswers(Request $request): array
    {
        return $request->getSession()->get('wrongAnswers', []);
    }

    private function startSession(SessionInterface $session, array $questionIds): void
    {
        $session->set('currentQuestion', array_shift($questionIds));
        $session->set('questionIds', $questionIds);
        $session->set('correctAnswers', []);
        $session->set('wrongAnswers', []);
    }

    private function updateSession(
        SessionInterface $session,
        array $correctAnswers,
        array $wrongAnswers,
        ?int $nextCurrentQuestion,
        array $questionIds
    ): void {
        $session->set('correctAnswers', $correctAnswers);
        $session->set('wrongAnswers', $wrongAnswers);
        $session->set('currentQuestion', $nextCurrentQuestion);
        $session->set('questionIds', $questionIds);
    }

    private function checkAnswers(Request $request, Question $question): ResultDTO
    {
        /** @var array|null $asSubmittedAnswers */
        $asSubmittedAnswers = $request->request->all('answers');
        if (is_array($asSubmittedAnswers)) {
            $flippedSubmittedAnswers = array_flip($asSubmittedAnswers);
            $correctAnswers = [];
            $wrongAnswers = [];

            foreach ($question->getAnswers() as $answer) {
                if ($answer->isCorrect()) {
                    $correctAnswers[$answer->getId()] = $answer;
                } else {
                    $wrongAnswers[$answer->getId()] = $answer;
                }
            }

            $foundInCorrectAnswers = array_intersect_key($correctAnswers, $flippedSubmittedAnswers);
            $foundInWrongAnswers = array_intersect_key($wrongAnswers, $flippedSubmittedAnswers);

            $result = count($foundInCorrectAnswers) > 0 && count($foundInWrongAnswers) === 0;
        }

        return new ResultDTO(
            $result ?? false,
            $foundInCorrectAnswers ?? [],
            $foundInWrongAnswers ?? []
        );
    }

    /**
     * @param Question $question
     * @param array|Answer[] $answers
     * @return string
     */
    private function createTextForDisplay(Question $question, array $answers): string
    {
        return sprintf(
            '%s (%s)',
            $question->getText(),
            count($answers) > 0 ? implode(') , (', $answers) : 'You haven\'t chosen anything'
        );
    }

    private function saveResult(string $sessionId, Question $question, ResultDTO $resultDTO): void
    {
        $result = new Result(
            $sessionId,
            $resultDTO->isCorrect(),
            $question->getText(),
            $resultDTO->getCorrectAnswers(),
            $resultDTO->getWrongAnswers()
        );
        $this->em->persist($result);
        $this->em->flush();
    }
}
