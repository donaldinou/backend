<?
namespace Viteloge\AdminBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class TestCommand extends ContainerAwareCommand
{
	/*
	* configuration
	*/
	protected function configure()
    {
        $this
            ->setName('viteloge:admin:test')
            ->setDescription('Test en tout genre')
            ->addArgument('IdTraitement', InputArgument::OPTIONAL, 'id traitement') 
            ->addArgument('arg2', InputArgument::OPTIONAL, 'argument') 
        ;
    }

	/*
	* execute
	* php app/console acreat:shopping:import-categories app/files/categories.txt
	*/
	protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get( 'doctrine.orm.entity_manager' );
        $repo = $em->getRepository( 'Viteloge\AdminBundle\Entity\Traitement' );
        $t = $repo->find( $input->getArgument( 'IdTraitement' ) );
        if ( is_null( $t ) ) {
            $output->writeln( "Unable to find traitement" );
        } else {
            $t->ExpLiensFiche = "test";
            $em->persist( $t );
            $em->flush();
        }
        
        
	}
	
	
}