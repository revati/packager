<?php namespace Package\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class AddPackageTypeCommand extends Command {

	public function configure()
	{
		$this->setName( 'type:add' )
		     ->setDescription( 'Add new package type' )
		     ->addArgument( 'name', InputArgument::REQUIRED, 'Type name' )
		     ->addArgument( 'source', InputArgument::REQUIRED, 'Type source' )
		     ->addOption( 'force', null, InputOption::VALUE_NONE, 'Overwrite existing package type if exists' );
	}

	public function execute( InputInterface $input, OutputInterface $output )
	{
		$config = new Config( $output );
		$types  = $config->get( 'types' );

		$typeName   = $input->getArgument( 'name' );
		$typeSource = $input->getArgument( 'source' );

		if(
			array_key_exists( $typeName, $types )
			&&
		    ! $input->getOption('force')
		)
		{
			$output->writeln( '<error>Package type already exists!</error> Use --force to overwrite' );
			exit( 1 );
		}

		$message = "New packages type '$typeName' with '$typeSource' source added";

		if( array_key_exists( $typeName, $types ) )
		{
			$message = "Packages type '$typeName' overwritten with new source '$typeSource'";
		}
		$types[ $typeName ] = $typeSource;

		$config->set( 'types', $types )->save();

		$output->writeln( "<info>{$message}</info>" );
	}

}