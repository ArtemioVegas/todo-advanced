<?php
/**
 * Created by PhpStorm.
 * User: Artemio
 * Date: 07.05.2018
 * Time: 21:21
 */

namespace AppBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use AppBundle\Entity\Project;

class ProjectSubscriber implements EventSubscriber
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

        if ($entity instanceof Project) {
            if ( $type === 'Persist' ){
                $entity->setCreatedAt(new \DateTime);
            }elseif ( $type === 'Update' ){
                $entity->setUpdatedAt(new \DateTime);
            }
        }
    }

}