<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[
    ORM\Entity,
    ORM\Table(name: 'answer')
]
class Answer
{
    #[
        ORM\Id,
        ORM\GeneratedValue(strategy: 'IDENTITY'),
        ORM\Column(type: 'integer', unique: true),
    ]
    private ?int $id;

    #[ORM\Column(type: 'string', length: 255, nullable: false)]
    private string $text;

    #[ORM\Column(type: 'boolean', nullable: false)]
    private bool $correct;

    #[
        ORM\ManyToOne(targetEntity: Question::class, inversedBy: 'answers'),
        ORM\JoinColumn(name: 'question_id', referencedColumnName: 'id', nullable: false)
    ]
    private Question $question;

    public function __construct(
        string $text,
        bool $correct,
        Question $question
    ) {
        $this->text = $text;
        $this->correct = $correct;
        $this->question = $question;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function isCorrect(): bool
    {
        return $this->correct;
    }

    public function getQuestion(): Question
    {
        return $this->question;
    }

    public function __toString(): string
    {
        return $this->getText();
    }
}
