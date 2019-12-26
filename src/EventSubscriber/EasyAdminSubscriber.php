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
        return array(
            'easy_admin.pre_persist' => array('setSlug'),
            'easy_admin.pre_update' => array('setSlug'),
        );
    }

    public function setSlug(GenericEvent $event)
    {
        $entity = $event->getSubject();

        if (method_exists($entity, 'setSlug')) {

            if (method_exists($entity, 'getTitle')) {
                $slug = $this->slugger->slug($entity->getTitle());
            } elseif (method_exists($entity, 'getName')) {
                $slug = $this->slugger->slug($entity->getName());
            }

            $entity->setSlug($slug);

            $event['entity'] = $entity;
        }
    }
}
