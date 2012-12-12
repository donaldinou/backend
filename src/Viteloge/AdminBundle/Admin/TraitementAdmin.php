<?php

namespace Viteloge\AdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\AdminBundle\Form\FormMapper;

use Viteloge\AdminBundle\Entity\Traitement;

class TraitementAdmin extends VitelogeAdmin
{    
    
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with( 'General' )
              ->add( 'agence' )
              ->add( 'BaseUrlTraitement', 'text' )
              ->add( 'ParametresPost', 'text', array('required' =>false ) )
              ->add( 'UrlInitSession', null, array( 'required' => false ) )
              ->add( 'TypeUrlTraitement', 'choice', array( 'choices' => Traitement::$TypesUrl ) )
              ->add( 'TypeTransactionTraitement', 'choice', array( 'choices' => Traitement::$TypesTransaction ) )
              ->add( 'TypeUrlSortieTraitement', 'choice', array( 'choices' => Traitement::$TypesUrl ) )
              ->add( 'LimitPublication', 'choice', array( 'choices' => Traitement::$PublicationLimits ) )
              ->add( 'Exclus', null, array( 'required' => false ) )
            ->end()
            ->with('Détails des expressions rationelles')
              ->add( 'ExpNbBien', 'text', array( 'required' => false ) )
              ->add( 'ExpNbPage', 'text', array( 'required' => false ) )
            ->end()
            ->with( 'Page suivante' )
              ->add( 'ExpPageSuivante', 'text', array( 'required' => false ) )
            ->add( 'ModelUrlPageSuivante', null, array( 'required' => false ) )
            ->end()
            ->with( 'Liens vers fiche / Page de résultats' )
              ->add( 'ExpLiensFiche', 'text', array( 'required' => false ) )
            ->add( 'ModelUrlFicheTraitement', null, array( 'required' => false ) )
            ->add( 'ModelUrlResultatTraitement', null, array( 'required' => false ) )
              ->add( 'SplitResultAnnonce', 'text', array( 'required' => false ) )
              ->add( 'ExpUrlElements', 'text', array( 'required' => false ) )
            ->add( 'ModelUrlFicheFinal', null, array( 'required' => false ) )
            ->end()
            ->with( 'Modules' )
              ->add( 'ModuleResultatTraitement', 'choice', array( 'choices' => Traitement::$ModulesResultat ) )
              ->add( 'ModuleFicheTraitement', 'choice', array( 'choices' => Traitement::$ModulesFiche ) )
            ->end()
            ->with( 'Photo de la fiche' )
              ->add( 'ExpUrlPhoto', 'text', array( 'required' => false ) )
              ->add( 'ModelUrlPhoto', null, array( 'required' => false ) )
              ->add( 'FilenameNoPhoto', null, array( 'required' => false ) )
            ->end()
            ->with( 'Éléments de la fiche' )
              ->add( 'ExpTypeLogement'   , 'text', array( 'required' => false ) )
              ->add( 'ExpNbChambre'      , 'text', array( 'required' => false ) )
              ->add( 'ExpSurface'        , 'text', array( 'required' => false ) )
              ->add( 'ExpPiece'          , 'text', array( 'required' => false ) )
              ->add( 'ExpPrix'           , 'text', array( 'required' => false ) )
              ->add( 'ExclusInPrix'      , null, array( 'required' => false ) )
              ->add( 'ExpVille'          , 'text', array( 'required' => false ) )
              ->add( 'ExpArrondissement' , 'text', array( 'required' => false ) )
              ->add( 'ExpCP'             , 'text', array( 'required' => false ) )
              ->add( 'ExpDescription'    , 'text', array( 'required' => false ) )
              ->add( 'ExpAgence'         , 'text', array( 'required' => false ) )
            ->end()
            ;
        
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('agence.nom')
            ->add('id');
        
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('agence.nom')
            ->addIdentifier('StringTypeTransaction')
            ->addIdentifier('ShortUrlTraitement')
            ->addIdentifier('Actif', 'boolean');
        
    }

    public function validate(ErrorElement $errorElement, $object)
    {
    }

    public function getTemplate( $name )
    {
        switch( $name )
        {
            case 'edit':
                return 'VitelogeAdminBundle:Traitement:edit.html.twig';
                break;
            default:
                return parent::getTemplate( $name );
        }
        
    }


    public function getNewInstance()
    {
        $instance = parent::getNewInstance();
        $id_agence = $this->request->get('idAgence');
        if ( ! empty( $id_agence ) ) {
            $agence_repo = $this->getConfigurationPool()->getContainer()->get('doctrine')->getRepository( 'Viteloge\AdminBundle\Entity\Agence' );
            $agence = $agence_repo->find( $id_agence );
            $instance->agence = $agence;
        }
        return $instance;
    }
    
}