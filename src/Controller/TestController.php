<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Contract\TestManagerInterface;

class TestController extends AbstractController
{
    public function __construct(
        private readonly TestManagerInterface $testManager
    ) {}

    #[Route('/test/start', name: 'test_start')]
    public function startTest(Request $request): Response
    {
        $result = $this->testManager->initTest($request);
        if ($result === false) {
            return $this->redirectToRoute('test_no_questions');
        }

        return $this->redirectToRoute('test_question');
    }

    #[Route('/test/question', name: 'test_question')]
    public function showQuestion(Request $request): Response
    {
        return $this->render('question.html.twig', [
            'question' => $this->testManager->findQuestion($request->getSession())
        ]);
    }

    #[Route('/test/answer', name: 'test_answer', methods: ['POST'])]
    public function processAnswer(Request $request): Response
    {
        $nextCurrentQuestionExists = $this->testManager->processAnswer($request);

        if ($nextCurrentQuestionExists === false) {
            return $this->redirectToRoute('test_result');
        }

        return $this->redirectToRoute('test_question');
    }

    #[Route('/test/result', name: 'test_result')]
    public function showResults(Request $request): Response
    {
        return $this->render('result.html.twig', [
            'correctAnswers' => $this->testManager->getCorrectAnswers($request),
            'wrongAnswers' => $this->testManager->getWrongAnswers($request)
        ]);
    }

    #[Route('/test/no-questions', name: 'test_no_questions')]
    public function noQuestions(): Response
    {
        return $this->render('no_questions.html.twig');
    }
}
