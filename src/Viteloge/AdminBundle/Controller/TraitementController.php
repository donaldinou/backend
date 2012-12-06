<?php

namespace Viteloge\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Viteloge\AdminBundle\Service\TestTraitementService;


/**
 * @Route("/traitement")
 */
class TraitementController extends Controller
{
    /**
     * @Route("/exclus")
     * @Template()
     */
    public function exclusAction()
    {
        $em =  $this->get('doctrine.orm.entity_manager');
        $repo = $em->getRepository('Viteloge\AdminBundle\Entity\Traitement' );
        return array( 'traitements' => $repo->getExclus() );
    }

    /**
     * @Route("/{id}/test")
     * @Template()
     */
    public function testAction( $id )
    {
        $em =  $this->get('doctrine.orm.entity_manager');
        $repo = $em->getRepository('Viteloge\AdminBundle\Entity\Traitement' );
        $traitement = $repo->find( $id );
        $source = '';
        $variables = array( 'traitement' => $traitement );

        $request = $this->get('request');
        if ( $source = $request->get('source') ) {
            $tester = new TestTraitementService( $traitement );

            $type = $request->get('TypeSource');
            $variables['type'] = $type;
            
            $variables['source'] = $source;

            $variables['results'] = $tester->run( $type, $source );
        }

        if ( ! array_key_exists( 'type', $variables ) ) {
            $variables['type'] = $traitement->TypeUrlTraitement;
        }
        $variables['admin_pool'] = $this->container->get('sonata.admin.pool');

        return $variables;
    }
    
    /**
     * @Route("/{id}/control")
     * @Template()
     */
    public function controlAction( $id )
    {
        $em =  $this->get('doctrine.orm.entity_manager');
        $repo = $em->getRepository('Viteloge\AdminBundle\Entity\Traitement' );
        $pile_repo = $em->getRepository( 'Viteloge\AdminBundle\Entity\Pile' );
        $annonce_repo = $em->getRepository( 'Viteloge\AdminBundle\Entity\Annonce' );
        $traitement = $repo->find( $id );

        $variables = array(
            'traitement' => $traitement,
            'admin_pool' => $this->container->get('sonata.admin.pool'),
            'pile' =>  $pile_repo->getPileForTraitement( $traitement ),
            'flags' => $this->flattenFlags( $annonce_repo->getCountByFlag( $traitement ) ),
            'nbAnnoncesExportees' => $annonce_repo->getCountExported( $traitement )
        );
        return $variables;
    }

    /**
     * @Route("/{id}/heap_remove/{heap_id}")
     */
    public function heapRemove( $id, $heap_id )
    {
    }

    private function flattenFlags( $flags )
    {
        $flag_mapping = array( 0 => 'handled', 1 => 'unhandled' );
        $new_flags = array( 'unhandled' => 0, 'handled' => 0 );
        foreach ( $flags as $flag ) {
            $new_flags[$flag_mapping[$flag['flag']]] = $flag['nbAnnonces'];
        }
        return $new_flags;
    }
}
