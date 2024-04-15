<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[
    ORM\Entity,
    ORM\Table(name: 'result')
]
class Result
{
    #[
        ORM\Id,
        ORM\GeneratedValue(strategy: 'IDENTITY'),
        ORM\Column(type: 'integer', unique: true),
    ]
    private ?int $id;

    #[ORM\Column(type: 'string', length: 255, nullable: false)]
    private string $sessionId;

    #[ORM\Column(type: 'boolean', nullable: false)]
    private bool $correct;

    #[ORM\Column(type: 'string', length: 255, nullable: false)]
    private string $question;

    #[ORM\Column(name: 'correct_answers', type: 'simple_array', nullable: true)]
    private ?array $correctAnswers;

    #[ORM\Column(name: 'wrong_answers', type: 'simple_array', nullable: true)]
    private ?array $wrongAnswers;

    public function __construct(
        string $sessionId,
        bool $correct,
        string $question,
        ?array $correctAnswers = [],
        ?array $wrongAnswers = []
    ) {
        $this->sessionId = $sessionId;
        $this->correct = $correct;
        $this->question = $question;
        $this->correctAnswers = $correctAnswers;
        $this->wrongAnswers = $wrongAnswers;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSessionId(): string
    {
        return $this->sessionId;
    }

    public function isCorrect(): bool
    {
        return $this->correct;
    }

    public function getQuestion(): string
    {
        return $this->question;
    }

    public function getCorrectAnswers(): ?array
    {
        return $this->correctAnswers;
    }

    public function getWrongAnswers(): ?array
    {
        return $this->wrongAnswers;
    }
}
