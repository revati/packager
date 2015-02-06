<?php namespace Package\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class NewPackageCommand extends Command {

	protected $config = null;

	public function configure()
	{
		$this->setName( 'new' )
		     ->setDescription( 'Creates new packages boilerplate from type' )
		     ->addArgument( 'name', InputArgument::REQUIRED, 'Packages name' );
	}

	public function execute( InputInterface $input, OutputInterface $output )
	{
		$packageName = $input->getArgument( 'name' );
		$packageDirectory = getcwd() . '/' . $packageName;

		$this->verifyPackageDoesntExist( $packageDirectory, $output );

		$this->initPackage( $packageDirectory, $packageName );

		$output->writeln( $packageName );
	}

	/**
	 * Verify that the application does not already exist.
	 *
	 * @param string          $directory
	 * @param OutputInterface $output
	 *
	 * @return void
	 */
	protected function verifyPackageDoesntExist( $directory, OutputInterface $output )
	{
		if( is_dir( $directory ) )
		{
			$output->writeln( '<error>Package already exists!</error>' );
			exit( 1 );
		}
	}

	protected function initPackage( $directory, $packageName )
	{
		mkdir( $directory );
		mkdir( $directory . '/src/' );

		$composer = file_get_contents ( __DIR__ . '/stubs/composer.json' );

		$composer = strtr( $composer, [ '{packageName}' => $packageName ] );

		file_put_contents( $directory . '/composer.json', $composer );
	}

	protected function getConfig( OutputInterface $output )
	{
		if( is_null( $this->config ) )
		{
			$configFile = realpath( __DIR__ . '/../config.json' );

			if( empty( $configFile ) )
			{
				$output->writeln( '<error>Config file is missing</error>' );
				exit( 1 );
			}

			$this->config = json_decode( file_get_contents ( $configFile ) );

			if( $this->config === false )
			{
				$output->writeln( '<error>Config file is corrupt</error>' );
				exit( 1 );
			}
		}

		return $this->config;
	}
}
