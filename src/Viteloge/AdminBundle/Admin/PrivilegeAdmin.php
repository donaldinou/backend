<?php

namespace Viteloge\AdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\AdminBundle\Form\FormMapper;

use Viteloge\AdminBundle\Entity\Privilege;


class PrivilegeAdmin extends Admin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $year = date('Y');
        $year_choices = range( $year, $year + 3 );
        
        $formMapper
            ->add( 'agence' )
            ->add( 'dateDebut', null, array( 'years' => $year_choices ) )
            ->add( 'dateFin', null, array( 'years' => $year_choices ) )
            ->add( 'typeTransaction', 'choice', array( 'expanded' => true, 'choices' => Privilege::$TYPES_TRANSACTION ) )
            ->add( 'minPrix' )
            ->add( 'maxPrix' )
            ->add( 'nbAnnonce' )
            ->add( 'code', "choice", array( "choices" => Privilege::$PRIVILEGE_CODES ) )
            ->add( 'specifAgence' )
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('agence.nom')
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier( 'nomAgence')
            ->add( 'offre', 'string' )
            ->add( "Nombre d'annonces", 'string' )
            ->addIdentifier( 'dateDebut' )
            ->addIdentifier( 'dateFin' )
            ->addIdentifier( 'typeTransaction' )
            ->addIdentifier( 'minPrix' )
            ->addIdentifier( 'maxPrix' )
        ;
    }

    public function validate(ErrorElement $errorElement, $object)
    {
    }
}