<?php namespace Packager\Commands;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ShowTypesListCommand extends BaseCommand {

	public function configure()
	{
		$this->setName( 'type:all' )->setDescription( 'Show package types list' );
	}

	public function execute( InputInterface $input, OutputInterface $output )
	{
		$this->prepare( $input, $output );

		$types = $this->config->get( 'types' );

		if( empty( $types ) )
		{
			$output->writeln( "No package types has been configured" );
			return;
		}

		$table = $this->getHelper('table');

		$table->setHeaders( [ "Name", "Source" ] );

		foreach( $types as $name => $source )
		{
			$table->addRow( [ $name, $source ] );
		}

		$table->render( $output );
	}

}