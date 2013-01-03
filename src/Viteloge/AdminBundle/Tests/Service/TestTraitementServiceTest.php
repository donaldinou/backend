<?php

namespace Viteloge\AdminBundle\Tests\Service;

//use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use Viteloge\AdminBundle\Service\TestTraitementService;

class TestTraitementServiceUnderTest extends TestTraitementService
{
    public function run_build_custom_url( $a, $b )
    {
        return $this->build_custom_url( $a, $b );
    }
    
}


class TestTraitementServiceTest extends \PHPUnit_Framework_TestCase
{
    public function testBasic()
    {
        $tester = new TestTraitementServiceUnderTest( null );
        $this->assertEquals( $tester->run_build_custom_url( '', 'Appartement-Marseille-8888883.htm' ), 'Appartement-Marseille-8888883.htm' );
    }

    public function testWithRondAPattes()
    {
        $tester = new TestTraitementServiceUnderTest( null );
        $this->assertEquals( $tester->run_build_custom_url( 'http://www.campagne-normande.fr¤', '/immobilier-normandie/maison-entre-gournay-et-rouen-1046' ), 'http://www.campagne-normande.fr/immobilier-normandie/maison-entre-gournay-et-rouen-1046' );
    }
    public function testWithRondAPattesAuMilieu()
    {
        $tester = new TestTraitementServiceUnderTest( null );
        $this->assertEquals( $tester->run_build_custom_url( 'http://www.c-loue-c-vendu.com/fiche-¤.html', '12561' ), 'http://www.c-loue-c-vendu.com/fiche-12561.html' );
    }

    public function _testJJP()
    {
        $tester = new TestTraitementServiceUnderTest( null );
        $tester->setUrls( 'http://www.jjp-immo.com/index.php?module=annonce#form=particulier&agence=3&cat=5' );
        $this->assertEquals( $tester->run_build_custom_url( '{$BASE_URL}?{clean_url($BASE_URL_GET, \'p\')}&p=¤#{$BASE_URL_POST}', '2' ), 'http://www.jjp-immo.com/index.php?module=annonce&p=2#form=particulier&agence=3&cat=5' );
    }

    public function testSimpleClean()
    {
        $tester = new TestTraitementServiceUnderTest( null );
        $tester->setUrls( 'http://www.immo-goult.com/annonces-immobilieres/recherche-multicritere/listing.html#origin=50&page=1&sort=7&sort_invert=2&typeannonce=0&typebiens%5B%5D=7&typebiens%5B%5D=8&typebiens%5B%5D=6&page=1' );
        $this->assertEquals( $tester->run_build_custom_url( '{clean_url($FULL_URL,\'page\')}&page=¤', '2' ), 'http://www.immo-goult.com/annonces-immobilieres/recherche-multicritere/listing.html#origin=50&sort=7&sort_invert=2&typeannonce=0&typebiens%5B%5D=7&typebiens%5B%5D=8&typebiens%5B%5D=6&page=2' );
    }
    
    public function testAutresRefsUrls()
    {
        $tester = new TestTraitementServiceUnderTest( null );
        $tester->setUrls( 'http://www.gi66.com/gi-3000.php?recherche=1#type=-1&prixmin=-1&prixmax=-1&ville=-1&Action=A+LOUER&Sender=immo_loc&RechercheOK=1' );
        $this->assertEquals( $tester->run_build_custom_url( '{$BASE_URL}?page=¤#{$BASE_URL_POST}', '1' ), 'http://www.gi66.com/gi-3000.php?page=1#type=-1&prixmin=-1&prixmax=-1&ville=-1&Action=A+LOUER&Sender=immo_loc&RechercheOK=1' );
    }
}
