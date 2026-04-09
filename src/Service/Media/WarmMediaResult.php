<?php

namespace App\Service\Media;

final class WarmMediaResult
{
    public function __construct(
        public readonly int $warmed,
        public readonly int $failed,
        public readonly bool $skipped,
        public readonly ?string $message = null,
    ) {}
}
