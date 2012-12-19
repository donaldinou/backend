<?php

namespace Viteloge\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Component\HttpFoundation\Response;

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
        return array(
            'traitements' => $repo->getExclus(),
            'admin_pool' => $this->container->get('sonata.admin.pool'),
        );
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
        $source = $request->get('source');
        if ( $source || ( $request->getMethod() == 'POST' ) ) {
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
            'lastDownload' => $repo->getLastContentInfo( $traitement ),
            'flags' => $this->flattenFlags( $annonce_repo->getCountByFlag( $traitement ) ),
            'nbAnnoncesExportees' => $annonce_repo->getCountExported( $traitement )
        );
        return $variables;
    }

    /**
     * @Route("/{id}/heap_remove/{heap_id}")
     */
    public function heapRemoveAction( $id, $heap_id )
    {
        $em =  $this->get('doctrine.orm.entity_manager');
        $traitement_repo = $em->getRepository('Viteloge\AdminBundle\Entity\Traitement' );
        $pile_repo = $em->getRepository( 'Viteloge\AdminBundle\Entity\Pile' );

        $traitement = $traitement_repo->find( $id );
        $pile = $pile_repo->find( $heap_id );
        if ( $pile && $traitement && $pile->traitement == $traitement ) {
            $full_url = $pile->getFull_url();
            $em->remove( $pile );
            $em->flush();
            $this->get('session')->setFlash( 'notice', $full_url . ' a été supprimée' );
        } else {
            $this->get('session')->setFlash( 'error', 'Paramètres invalides' );
        }
        return $this->redirect( $this->generateUrl( 'viteloge_admin_traitement_control', array( 'id' => $id )  ) );
    }

    /**
     * @Route("/{id}/modify")
     * @Method("POST")
     */
    public function modifyAction( $id )
    {
        $em =  $this->get('doctrine.orm.entity_manager');
        $traitement_repo = $em->getRepository('Viteloge\AdminBundle\Entity\Traitement' );
        $pile_repo = $em->getRepository( 'Viteloge\AdminBundle\Entity\Pile' );
        $annonce_repo = $em->getRepository( 'Viteloge\AdminBundle\Entity\Annonce' );
        $traitement = $traitement_repo->find( $id );

        $request = $this->get('request');
        $action = $request->get('action');
        switch( $action ) 
        {
            case 'clear_heap':
                $nb = $pile_repo->clearFor( $traitement );
                $this->get('session')->setFlash( 'notice', 'Pile vidée... (' . $nb . ' urls)' );
                break;
            case 'reset_flag':
                $nb = $annonce_repo->resetFlag( $traitement );
                $this->get('session')->setFlash( 'notice', 'Reset des flags pour traitement... (' . $nb . ' annonces)' );
                break;
            case 'heap_top':
                $traitement->TimeStampTraitement = new \DateTime('2000-01-01');
                $em->persist( $traitement );
                $em->flush();
                $this->get('session')->setFlash( 'notice', 'Traitement remis en haut de la pile...' );
                break;
            case 'file_update':
                $nb = $annonce_repo->forceUpdate( $traitement );
                $this->get('session')->setFlash( 'notice', 'Forçage de la mise à jour... (' . $nb . ' annonces)' );
                break;
            case 'reset_errors':
                $nb = $traitement_repo->resetErrors( $traitement );
                $this->get('session')->setFlash( 'notice', 'Reset des erreurs pour traitement... (' . $nb . ' erreurs supprimées)' );
                break;
            case 'reactivate_safe':
                $traitement->reactivate();
                $em->persist( $traitement );
                $em->flush();
                $this->get('session')->setFlash( 'notice', 'Réactivation du traitement...' );
                break;
            case 'reactivate_continue':
                $traitement->reactivate( true );
                $em->persist( $traitement );
                $em->flush();
                $this->get('session')->setFlash( 'notice', 'Remise en route du traitement...' );
                break;
            case 'end':
                $traitement->forceEnd();
                $em->persist( $traitement );
                $em->flush();
                $this->get('session')->setFlash( 'notice', 'Fin de traitement forcée...' );
                break;
            case 'unpause':
                $traitement->endPause();
                $em->persist( $traitement );
                $em->flush();                
                $this->get('session')->setFlash( 'notice', 'Traitement dépausé...' );
                break;
            default:
                $this->get('session')->setFlash( 'error', 'No such action!' );
                break;
        }
        
        return $this->redirect( $this->generateUrl( 'viteloge_admin_traitement_control', array( 'id' => $id )  ) );
    }

    /**
     * @Route("/{id}/stats.png")
     */
    public function graphAction( $id )
    {
        require_once( __DIR__ . '/../libs/jpgraph/jpgraph.php' );
        require_once( __DIR__ . '/../libs/jpgraph/jpgraph_line.php' );
        require_once( __DIR__ . '/../libs/jpgraph/jpgraph_date.php' );
        
        $em =  $this->get('doctrine.orm.entity_manager');
        $traitement_repo = $em->getRepository('Viteloge\AdminBundle\Entity\Traitement' );
        $traitement = $traitement_repo->find( $id );

        $cycles = $traitement_repo->getCycles( $traitement );
        $xdata = array();
        $ydata = array();
        $ydataNew = array();
        $ydataDel = array();
        foreach ( $cycles as $cycle ) {
            $ydata[] = $cycle->nbAnnonce;
            $ydataNew[] = $cycle->nbAnnonceInsert;
            $ydataDel[] = $cycle->nbAnnonceDelete;
            $xdata[] = $cycle->fin->getTimestamp();
        }
        

        $graph = new \Graph( 800, 450 );
        $graph->SetScale("datlin");
//        $graph->img->SetAntiAliasing();
        $graph->yaxis->title->Set("Nombre d'annonces");
        $graph->xaxis->SetLabelAngle( 30 );
        $graph->legend->setPos( 0.5, 0, 'center', 'top' );

        $lineplot = new \LinePlot($ydata, $xdata );
        $lineplot->SetStepStyle();
        $lineplot->SetLegend("Nombre d'annonces total");

        $lineplotNew = new \LinePlot($ydataNew, $xdata );
        $lineplotNew ->SetColor("blue"); 
        $lineplotNew->SetStepStyle();
        $lineplotNew->SetLegend("Nombre d'annonces nouvelles");
	
        $lineplotDelete = new \LinePlot($ydataDel, $xdata );
        $lineplotDelete ->SetColor("red"); 
        $lineplotDelete->SetStepStyle();
        $lineplotDelete->SetLegend("Nombre d'annonces supprimées");

        
        $graph->Add($lineplot);
        $graph->Add($lineplotNew);
        $graph->Add($lineplotDelete);
        
        $graph->Stroke();
        exit;
    }

    /**
     * @Route("/{id}/lastContent")
     */
    public function lastContentAction( $id )
    {
        require_once( 'my_http_build_url.php' );
        
        $em =  $this->get('doctrine.orm.entity_manager');
        $traitement_repo = $em->getRepository('Viteloge\AdminBundle\Entity\Traitement' );
        $traitement = $traitement_repo->find( $id );

        $content = $traitement_repo->getLastContentInfo( $traitement, true );

        $content_txt = $content['result'];

        $url = http_build_url( $content['url'] . $content['url2'], null,
                               HTTP_URL_STRIP_PATH | HTTP_URL_STRIP_QUERY | HTTP_URL_STRIP_FRAGMENT );

        if ( ! preg_match( '/<base *href/i', $content_txt ) ) {
            $content_txt = '<base href="' . $url . '">' . $content_txt;
        }

        $response = new Response();
        $response->setContent( $content_txt );
        $response->headers->set('Content-Base', 'http://localhost:10101/');
        return $response;
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
