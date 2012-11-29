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
        $results = null;

        $request = $this->get('request');
        if ($request->getMethod() == 'POST') {
            $tester = new TestTraitementService( $traitement );

            $type = $request->get('TypeSource');
            $source = $request->get('source');

            $results = $tester->run( $type, $source );
        }

        return array( 'traitement' => $traitement, 'source' => $source, 'results' => $results );
    }
    
}
