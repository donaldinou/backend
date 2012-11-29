<?php

namespace Viteloge\AdminBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Viteloge\AdminBundle\Entity\Agence;

class DefaultSetter
{
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $entityManager = $args->getEntityManager();

        if ( $entity instanceof Agence) {
            $entity->dateCreation = new \DateTime();
        }
    }
}