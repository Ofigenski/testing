<?php

declare(strict_types=1);

namespace App\DTO;

use App\Entity\Answer;
use App\Entity\Question;

class ResultDTO
{
    public function __construct(
        public readonly bool $correct,
        public readonly array $correctAnswers,
        public readonly array $wrongAnswers,
    ) {}

    public function isCorrect(): bool
    {
        return $this->correct;
    }

    /** @return array|Answer[] */
    public function getCorrectAnswers(): array
    {
        return $this->correctAnswers;
    }

    /** @return array|Answer[] */
    public function getWrongAnswers(): array
    {
        return $this->wrongAnswers;
    }
}
