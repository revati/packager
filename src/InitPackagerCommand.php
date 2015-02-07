<?php namespace Packager;

use Exception;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InitPackagerCommand extends BaseCommand {

	/**
	 * Configure the command options.
	 *
	 * @return void
	 */
	protected function configure()
	{
		$this->setName( 'init' )
		     ->setDescription( 'Create packager config stub' );
	}

	/**
	 * Execute the command.
	 *
	 * @param  \Symfony\Component\Console\Input\InputInterface   $input
	 * @param  \Symfony\Component\Console\Output\OutputInterface $output
	 *
	 * @throws \Exception
	 * @return void
	 */
	public function execute( InputInterface $input, OutputInterface $output )
	{
		parent::execute( $input, $output );

		if( is_dir( config_path() ) )
		{
			throw new Exception( "Packager has already been initialized." );
		}

		$this->writeComment( 'Initializing packager config...' );

		mkdir( config_path() );
		mkdir( cache_path() );

		copy( __DIR__ . '/stubs/Packager.json', config_path() . '/Packager.json' );

		$this->writeInfo( 'Packager config stub created at: ' . config_path() );
	}
}