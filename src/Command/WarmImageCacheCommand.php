<?php

namespace App\Command;

use App\Entity\Media;
use App\Service\Media\ImageCacheWarmer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:image:warm-cache',
    description: 'Warm LiipImagine thumbnails for legacy media images.',
)]
final class WarmImageCacheCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly ImageCacheWarmer $warmer,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption(
                'filter',
                null,
                InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                'Filter(s) to warm. If omitted, configured defaults are used.'
            )
            ->addOption(
                'limit',
                null,
                InputOption::VALUE_REQUIRED,
                'Maximum number of media rows to process.'
            )
            ->addOption(
                'id-from',
                null,
                InputOption::VALUE_REQUIRED,
                'Only process media with id >= this value.'
            )
            ->addOption(
                'batch-size',
                null,
                InputOption::VALUE_REQUIRED,
                'Doctrine batch size.',
                50
            )
            ->addOption(
                'dry-run',
                null,
                InputOption::VALUE_NONE,
                'Show what would be warmed without actually warming it.'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $filters = $input->getOption('filter');
        $limit = $this->toNullablePositiveInt($input->getOption('limit'), 'limit');
        $idFrom = $this->toNullablePositiveInt($input->getOption('id-from'), 'id-from');
        $batchSize = $this->toPositiveInt((string) $input->getOption('batch-size'), 'batch-size');
        $dryRun = (bool) $input->getOption('dry-run');

        $io->title('Warm LiipImagine image cache');

        $io->definitionList(
            ['Filters' => [] !== $filters ? implode(', ', $filters) : 'configured defaults'],
            ['Limit' => $limit ?? 'none'],
            ['ID from' => $idFrom ?? 'none'],
            ['Batch size' => $batchSize],
            ['Dry run' => $dryRun ? 'yes' : 'no'],
        );

        $total = $this->countMediaToProcess($idFrom, $limit);

        if (0 === $total) {
            $io->warning('No media found to process.');

            return Command::SUCCESS;
        }

        $io->section(sprintf('Found %d media item(s) to process.', $total));

        if ($dryRun) {
            $io->success('Dry run completed.');

            return Command::SUCCESS;
        }

        $progressBar = new ProgressBar($output, $total);
        $progressBar->start();

        $processed = 0;
        $warmed = 0;
        $failed = 0;
        $skipped = 0;
        $errorSamples = [];

        foreach ($this->iterateMedia($idFrom, $limit) as $media) {
            \assert($media instanceof Media);

            $result = $this->warmer->warmMedia(
                $media,
                [] !== $filters ? $filters : null,
            );

            ++$processed;
            $warmed += $result->warmed;
            $failed += $result->failed;

            if ($result->skipped) {
                ++$skipped;
            }

            if (null !== $result->message && \count($errorSamples) < 10) {
                $errorSamples[] = sprintf(
                    'Media #%s: %s',
                    (string) $media->getId(),
                    $result->message
                );
            }

            $progressBar->advance();

            if (0 === $processed % $batchSize) {
                $this->entityManager->clear();
            }
        }

        $progressBar->finish();
        $io->newLine(2);

        $io->table(
            ['Metric', 'Value'],
            [
                ['Processed media', (string) $processed],
                ['Warmed thumbs', (string) $warmed],
                ['Failed thumbs', (string) $failed],
                ['Skipped media', (string) $skipped],
            ]
        );

        if ([] !== $errorSamples) {
            $io->section('Sample errors');
            $io->listing($errorSamples);
        }

        if ($failed > 0) {
            $io->warning('Completed with some thumbnail warmup failures.');

            return Command::FAILURE;
        }

        $io->success('Image cache warmup completed successfully.');

        return Command::SUCCESS;
    }

    /**
     * @return iterable<Media>
     */
    private function iterateMedia(?int $idFrom, ?int $limit): iterable
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('m')
            ->from(Media::class, 'm')
            ->orderBy('m.id', 'ASC');

        if (null !== $idFrom) {
            $qb
                ->andWhere('m.id >= :idFrom')
                ->setParameter('idFrom', $idFrom);
        }

        if (null !== $limit) {
            $qb->setMaxResults($limit);
        }

        return $qb->getQuery()->toIterable();
    }

    private function countMediaToProcess(?int $idFrom, ?int $limit): int
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('COUNT(m.id)')
            ->from(Media::class, 'm');

        if (null !== $idFrom) {
            $qb
                ->andWhere('m.id >= :idFrom')
                ->setParameter('idFrom', $idFrom);
        }

        $count = (int) $qb->getQuery()->getSingleScalarResult();

        if (null !== $limit) {
            return min($count, $limit);
        }

        return $count;
    }

    private function toNullablePositiveInt(mixed $value, string $name): ?int
    {
        if (null === $value || '' === $value) {
            return null;
        }

        return $this->toPositiveInt((string) $value, $name);
    }

    private function toPositiveInt(string $value, string $name): int
    {
        if (!ctype_digit($value) || (int) $value <= 0) {
            throw new \InvalidArgumentException(sprintf('The "%s" option must be a positive integer.', $name));
        }

        return (int) $value;
    }
}
