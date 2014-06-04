<?php

namespace Viteloge\AdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;

use Viteloge\AdminBundle\Controller\AgenceController;

if ( ! is_callable('Viteloge\AdminBundle\Admin\array_to_array') ){
    function array_to_array( $src )
    {
        $dest = array();
        foreach ( $src as $k ) {
            $dest[$k] = $k;
        }
        return $dest;
    }
}

class AgenceAdmin extends VitelogeAdmin
{
    public $logo_manager;

    
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with($this->trans("group.agence.general") )
              ->add('nom')
              ->add('url')
              ->add('departement', null, array( 'required' => false ))
            ->add('civiliteContact', 'choice', array( 'choices' => array_to_array( array( 'Monsieur', 'Mademoiselle', 'Madame', 'Maître' ) ), 'required' => false ))
              ->add('nomContact', null, array( 'required' => false ))
              ->add('mail', null, array( 'required' => false ))
              ->add('inactive', 'choice', array( 'choices' => array( true => 'Désactivée', false => 'Activée' ), 'required' => true ))
            ->end()
            ->with($this->trans("group.agence.details"))
              ->add( 'adresse', 'textarea', array( 'required' => false ) )
              ->add('cp', null, array( 'required' => false ))
              ->add('ville', null, array( 'required' => false ))
              ->add('tel', null, array( 'required' => false ))
              ->add('fax', null, array( 'required' => false ))
            ->end()
            ->add('specif', null, array( 'required' => false ) )
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('nom')
            ->add('ville')
            ->add('agenceMere.nom')
            ->add('with_privileges','doctrine_orm_callback', array(
                      'callback' => function($queryBuilder, $alias, $field, $value) {
                          if (!$value) {
                              return;
                          }
                          if ( ! $value["value"] ) {
                              return true;
                          }
                          
                          $queryBuilder->andWhere( $alias . '.idPrivilege != 0');
                          return true;
                      },
                      'field_type' => 'checkbox'
            )) 
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('NomAgenceMere')
            ->addIdentifier('nom')
            ->addIdentifier('cp')
            ->addIdentifier('ville', null, array( 'template' => 'VitelogeAdminBundle::shortened_list_field.html.twig' ) )
            ->addIdentifier('departement', null, array( 'template' => 'VitelogeAdminBundle::shortened_list_field.html.twig' ) )
            ->addIdentifier('countTraitements')
            ->add('hasXml', 'boolean' )
            ->add( 'active', 'boolean' )
            ->add('privilegiee', null, array( 'editable' => false, 'template' => 'VitelogeAdminBundle:Agence:privilegiee.html.twig', 'label' => '[P]' ) )
        ;
        
        if ( $this->isGranted( 'ROLE_OPERATOR' ) || $this->isGranted( 'ROLE_COMMERCIAL' ) ) {
            
            $listMapper->add( '_action', 'actions', array(
                                  'actions' => array( 'stats' => array( 'template' => 'VitelogeAdminBundle:Agence:list_action_stats.html.twig' ) )
                                                          )
                              )
                ;
        }
    }

    public function validate(ErrorElement $errorElement, $object)
    {
        $errorElement
            ->with('nom')
                ->assertLength(array('max' => 255))
            ->end()
            ->with('mail')
                ->assertEmail()
            ->end()
        ;
    }

    public function getTemplate( $name )
    {
        switch ( $name ) 
        {
            case 'edit':
                return 'VitelogeAdminBundle:Agence:edit.html.twig';
                break;
            default:
                return parent::getTemplate( $name );
        }
        
    }

    public function Upload_Form()
    {
        return AgenceController::buildLogoForm( $this->getFormContractor()->getFormBuilder( "agence_logo" ) )->createView();
    }

    public function has_logo()
    {
        return $this->logo_manager->hasLogo( $this->subject );
    }

    public function logo_path()
    {
        return $this->logo_manager->logoPath( $this->subject );
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $id_param = $this->getRouterIdParameter();
        $collection->add('stats',$id_param.'/stats');
        $collection->add('statsdetail',$id_param.'/stats/detail');
        $collection->add('report',$id_param.'/report');
        $collection->add('img','img/{path}');
        $collection->add('logo', $id_param.'/logo');
    }
}
