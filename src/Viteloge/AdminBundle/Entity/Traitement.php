<?php

namespace Viteloge\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Viteloge\AdminBundle\Entity\Traitement
 *
 * @ORM\Table(name="traitement")
 * @ORM\Entity(repositoryClass="Viteloge\AdminBundle\Entity\TraitementRepository")
 */
class Traitement
{

    public static $TypesTransaction = array( 'V' => 'Vente', 'L' => 'Location' );
    public static $TypesUrl = array( 'F' => 'Fiche annonce', 'R' => 'Liste de résultats' );
    public static $PublicationLimits = array( 0 => "Non", 1 => "Exclus de l'alerte mail", 2 => "Exclus de la redirection web" );

    public static $ModulesResultat = array( 'IncResultatDefaut.pl', 'LAFORET/IncResultatLAFORET.pl', 'ORPI/IncResultatORPI.pl', 'IncResultatAnnonceInListe.pl' );
    public static $ModulesFiche = array( 'IncAnnonceDefaut.pl', 'ORPI/IncAnnonceORPI.pl', 'REBOURS/IncAnnonceREBOURS.pl' );
    

    /**
     * @var integer $id
     *
     * @ORM\Column(name="IdTraitement", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    public function __construct()
    {
    }
    

    /**
     * @ORM\Column(name="UrlTraitement",type="text")
     */
    private $UrlTraitement; 
    /**
     * @ORM\Column(name="UrlInitSession",type="text")
     */
    private $UrlInitSession = ''; 
    /**
     * @ORM\Column(name="TypeUrlTraitement",type="string",length=1)
     */
    private $TypeUrlTraitement; 
    /**
     * @ORM\Column(name="StatutTraitement",type="smallint")
     */
    private $StatutTraitement = 0; 
    /**
     * @ORM\Column(name="TimestampPause",type="datetime")
     */
    private $TimestampPause; 
    /**
     * @ORM\Column(name="NbPauseTraitement",type="smallint")
     */
    private $NbPauseTraitement = 0; 
    /**
     * @ORM\Column(name="TypeTransactionTraitement",type="string",length=1)
     */
    private $TypeTransactionTraitement; 
    /**
     * @ORM\Column(name="TypeUrlSortieTraitement",type="string",length=1)
     */
    private $TypeUrlSortieTraitement; 
    /**
     * @ORM\Column(name="ModelUrlFicheTraitement",type="string",length=255)
     */
    private $ModelUrlFicheTraitement = ''; 
    /**
     * @ORM\Column(name="ModelUrlResultatTraitement",type="string",length=255)
     */
    private $ModelUrlResultatTraitement = ''; 
    /**
     * @ORM\Column(name="ModelUrlFicheFinal",type="string",length=255)
     */
    private $ModelUrlFicheFinal = ''; 
    /**
     * @ORM\Column(name="ModelUrlPageSuivante",type="text")
     */
    private $ModelUrlPageSuivante = ''; 
    /**
     * @ORM\Column(name="ModelUrlPhoto",type="string",length=255)
     */
    private $ModelUrlPhoto = ''; 
    /**
     * @ORM\Column(name="DateTraitement",type="datetime")
     */
    private $DateTraitement; 
    /**
     * @ORM\Column(name="TimeStampTraitement",type="datetime")
     */
    private $TimeStampTraitement; 
    /**
     * @ORM\Column(name="ModuleFicheTraitement",type="string",length=255)
     */
    private $ModuleFicheTraitement = ''; 
    /**
     * @ORM\Column(name="ModuleResultatTraitement",type="string",length=255)
     */
    private $ModuleResultatTraitement = ''; 
    /**
     * @ORM\Column(name="FilenameNoPhoto",type="string",length=255)
     */
    private $FilenameNoPhoto = ''; 
    /**
     * @ORM\Column(name="ExclusInPrix",type="string",length=20)
     */
    private $ExclusInPrix = ''; 
    /**
     * @ORM\Column(name="NbAnnonces",type="integer")
     */
    private $NbAnnonces = 0; 
    /**
     * @ORM\Column(name="NbErreurTraitement",type="string",length=255)
     */
    private $NbErreurTraitement = 0; 
    /**
     * @ORM\Column(name="LimitPublication",type="smallint")
     */
    private $LimitPublication = 0; 
    /**
     * @ORM\Column(name="Exclus",type="boolean")
     */
    private $Exclus = FALSE; 

    
    /**
     * @ORM\OneToOne(targetEntity="ExpressionReguliere",mappedBy="traitement", cascade={"persist", "merge","remove"})
     */
    private $expression;

    /**
     * @ORM\ManyToOne(targetEntity="Agence",inversedBy="traitements")
     * @ORM\JoinColumn(name="IdAgence", referencedColumnName="idAgence")
     */
    private $agence;

    /**
     * @ORM\OneToMany(targetEntity="Blacklist",mappedBy="traitement")
     */
    private $blacklists;

    /**
     * @ORM\OneToMany(targetEntity="Cycle",mappedBy="traitement")
     */
    private $cycles;
    
    /**
     * Methode magique __get()
     */
    public function __get($property)
    {
        if ( $property == 'StringTypeTransaction' ) {
            if ( isset( $this->TypeTransactionTraitement ) && ! empty( $this->TypeTransactionTraitement ) ) {
                return self::$TypesTransaction[ $this->TypeTransactionTraitement ];
            }
        }
        if ( property_exists( $this, $property ) ) {
            return $this->$property;
        }
        $this->createExpressionIfNecessary();
        if ( property_exists( $this->expression, $property ) ) {
            return $this->expression->$property;
        }
        return null;
    }

    public static $STRING_NONNULLABLE_KEYS = array( 'UrlInitSession', 'ModelUrlFicheTraitement', 'ModelUrlResultatTraitement', 'ModelUrlFicheTraitement' );
    public static $INT_NONNULLABLE_KEYS = array( );


    /**
     * Methode magique __isset()
     */
    public function __isset($name)
    {
        if ( $name == 'StringTypeTransaction' ) {
            return property_exists( $this, 'TypeTransactionTraitement' ) && ! empty( $this->TypeTransactionTraitement );
        }
        return property_exists($this, $name) || ( !is_null( $this->expression ) && property_exists( $this->expression, $name ) );
    }
    /**
     * Methode magique __set()
     */
    public function __set($property, $value)
    {
        if ( ! property_exists( $this, $property ) ) {
            $this->createExpressionIfNecessary();
            if ( property_exists( $this->expression, $property ) ) {
                $this->expression->$property = $value;
            }
        } else {
            if ( is_null( $value ) && ! is_null( $this->$property ) ) {
                $value = '';
            }
            $this->$property = $value;
        }
    }

    public function __toString() 
    {
        return
            "Traitement " . @self::$TypesTransaction[ $this->TypeTransactionTraitement ]
            . " " . $this->agence->nom
            ;
    }
    
    private function createExpressionIfNecessary()
    {
        if ( ! isset( $this->expression ) ) {
            $this->expression= new ExpressionReguliere();
            $this->expression->traitement = $this;
        }
    }

    public function getIdTraitement()
    {
        return $this->id;
    }

    public function getActif()
    {
        return !$this->Exclus;
    }
    

    public function getBaseUrlTraitement()
    {
        $url = explode( "#", $this->UrlTraitement );
        return $url[0];
    }
    public function getParametresPost()
    {
        $url = explode( "#", $this->UrlTraitement );
        if ( count( $url ) > 1 ) {
            return $url[1];
        }
        return '';
    }

    private function overwriteUrlTraitement( $values )
    {
        $url = explode( "#", $this->UrlTraitement );
        $original_values = array(
            'base' => count( $url ) > 0 ? $url[0] : '',
            'post' => count( $url ) > 1 ? $url[1] : '',
        );
        $new_values = array_merge( $original_values, $values );
        
        if ( empty( $new_values['post'] ) ) {
            $this->UrlTraitement = $new_values['base'];
        } else {
            $this->UrlTraitement = $new_values['base'] . '#' . $new_values['post'];
        }
    }
    
    
    public function setBaseUrlTraitement( $value ) 
    {
        $this->overwriteUrlTraitement( array( 'base' => $value ) );
    }
    public function setParametresPost( $value ) 
    {
        $this->overwriteUrlTraitement( array( 'post' => $value ) );
    }

    public function getShortUrlTraitement()
    {
        if ( strlen( $this->UrlTraitement ) > 100 ) {
            return substr( $this->UrlTraitement, 0, 100 ) . "...";
        }
        return $this->UrlTraitement;
    }

    public function reactivate( $full = false )
    {
        $this->TimeStampTraitement =  new \DateTime('2001-01-01');
        $this->Exclus = false;
        $this->NbPauseTraitement = 0;
        if ( $full ) {
            $this->TimestampPause = null;
            $this->StatutTraitement = 1;
        }
    }
    public function forceEnd()
    {
        $this->TimeStampTraitement =  new \DateTime('2001-01-01');
        $this->StatutTraitement = 1;
    }
    public function endPause()
    {
        $this->TimeStampTraitement =  new \DateTime('2001-01-01');
        $this->TimestampPause = null;
        $this->NbPauseTraitement = 0;
    }
    
    public function isPaused()
    {
        return $this->TimestampPause > new \DateTime('0000-00-00');
    }
    
    
}
