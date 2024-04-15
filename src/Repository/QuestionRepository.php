<?php

declare(strict_types=1);

namespace App\Repository;

use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use App\Entity\Question;

class QuestionRepository extends ServiceEntityRepository
{
    public function __construct(
        protected readonly ManagerRegistry $registry,
    ) {
        parent::__construct($registry, Question::class);
    }
}
