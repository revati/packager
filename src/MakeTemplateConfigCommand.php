<?php namespace Packager;

use Exception;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\SplFileInfo;

class MakeTemplateConfigCommand extends BaseCommand {

	protected $newConfig = [
			'name'        => '',
			'description' => '',
			'files'       => [ ],
			'folders'     => [ ],
		];

	protected $ignorePaths = [ "vendor", ".git", "packager.json" ];

	public function configure()
	{
		$this->setName( 'template:make' )
		     ->setDescription( 'Make package template config file from folder' )
		     ->addArgument( 'name', InputArgument::REQUIRED, 'Template name' )
		     ->addArgument( 'source', InputArgument::OPTIONAL, 'Template source' )
		     ->addOption( 'description', 'd', InputOption::VALUE_OPTIONAL, 'Template description' )
		     ->addOption( 'force', 'f', InputOption::VALUE_NONE, 'Overwrite existing packages template if exists' )
		     ->addOption( 'default', null, InputOption::VALUE_NONE, 'Mark as default template' );
	}

	public function execute( InputInterface $input, OutputInterface $output )
	{
		parent::execute( $input, $output );

		$name   = $this->input->getArgument( 'name' );
		$source = $this->input->getArgument( 'source' );

		if(
			$this->config->hasTemplate( $name )
			&&
			! $input->getOption( 'force' )
		)
		{
			throw new Exception( 'Template already exists!' );
		}

		$this->prepareConfig( $this->getAbsolutePath( $source ) );

		$this->saveConfig();

		$this->config->setTemplate( $name, $this->getTemplatePath() );

		if( $input->getOption( 'default' ) )
		{
			$this->config->setDefaultTemplate( $name );
		}

		$this->config->save();

		$this->writeInfo( "Template config generated as '{$name}'" );
	}

	protected function prepareConfig( $directory )
	{
		$this->newConfig[ 'name' ]        = $this->input->getArgument( 'name' );
		$this->newConfig[ 'description' ] = $this->input->getOption( 'description' );
		$this->newConfig[ 'config' ]      = $this->getPackageConfig( $directory );

		$files = $this->finder
			->ignoreUnreadableDirs()
			->exclude( $this->ignorePaths )
			->in( $directory );

		foreach( $files as $file )
		{
			$this->prepareFile( $file );
		}
	}

	protected function saveConfig()
	{
		file_put_contents( $this->getTemplatePath(), json_encode( $this->newConfig, JSON_PRETTY_PRINT ) );
	}

	protected function getTemplatePath()
	{
		$name = $this->input->getArgument( 'name' ) . '.json';

		return cache_path() . DIRECTORY_SEPARATOR . $name;
	}

	protected function prepareFile( SplFileInfo $file )
	{
		$filePath = $file->getRelativePathname();

		if( $file->isDir() )
		{
			$this->newConfig[ 'folders' ][ ] = $filePath;

			return;
		}

		$fileContents = file_get_contents( $file->getRealPath() );

		$this->newConfig[ 'files' ][ $filePath ] = $fileContents;
	}

	protected function getPackageConfig( $directory )
	{
		$configFile = file_get_contents( $directory . DIRECTORY_SEPARATOR . 'packager.json' );

		if( empty( $configFile ) )
		{
			return [];
		}

		return json_decode( $configFile, true );
	}
}