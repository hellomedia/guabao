<?php

namespace Pack\Media\Doctrine\Listener;

use App\Entity\Media;
use App\Enum\MediaType;
use Pack\Media\Helper\UploadHelper;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PostRemoveEventArgs;
use Doctrine\ORM\Events;

/**
 * https://symfony.com/doc/current/doctrine/events.html
 */
#[AsEntityListener(event: Events::postRemove, method: 'removeFileFromDisk', entity: Media::class, lazy: true)]
class MediaRemovedListener
{
    public function __construct(
        private UploadHelper $uploadHelper,
        private string $uploadsPath,
    )
    {
    }

    public function removeFileFromDisk(Media $media, PostRemoveEventArgs $args)
    {
        if ($media->getType() !== MediaType::IMAGE) {
            return;
        }

        $file = $this->uploadsPath . '/' . $media->getPath();

        if ($file && file_exists($file) && is_file($file)) {
            unlink($file);
        }
    }
}
