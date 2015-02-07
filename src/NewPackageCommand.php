<?php namespace Packager;

use Exception;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class NewPackageCommand extends BaseCommand {

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
		parent::execute( $input, $output );

		$packageDirectory = getcwd() . DIRECTORY_SEPARATOR . $input->getArgument( 'name' );
		$this->verifyPackageDoesntExist( $packageDirectory );

		$this->initPackage( $packageDirectory, $this->getPackageSource() );
	}

	protected function getPackageSource()
	{
		$templateName = $this->input->getArgument( 'type' );

		if( ! $templateName )
		{
			$templateName = $this->config->getDefaultTemplate();
		}

		return $this->config->getTemplate( $templateName );
	}

	/**
	 * Verify that the application does not already exist.
	 *
	 * @param string $directory
	 *
	 * @throws \Exception
	 * @return void
	 */
	protected function verifyPackageDoesntExist( $directory )
	{
		if( is_dir( $directory ) )
		{
			throw new Exception( "Folder is taken" );
		}
	}

	protected function initPackage( $directory, $source )
	{
		$config = $this->getTemplateConfig( $source );

		mkdir( $directory );

		foreach( $config[ 'folders' ] as $folder )
		{
			mkdir( $directory . DIRECTORY_SEPARATOR . $folder );
		}

		foreach( $config[ 'files' ] as $file => $contents )
		{
			file_put_contents( $directory . DIRECTORY_SEPARATOR . $file, $contents );
		}
	}

	protected function getTemplateConfig( $source )
	{
		$config = @file_get_contents( $source );

		if( $config === false )
		{
			throw new Exception( 'Package template config is missing' );
		}

		$config = strtr( $config, $this->collectReplaceData() );

		return json_decode( $config, true );
	}

	protected function collectReplaceData()
	{
		$packageName   = $this->input->getArgument( 'name' );
		$packageAuthor = $this->config->getAuthorName();

		return [
			'__author_name__'         => $packageAuthor,
			'__author_email__'        => $this->config->getAuthorEmail(),
			'__package_name__'        => $packageName,
			'__package_description__' => $this->input->getOption( 'description' ),
			'__package_class__'       => $this->getClassFromString( $packageName ),
			'__author_class__'        => $this->getClassFromString( $packageAuthor ),
		];
	}

	protected function getClassFromString( $packageName )
	{
		$words = strtr( $packageName, [ '-' => ' ', '_' => ' ' ] );

		return strtr( ucwords( $words ), [ ' ' => '' ] );
	}
}
