<?php

declare(strict_types=1);

namespace App\Contract;

use App\Entity\Question;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

interface TestManagerInterface
{
    public function initTest(Request $request): bool;

    public function findQuestion(SessionInterface $session): ?Question;

    public function processAnswer(Request $request): bool;

    public function getCorrectAnswers(Request $request): array;

    public function getWrongAnswers(Request $request): array;
}
