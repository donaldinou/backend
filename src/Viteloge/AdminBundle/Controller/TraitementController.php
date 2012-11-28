<?php

namespace Viteloge\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;


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
}
