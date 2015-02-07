<?php namespace Packager\Commands;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class AddPackageTypeCommand extends BaseCommand {

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
		$this->prepare( $input, $output );

		$types      = $this->config->get( 'types' );
		$typeName   = $input->getArgument( 'name' );
		$typeSource = $input->getArgument( 'source' );

		if(
			array_key_exists( $typeName, $types )
			&&
			! $input->getOption( 'force' )
		)
		{
			$this->writeError( 'Package type already exists!' );
			exit( 1 );
		}

		$message = "New packages type '$typeName' with '$typeSource' source added";

		if( array_key_exists( $typeName, $types ) )
		{
			$message = "Packages type '$typeName' overwritten with new source '$typeSource'";
		}

		$types[ $typeName ] = $typeSource;

		$this->config->set( 'types', $types )->save();

		$this->writeInfo( $message );
	}

}