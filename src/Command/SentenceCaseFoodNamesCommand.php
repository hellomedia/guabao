<?php

namespace App\Command;

use App\Entity\Food;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:food:sentence',
    description: 'Converts food names to sentence case.'
)]
class SentenceCaseFoodNamesCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $em,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $foods = $this->em->getRepository(Food::class)->findAll();

        foreach ($foods as $food) {

            $io->info('before: ' . $food->getNameEn());

            $food->setNameEn($this->toSentenceCase($food->getNameEn()));

            $io->info('after: ' . $food->getNameEn());
        }

        $this->em->flush();

        return Command::SUCCESS;
    }

    private function toSentenceCase(?string $text): ?string
    {
        if (!$text) {
            return $text;
        }

        // Skip if contains scripts without casing
        if (preg_match('/\p{Han}|\p{Thai}|\p{Hiragana}|\p{Katakana}/u', $text)) {
            return $text;
        }

        $lower = mb_strtolower($text);

        return mb_strtoupper(mb_substr($lower, 0, 1)) . mb_substr($lower, 1);
    }
}