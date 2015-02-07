<?php namespace Packager;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ShowTemplatesListCommand extends BaseCommand {

	public function configure()
	{
		$this->setName( 'template:all' )->setDescription( 'Show templates list' );
	}

	public function execute( InputInterface $input, OutputInterface $output )
	{
		parent::execute( $input, $output );

		$templates = $this->config->getTemplate();

		if( empty( $templates ) )
		{
			$output->writeln( "No templates has been configured" );
			return;
		}

		$table = $this->getHelper('table');

		$table->setHeaders( [ "Name", "Source" ] );

		foreach( $templates as $name => $source )
		{
			$table->addRow( [ $name, $source ] );
		}

		$table->render( $output );
	}
}