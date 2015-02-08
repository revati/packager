<?php namespace Revati\Packager;

use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InitCommand extends Command {

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
	 * Executes the current command.
	 *
	 * @param \Symfony\Component\Console\Input\InputInterface   $input
	 * @param \Symfony\Component\Console\Output\OutputInterface $output
	 *
	 * @throws \Exception
	 * @return void
	 */
	public function execute( InputInterface $input, OutputInterface $output )
	{
		if( $this->isInitialized() )
		{
			throw new Exception( 'Packager is initialized' );
		}

		$output->writeln( '<comment>Initializing packager...</comment>' );

		mkdir( local_path() );
		mkdir( local_path( 'templates' ) );

		copy( stub_path( 'global-config.json' ), local_path( 'packager.json' ) );

		$output->writeln( '<info>✔ Package initialized</info>' );
	}

	/**
	 * Checks if packager directory is created
	 *
	 * @return bool
	 */
	protected function isInitialized()
	{
		return is_dir( local_path() );
	}
}