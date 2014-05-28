<?php

namespace Viteloge\AdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Route\RouteCollection;

use Viteloge\AdminBundle\Entity\Traitement;

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


class CampaignAdmin extends VitelogeAdmin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('name')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name')
            ->add('transaction')
        ;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('name')
            ->add('subject')
            ->add('template')
            ->add('transaction', 'choice', array( 'choices' => Traitement::$TypesTransaction, 'empty_value' => '', 'required' => false ))
            ->add('types', 'choice', array( 'choices' => array_to_array( array( 'Appartement', 'Maison', 'Terrain' ) ), 'multiple' => true, 'required' => false ) )
            ->add('pieces', 'choice', array( 'choices' => array( 1 => "1", 2 => "2", 3 => "3", 4 => "4", 5 => "5 et +" ), 'multiple' => true, 'required' => false ) )
            ->add('insee')
            ->add( 'schedules', 'sonata_type_collection', array(), array(
                'edit' => 'inline',
                'inline' => 'table'
            ) )
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('name')
            ->add('template')
            ->add('transaction')
            ->add('types')
            ->add('pieces')
            ->add('insee')
        ;
    }
    
    public function prePersist($transaction)
    {
        $this->preUpdate($transaction);
    }
 
    public function preUpdate($transaction)
    {
        foreach ( $transaction->getSchedules() as $schedule ) {
            $schedule->setCampaign( $transaction );
        }
    }

    public function getTemplate( $name )
    {
        switch ( $name ) 
        {
            case 'edit':
                return 'VitelogeAdminBundle:Campaign:edit.html.twig';
                break;
            default:
                return parent::getTemplate( $name );
        }       
    }

    protected function configureRoutes( RouteCollection $collection ) {
        $id_param = $this->getRouterIdParameter();
        $collection->add( 'visualize', $id_param . '/test' );
    }
}
