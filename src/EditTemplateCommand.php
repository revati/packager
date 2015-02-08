<?php namespace Packager;

use Exception;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class EditTemplateCommand extends BaseCommand {

	public function configure()
	{
		$this->setName( 'template:edit' )
		     ->setDescription( 'Recreates raw template' )
		     ->addArgument( 'template', InputArgument::REQUIRED, 'Template name' )
		     ->addArgument( 'destination', InputArgument::OPTIONAL, 'Destination folder' );
	}

	public function execute( InputInterface $input, OutputInterface $output )
	{
		parent::execute( $input, $output );

		$templateDirectory = $this->getDestination();
		$this->verifyPackageDoesntExist( $templateDirectory );

		$templateName = $this->config->getTemplate( $input->getArgument( 'template' ) );
		$this->initTemplate( $templateDirectory, $templateName );

		$this->writeInfo( 'Template boilerplate recreated in ' . $templateDirectory );
	}

	protected function initTemplate( $directory, $source )
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

	protected function getPackageSource()
	{
		$templateName = $this->input->getArgument( 'template' );

		return $this->config->getTemplate( $templateName );
	}

	protected function getDestination()
	{
		$destination = $this->input->getArgument( 'destination' );

		if( ! $destination )
		{
			$template    = $this->input->getArgument( 'template' );

			return getcwd() . DIRECTORY_SEPARATOR . $template;
		}

		return $this->getAbsolutePath( $destination );
	}

	protected function getTemplateConfig( $source )
	{
		$config = @file_get_contents( $source );

		if( $config === false )
		{
			throw new Exception( 'Package template config is missing' );
		}

		return json_decode( $config, true );
	}

}