<?php

namespace AppBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use AppBundle\Entity\User;


class UserSubscriber implements EventSubscriber
{
    public function getSubscribedEvents()
    {
        return array(
            'prePersist',
            'preUpdate',
        );
    }

    public function preUpdate(LifecycleEventArgs $args)
    {
        $this->setTime($args,'Update');
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $this->setTime($args,'Persist');
    }

    public function setTime(LifecycleEventArgs $args, $type)
    {
        $entity = $args->getEntity();

        if ($entity instanceof User) {
            if ( $type === 'Persist' ){
                $entity->setCreatedAt(new \DateTime);
            }elseif ( $type === 'Update' ){
                $entity->setUpdatedAt(new \DateTime);
            }
        }
    }
}