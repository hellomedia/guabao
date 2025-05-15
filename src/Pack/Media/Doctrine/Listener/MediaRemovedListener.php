<?php

namespace App\Pack\Media\Doctrine\Listener;

use App\Entity\Media;
use App\Pack\Media\Helper\UploadHelper;
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

    public function removeFileFromDisk(Media $image, PostRemoveEventArgs $args)
    {
        $file = $this->uploadsPath . '/' . $image->getPath();

        if ($file && file_exists($file)) {
            unlink($file);
        }
    }
}
