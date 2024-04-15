<?php

declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Yaml\Yaml;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Entity\Question;
use App\Entity\Answer;

#[AsCommand(
    name: "app:load-questions"
)]
class LoadQuestionsCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        string $name = null,
    ) {
        parent::__construct($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $yamlFile = file_get_contents('src/Resources/questions.yaml');
        $data = Yaml::parse($yamlFile);

        foreach ($data['questions'] as $qData) {
            $question = new Question($qData['text']);
            foreach ($qData['answers'] as $aData) {
                $answer = new Answer($aData['text'], $aData['correct'], $question);
                $question->addAnswer($answer);
            }
            $this->entityManager->persist($question);
        }

        $this->entityManager->flush();

        $io->success('Questions have been loaded successfully!');

        return Command::SUCCESS;
    }
}
