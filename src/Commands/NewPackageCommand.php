<?php namespace Packager\Commands;

use Packager\BaseCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class NewPackageCommand extends BaseCommand {

	protected $config = null;

	public function configure()
	{
		$this->setName( 'new' )
		     ->setDescription( 'Creates new packages boilerplate from type' )
		     ->addArgument( 'name', InputArgument::REQUIRED, 'Package name' )
		     ->addArgument( 'type', InputArgument::OPTIONAL, 'Package type' )
		     ->addOption( 'description', null, InputOption::VALUE_OPTIONAL, 'Package description' );
	}

	public function execute( InputInterface $input, OutputInterface $output )
	{
		$this->prepare( $input, $output );

		$packageDirectory = getcwd() . '/' . $input->getArgument( 'name' );
		$this->verifyPackageDoesntExist( $packageDirectory );

		$this->initPackage( $packageDirectory, $this->getPackageSource() );
	}

	protected function getPackageSource()
	{
		$type = $this->input->getArgument( 'type' );

		if( ! $type )
		{
			$type = $this->config->get( 'defaultType' );
		}

		$types = $this->config->get( 'types' );

		if( ! array_key_exists( $type, $types ) )
		{
			$this->writeError( "Undefined type provided: $type" );
			exit( 1 );
		}

		return $types[ $type ];
	}

	/**
	 * Verify that the application does not already exist.
	 *
	 * @param string $directory
	 *
	 * @return void
	 */
	protected function verifyPackageDoesntExist( $directory )
	{
		if( is_dir( $directory ) )
		{
			$this->output->writeln( '<error>Package already exists!</error>' );
			exit( 1 );
		}
	}

	protected function initPackage( $directory, $source )
	{
		mkdir( $directory );

		$composer = file_get_contents( __DIR__ . '/../stubs/composer.json' );

		$composer = strtr( $composer, $this->collectReplaceData() );

		file_put_contents( $directory . '/composer.json', $composer );
	}

	protected function collectReplaceData()
	{
		$packageName = $this->input->getArgument( 'name' );

		return [
			'{author}'             => $this->config->get( 'author' ),
			'{packageName}'        => $packageName,
			'{packageDescription}' => $this->input->getOption( 'description' ),
			'{packageClass}'       => $this->getPackageRootClass( $packageName ),
		];
	}

	protected function getPackageRootClass( $packageName )
	{
		$words = strtr( $packageName, [ '-' => ' ', '_' => ' ' ] );

		return strtr( ucwords( $words ), [ ' ' => '' ] );
	}
}
