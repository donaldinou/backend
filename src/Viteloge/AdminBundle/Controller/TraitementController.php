<?php

namespace Viteloge\AdminBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController as Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Viteloge\AdminBundle\Service\TestTraitementService;
use Viteloge\AdminBundle\Entity\BlacklistFilter;


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
        $t = $this->get('translator');

        $request = $this->get('request');

        $filter = new BlacklistFilter();
        $form = $this->createFormBuilder( $filter )
            ->add( 'poliris', 'choice',
                   array( 'choices' => array(
                              '-1' => $t->trans( 'Sauf poliris' ),
                              '0' => $t->trans( 'Tous' ),
                              '1' => $t->trans( 'Juste poliris' ) ),
                          'expanded' => true, 'required' => false
                          ) )
            ->add( 'sort', 'choice',
                   array( 'choices' => array(
                              $repo::SORT_BY_PRIVILEGE => $t->trans( 'Trier par privilège' ),
                              $repo::SORT_BY_AGENCY => $t->trans( 'Trier par agence' ),
                              $repo::SORT_BY_EXDATE => $t->trans( 'Trier par date d\'exclusion' ) ),
                          'required' => false
                          ) )
            ->getForm();
        $form->bind( $request );
        
        $opts = array();
        if ( $filter->getPoliris() ) {
            $opts['only_poliris'] = ( "1" == $filter->getPoliris() );
        }
        if ( $filter->getSort() ) {
            $opts['sort_key'] = $filter->getSort();
        }
        
        return array(
            'traitements' => $repo->getExclus( $opts ),
            'admin_pool' => $this->container->get('sonata.admin.pool'),
            'filter_form' => $form->createView()
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

            try 
            {
                $tmp = $tester->run( $type, $source );
                $variables['results'] = $tmp['results'];
                $variables['expressions'] = $tmp['info_expressions'];
                

                if ( ! is_null( $tester->downloadedSource ) ) {
                    $variables['source'] = $tester->downloadedSource;
                }
            } catch( \Exception $e ) {
                $this->addFlash( 'error', $e->getMessage() );
            }
        }
        if ( ! empty( $traitement->UrlInitSession ) ) {
            $variables["initsessionurl"] = TestTraitementService::build_single_custom_url( $traitement, $traitement->UrlInitSession );
        }
        

        if ( ! array_key_exists( 'type', $variables ) ) {
            $variables['type'] = $traitement->TypeUrlTraitement;
        }
        $variables['admin_pool'] = $this->container->get('sonata.admin.pool');

        return $variables;
    }
    /**
     * @Route("/{id}/test/clear_cookies")
     */
    public function clearCookiesAction( $id )
    {
        $em =  $this->get('doctrine.orm.entity_manager');
        $repo = $em->getRepository('Viteloge\AdminBundle\Entity\Traitement' );
        $traitement = $repo->find( $id );
        $tester = new TestTraitementService( $traitement );
        if ( $tester->clearCookies() ) {
            return new Response( "ok" );
        } else {
            return new Response( "ko" );
        }
    }
    
    
    /**
     * @Route("/{id}/control")
     * @Template()
     */
    public function controlAction( Request $request, $id )
    {
        $em =  $this->get('doctrine.orm.entity_manager');
        $repo = $em->getRepository('Viteloge\AdminBundle\Entity\Traitement' );
        $pile_repo = $em->getRepository( 'Viteloge\AdminBundle\Entity\Pile' );
        $annonce_repo = $em->getRepository( 'Viteloge\AdminBundle\Entity\Annonce' );
        $traitement = $repo->find( $id );

        $pile_long = (bool) $request->query->get( 'pile_long', false );
        $pile_total = $pile_repo->getPileCountForTraitement( $traitement );
        
        $variables = array(
            'traitement' => $traitement,
            'admin_pool' => $this->container->get('sonata.admin.pool'),
            'pile' =>  $pile_total > 0 ? $pile_repo->getPileForTraitement( $traitement, $pile_long ) : array(),
            'pile_total' => $pile_total,
            'pile_long' => $pile_long,
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
            $this->addFlash( 'notice', $full_url . ' a été supprimée' );
        } else {
            $this->addFlash( 'error', 'Paramètres invalides' );
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

        $t = $this->get('translator');
        
        switch( $action ) 
        {
            case 'clear_heap':
                $nb = $pile_repo->clearFor( $traitement );
                $this->addFlash( 'notice',  $t->trans( 'control.messages.clear_heap' ) . '... (' . $nb . ' urls)' );
                break;
            case 'reset_flag':
                $nb = $annonce_repo->resetFlag( $traitement );
                $this->addFlash( 'notice', $t->trans( 'control.messages.reset_flag' ) . '... (' . $nb . ' annonces)' );
                break;
            case 'heap_top':
                $traitement->TimeStampTraitement = new \DateTime('2000-01-01');
                $em->persist( $traitement );
                $em->flush();
                $this->addFlash( 'notice', $t->trans( 'control.messages.heap_top' ) );
                break;
            case 'file_update':
                $nb = $annonce_repo->forceUpdate( $traitement );
                $this->addFlash( 'notice', $t->trans( 'control.messages.file_update' ) . '... (' . $nb . ' annonces)' );
                break;
            case 'file_update_errors':
                $nb = $annonce_repo->forceUpdate( $traitement,false );
                $this->addFlash( 'notice', $t->trans( 'control.messages.file_update_errors' ) . '... (' . $nb . ' annonces)' );
                break;
            case 'file_clear':
                if ( $traitement->agence->idPrivilege != 0 ) {
                    $this->addFlash( 'error', $t->trans( 'control.messages.file_clear_privileges_error' ) );
                } else if ( ( $nb_annonces = $annonce_repo->getCountExported( $traitement ) ) > 1000 ) {
                    $this->addFlash( 'error', $t->trans( 'control.messages.file_clear_too_big' ) );
                } else {
                    if ( $request->get('confirmed') ) {
                        $nb = $annonce_repo->forceDelete( $traitement );
                        $this->addFlash( 'notice', $nb . ' ' . $t->trans( 'control.messages.file_clear' ) );
                        
                    } else {
                        return $this->render( 'VitelogeAdminBundle:Traitement:confirm_clear_file.html.twig',
                                              array( 'traitement' => $traitement,
                                                     'nb_annonces' => $nb_annonces )
                        );
                    }
                    
                }
                break;
            case 'reset_errors':
                $nb = $traitement_repo->resetErrors( $traitement );
                $this->addFlash( 'notice',  $t->trans(  'control.messages.reset_errors', array( '%nb%' => $nb ) ) );
                break;
            case 'reactivate_safe':
                $traitement->reactivate();
                $em->persist( $traitement );
                $em->flush();
                $this->addFlash( 'notice', $t->trans( 'control.messages.reactivate_safe' ) );
                break;
            case 'reactivate_continue':
                $traitement->reactivate( true );
                $em->persist( $traitement );
                $em->flush();
                $this->addFlash( 'notice', $t->trans( 'control.messages.reactivate_continue' ) );
                break;
            case 'end':
                $traitement->forceEnd();
                $em->persist( $traitement );
                $em->flush();
                $this->addFlash( 'notice', $t->trans( 'control.messages.end' ) );
                break;
            case 'unpause':
                $traitement->endPause();
                $em->persist( $traitement );
                $em->flush();                
                $this->addFlash( 'notice', $t->trans( 'control.messages.unpause' ) );
                break;
            default:
                $this->addFlash( 'error', 'No such action!' );
                break;
        }
        
        return $this->redirect( $this->generateUrl( 'viteloge_admin_traitement_control', array( 'id' => $id )  ) );
    }

    /**
     * @Route("/modify")
     * @Method("POST")
     */
    public function massModifyAction()
    {
        $em =  $this->get('doctrine.orm.entity_manager');
        $traitement_repo = $em->getRepository('Viteloge\AdminBundle\Entity\Traitement' );
//        $traitement = $traitement_repo->find( $id );

        $request = $this->get('request');
        $action = $request->get('action');
        switch( $action ) 
        {
            case 'reactivate_safe':
                $full = false;
                break;
            case 'reactivate_continue':
                $full = true;
                break;
            default:
                throw new \Exception( "Unknown action" );
        }
        
        $selected = $request->get('selected');

        if ( $selected ) {    
            foreach ( $selected as $id => $_id ) {
                $traitement = $traitement_repo->find( $id );
                $traitement->reactivate( $full );
                $em->persist( $traitement );
            }
            $em->flush();
        }
        
        
        return $this->redirect( $this->generateUrl( 'viteloge_admin_traitement_exclus' ) );
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
        require_once( __DIR__ . '/../libs/my_http_build_url.php' );
        
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
