<?php

namespace App\Service\Media;

use App\Entity\Media;
use Liip\ImagineBundle\Service\FilterService;
use Pack\Media\Helper\UploadHelper;
use Psr\Log\LoggerInterface;

final class ImageCacheWarmer
{
    private array $filters;

    /**
     * @param string[] $filters
     */
    public function __construct(
        private UploadHelper $uploadHelper,
        private readonly FilterService $filterService,
        private readonly LoggerInterface $logger,
        private readonly array $filterSets = [],
    ) {
        $this->filters = array_filter(
            array_keys($filterSets),
            fn(string $name) => str_starts_with($name, 'thumb_')
        );
    }

    /**
     * @param string[]|null $filters
     */
    public function warmMedia(Media $media, ?array $filters = null): WarmMediaResult
    {
        if (!$media->isImage()) {
            return new WarmMediaResult(
                warmed: 0,
                failed: 0,
                skipped: true,
                message: 'Not an image.',
            );
        }

        $path = $this->uploadHelper->getPublicPath($media);
        $filtersToWarm = $filters ?? $this->filters;

        if (!$path) {
            return new WarmMediaResult(
                warmed: 0,
                failed: 0,
                skipped: true,
                message: 'Missing media path.',
            );
        }

        if ([] === $filtersToWarm) {
            return new WarmMediaResult(
                warmed: 0,
                failed: 0,
                skipped: true,
                message: 'No filters configured.',
            );
        }

        $warmed = 0;
        $failed = 0;
        $errors = [];

        foreach ($filtersToWarm as $filter) {
            try {
                $this->filterService->warmupCache($path, $filter);
                ++$warmed;
            } catch (\Throwable $e) {
                ++$failed;
                $errors[] = sprintf('[%s] %s', $filter, $e->getMessage());

                $this->logger->warning('Failed to warm LiipImagine cache.', [
                    'mediaId' => $media->getId(),
                    'path' => $path,
                    'filter' => $filter,
                    'exception' => $e,
                ]);
            }
        }

        return new WarmMediaResult(
            warmed: $warmed,
            failed: $failed,
            skipped: false,
            message: [] !== $errors ? implode(' | ', $errors) : null,
        );
    }
}
