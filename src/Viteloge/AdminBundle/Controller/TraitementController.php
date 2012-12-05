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
    
}
