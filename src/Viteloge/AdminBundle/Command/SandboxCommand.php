<?php
namespace Viteloge\AdminBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SandboxCommand extends ContainerAwareCommand
{
	/*
	* configuration
	*/
	protected function configure()
    {
        $this
            ->setName('viteloge:admin:sandbox')
            ->setDescription('Test en tout genre')
            ->addArgument('IdTraitement', InputArgument::OPTIONAL, 'id traitement') 
            ->addArgument('IdAgence', InputArgument::OPTIONAL, 'id_agence') 
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
        $ag_repo = $em->getRepository( 'Viteloge\AdminBundle\Entity\Agence' );
        $ann_repo = $em->getRepository( 'Viteloge\AdminBundle\Entity\Annonce' );
        if ( $input->getArgument( 'IdTraitement' ) ) {
            $t = $repo->find( $input->getArgument( 'IdTraitement' ) );
            if ( is_null( $t ) ) {
                $output->writeln( "Unable to find traitement" );
                return;
            }
        } else if ( $input->getArgument( 'IdAgence' ) ) {
            $a = $ag_repo->find ( $input->getArgument( 'IdAgence' ) );
            if ( is_null( $a ) ) {
                $output->writeln( "Unable to find agence" );
                return;
            }
            print_r( $ann_repo->getCountByInsee( $a ) );
        }
        
	}
}