<?php

namespace Viteloge\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;


/**
 * @Route("/xmlfeed")
 */
class XmlFeedController extends Controller
{
    /**
     * @Route("/{id}/stats")
     * @Template()
     */
    public function statsAction( $id )
    {
        $em =  $this->get('doctrine.orm.entity_manager');
        $agence_repo = $em->getRepository('Viteloge\AdminBundle\Entity\Agence' );
        $feed_repo = $em->getRepository('Viteloge\AdminBundle\Entity\XmlFeed' );

        $feed = $feed_repo->find( $id );

        $cycles_objects = $feed_repo->getCycles( $feed );

        $cycles = array_map( function($cycle){
                return array(
                    $cycle->fin->format('Y-m-d'),
                    $cycle->nbAnnonce,
                    $cycle->nbAnnonceInsert,
                    $cycle->nbAnnonceDelete
                );
            }, $feed_repo->getCycles( $feed, true ) );

        return array(
            'admin_pool' => $this->container->get('sonata.admin.pool'),
            'xmlfeed' => $feed,
            'cycles' => $cycles
        );
    }
}
