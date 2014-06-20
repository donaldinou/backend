<?php

namespace Viteloge\AdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

use Viteloge\AdminBundle\Entity\Traitement;

class SponsorAdmin extends Admin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('type')
            ->add('insee')
            ->add('titre')
            ->add('texte')
            ->add('url')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('type')
            ->add('insee')
            ->addIdentifier('titre')
            ->addIdentifier('url')
        ;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add(
                'type',
                'choice',
                array(
                    'choices' => array(
                        'vignette' => 'sponsor.types.vignette'
                    )
                )
            )
            ->add('insee', null, array(
                'help' => 'help.sponsor_insee',
                'required' => false
            ) )
            ->add(
                'transac',
                'choice',
                array(
                    'choices' => Traitement::$TypesTransaction,
                    'empty_value' => '',
                    'required' => false
                )
            )
            ->add('titre')
            ->add('texte', null, array(
                'help' => 'help.sponsor_texte'
            ))
            ->add('url')
            ->add('link', null, array(
                'help' => 'help.sponsor_link'
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
            ->add('type')
            ->add('insee')
            ->add('transac')
            ->add('titre')
            ->add('texte')
            ->add('url')
            ->add('link')
        ;
    }
}
