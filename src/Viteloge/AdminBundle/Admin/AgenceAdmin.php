<?php

namespace Viteloge\AdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\AdminBundle\Form\FormMapper;

use Viteloge\AdminBundle\Controller\AgenceController;

class AgenceAdmin extends VitelogeAdmin
{
    public $logo_manager;

    
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with("Général")
              ->add('nom')
              ->add('url')
              ->add('departement', null, array( 'required' => false ))
            ->add('civiliteContact', 'choice', array( 'choices' => array( 'Monsieur', 'Mademoiselle', 'Madame', 'Maître' ), 'required' => false ))
              ->add('nomContact', null, array( 'required' => false ))
              ->add('mail', null, array( 'required' => false ))
              ->add('inactive', 'choice', array( 'choices' => array( true => 'Désactivée', false => 'Activée' ), 'required' => true ))
            ->end()
            ->with("Coordonnées")
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
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('privilegiee', null, array( 'editable' => true, 'template' => 'VitelogeAdminBundle:Agence:privilegiee.html.twig', 'label' => ' ' ) )
            ->addIdentifier('NomAgenceMere')
            ->addIdentifier('nom')
            ->addIdentifier('cp')
            ->addIdentifier('ville')
            ->addIdentifier('departement')
            ->addIdentifier('countTraitements')
            ->add('hasXml', 'boolean' )
            ->add( 'active', 'boolean' )
        ;
    }

    public function validate(ErrorElement $errorElement, $object)
    {
        $errorElement
            ->with('nom')
                ->assertMaxLength(array('limit' => 255))
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
    
}