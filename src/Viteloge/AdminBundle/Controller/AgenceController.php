<?php

namespace Viteloge\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;


/**
 * @Route("/agence")
 */
class AgenceController extends Controller
{
    /**
     * @Route("/")
     * @Template()
     */
    public function indexAction()
    {
        return array();
    }

    /**
     * @Route("/{id}/stats")
     * @Template()
     */
    public function statsAction( $id )
    {
        $em =  $this->get('doctrine.orm.entity_manager');
        $agence_repo = $em->getRepository('Viteloge\AdminBundle\Entity\Agence' );
        $traitement_repo = $em->getRepository('Viteloge\AdminBundle\Entity\Traitement' );
        $annonce_repo = $em->getRepository('Viteloge\AdminBundle\Entity\Annonce' );
        $agence = $agence_repo->find( $id );

        $cycles = array();
        foreach ( $agence->traitements as $traitement ) {
            $cycles[$traitement->id] = array_map( function($cycle){
                    return array(
                        $cycle->fin->format('Y-m-d'),
                        $cycle->nbAnnonce,
                        $cycle->nbAnnonceInsert,
                        $cycle->nbAnnonceDelete
                    );
                }, $traitement_repo->getCycles( $traitement, true ) );
            
        }

        $stats_insee = $annonce_repo->getCountByInsee( $agence );
        if ( count( $stats_insee ) > 0 ) {
            $repartition = array(
                'labels' => array_map( function($stat) {
                        return join( ' : ', array( $stat['fullName'], $stat['nbAnnonces'] ) );
                    }, $stats_insee ),
                'data' => array_map( function($stat){
                        return $stat['nbAnnonces'];
                    }, $stats_insee )
            );
        }
        
        return array(
            'agence' => $agence,
            'cycles' => $cycles,
            'admin_pool' => $this->container->get('sonata.admin.pool'),
            'stats' => array(
                'exported' => $annonce_repo->getCountExported( null, $agence ),
                'all' => $annonce_repo->getCountMisc( $agence ),
                'nbvisits' => $agence_repo->getVisits( $agence ),
                'repartition' => $repartition
            )
        );
    }
    /**
     * @Route("/{id}/report")
     */
    public function reportAction( $id )
    {
        $reporter = $this->get('viteloge.admin.report_generator');

        $response = new Response();
        $response->setCharset( "iso-8859-1" );
        $response->setContent( $reporter->run( $id ) );
        
        return $response;
    }

    /**
     * @Route( "/img/{path}" )
     */
    public function imgAction( $path )
    {
        $reporter = $this->get('viteloge.admin.report_generator');

        if ( $reporter->pathIsReportImage( $path ) ) {
            $response = new Response();
            $response->setContent( $reporter->getImage( $path ) );
            $response->headers->set('Content-Type', 'image/png');
        } else {
            throw $this->createNotFoundException( "No such file" );
        }
        
        return $response;
    }
}
