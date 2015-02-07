<?php namespace Packager\Commands\Bootstrap;

use Packager\Commands\BaseCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ShowBootstrapsListCommand extends BaseCommand {

	public function configure()
	{
		$this->setName( 'bootstrap:all' )->setDescription( 'Show bootstraps list' );
	}

	public function execute( InputInterface $input, OutputInterface $output )
	{
		$this->prepare( $input, $output );

		$bootstraps = $this->config->getBootstrap();

		if( empty( $bootstraps ) )
		{
			$output->writeln( "No bootstraps has been configured" );
			return;
		}

		$table = $this->getHelper('table');

		$table->setHeaders( [ "Name", "Source" ] );

		foreach( $bootstraps as $name => $source )
		{
			$table->addRow( [ $name, $source ] );
		}

		$table->render( $output );
	}
}