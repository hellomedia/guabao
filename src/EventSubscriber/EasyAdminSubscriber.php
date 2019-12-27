<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\String\Slugger\SluggerInterface;

class EasyAdminSubscriber implements EventSubscriberInterface
{
    private $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public static function getSubscribedEvents()
    {
        return [
            'easy_admin.pre_persist' => [
                ['setSlug', 10],
                ['setCreatedAt', 11],
                ['setUpdatedAt', 12],
            ],
            'easy_admin.pre_update' => [
                ['setSlug', 10],
                ['setUpdatedAt', 12],
            ]
        ];
    }

    public function setSlug(GenericEvent $event)
    {
        $entity = $event->getSubject();

        if (method_exists($entity, 'setSlug')) {

            if (method_exists($entity, 'getTitle')) {
                $slug = $this->slugger->slug($entity->getTitle());
            } elseif (method_exists($entity, 'getName')) {
                $slug = $this->slugger->slug($entity->getName());
            } elseif (method_exists($entity, 'getLabel')) {
                $slug = $this->slugger->slug($entity->getLabel());
            }

            $entity->setSlug($slug);

            $event['entity'] = $entity;
        }
    }

    public function setCreatedAt(GenericEvent $event)
    {
        $entity = $event->getSubject();

        if (method_exists($entity, 'setCreatedAt')) {

            $entity->setCreatedAt(new \Datetime());

            $event['entity'] = $entity;
        }
    }

    public function setUpdatedAt(GenericEvent $event)
    {
        $entity = $event->getSubject();

        if (method_exists($entity, 'setUpdatedAt')) {

            $entity->setUpdatedAt(new \Datetime());

            $event['entity'] = $entity;
        }
    }
}
