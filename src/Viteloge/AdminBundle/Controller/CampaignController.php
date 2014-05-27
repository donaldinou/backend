<?php

namespace Viteloge\AdminBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController as Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Form\FormBuilder;

/**
 * @Route("/campaign")
 */
class CampaignController extends Controller
{
    /**
     * @Route("/{id}/test")
     */
    public function visualizeAction( $id ) {
        $om = $this->getDoctrine()->getManager();
        $repo = $om->getRepository('Viteloge\AdminBundle\Entity\Campaign' );
        $campaign = $repo->find( $id );

        $content = $campaign->getTemplate();
        
        return new Response( $content );
    }
}
