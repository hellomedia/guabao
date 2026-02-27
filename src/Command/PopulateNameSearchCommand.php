<?php

namespace App\Command;

use App\Entity\Food;
use App\Entity\Ingredient;
use App\Entity\Story;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\String\Slugger\SluggerInterface;

#[AsCommand(name: 'app:search:populate', description: 'Populate nameSearch property')]
class PopulateNameSearchCommand extends Command
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

        $io->info('foods');

        $foods = $this->entityManager
            ->getRepository(Food::class)
            ->findAll();

        $io->info(count($foods));

        $count = 0;

        foreach ($foods as $food) {

            $io->info($food->getNameEn());

            $text = trim(mb_strtolower($food->getNameFr() . ' ' . $food->getNameEn() . ' ' . $food->getOriginalName()));
            $normalized = (string) $this->slugger->slug($text, ' ');

            $food->setNameSearch($normalized);

            $count ++;
        }

        $this->entityManager->flush();

        $io->success( $count . ' foods updated successfully');

        $io->info('ingredients');

        $ingredients = $this->entityManager
            ->getRepository(Ingredient::class)
            ->findAll();

        $io->info(count($ingredients));

        $count = 0;

        foreach ($ingredients as $ingredient) {

            $io->info($ingredient->getNameEn());

            $text = trim(mb_strtolower($ingredient->getNameFr() . ' ' . $ingredient->getNameEn()));
            $normalized = (string) $this->slugger->slug($text, ' ');

            $ingredient->setNameSearch($normalized);

            $count++;
        }

        $this->entityManager->flush();

        $io->success($count . ' ingredients updated successfully');

        return Command::SUCCESS;
    }
}
