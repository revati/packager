<?php namespace Package\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ShowTypesListCommand extends Command {

	public function configure()
	{
		$this->setName( 'type:all' )->setDescription( 'Show package types list' );
	}

	public function execute( InputInterface $input, OutputInterface $output )
	{
		$config = new Config( $output );

		echo print_r( $config->get(), 1 );
		$types = $config->get( 'types' );

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