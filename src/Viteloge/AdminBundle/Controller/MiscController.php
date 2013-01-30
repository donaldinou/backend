<?php

namespace Viteloge\AdminBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;


class MiscController extends Controller 
{
    /**
     * @Route("/lang/{lang}")
     */
    public function langAction( $lang )
    {
        $request = $this->getRequest();

        $request->setLocale($lang);
        $this->get('session')->set('_locale', $lang);
        
        $referer_url = $this->get('request')->headers->get('referer');
        if ($referer_url != null) {
            return $this->redirect($referer_url);
        } else {
            return $this->redirect($this->generateUrl('admin_dashboard'));
        }
    }
}
