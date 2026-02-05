<?php

namespace App\Command;

use App\Entity\Story;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\String\Slugger\SluggerInterface;

#[AsCommand(name: 'app:story:populate-slugs', description: 'Populate story slugs from name')]
class PopulateStorySlugsCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private SluggerInterface $slugger,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // Find the user
        $stories = $this->entityManager
            ->getRepository(Story::class)
            ->findAll();

        $count = 0;

        foreach ($stories as $story) {
            $story->setSlugFr($this->slugger->slug(\mb_strtolower($story->getNameFr())));
            $story->setSlugEn($this->slugger->slug(\mb_strtolower($story->getNameEn())));
            $count ++;
        }

        // Persist and flush
        $this->entityManager->flush();

        $io->success( $count . ' stories updated successfully');

        return Command::SUCCESS;
    }
}
