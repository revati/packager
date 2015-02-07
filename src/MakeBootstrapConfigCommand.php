<?php namespace Packager;

use Exception;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\SplFileInfo;

class MakeBootstrapConfigCommand extends BaseCommand {

	protected $newConfig
		                   = [
			'name'        => '',
			'description' => '',
			'files'       => [ ],
			'folders'     => [ ],
		];

	protected $ignorePaths = [ "vendor", ".git" ];

	public function configure()
	{
		$this->setName( 'bootstrap:make' )
		     ->setDescription( 'Make package bootstrap config file from folder' )
		     ->addArgument( 'name', InputArgument::REQUIRED, 'Bootstrap name' )
		     ->addArgument( 'source', InputArgument::OPTIONAL, 'Bootstrap source' )
		     ->addOption( 'description', 'd', InputOption::VALUE_OPTIONAL, 'Bootstrap description' )
		     ->addOption( 'force', 'f', InputOption::VALUE_NONE, 'Overwrite existing packages bootstrap if exists' );
	}

	public function execute( InputInterface $input, OutputInterface $output )
	{
		parent::execute( $input, $output );

		$name = $this->input->getArgument( 'name' );

		if(
			$this->config->hasBootstrap( $name )
			&&
			! $input->getOption( 'force' )
		)
		{
			throw new Exception( 'Bootstrap already exists!' );
		}

		$this->prepareConfig( $this->fetchDirectory() );

		$this->saveConfig();

		$this->config->setBootstrap( $name, $this->getBootstrapPath() )->save();

		$this->writeInfo( "Bootstrap config generated as '{$name}'" );
	}

	protected function prepareConfig( $directory )
	{
		$this->newConfig[ 'name' ]        = $this->input->getArgument( 'name' );
		$this->newConfig[ 'description' ] = $this->input->getOption( 'description' );

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
		file_put_contents( $this->getBootstrapPath(), json_encode( $this->newConfig, JSON_PRETTY_PRINT ) );
	}

	protected function getBootstrapPath()
	{
		$name = $this->input->getArgument( 'name' ) . '.json';

		return cache_path() . DIRECTORY_SEPARATOR . $name;
	}

	protected function prepareFile( SplFileInfo $file )
	{
		$filePath = $file->getRelativePathname();

		if( $file->isDir() )
		{
			$this->newConfig[ 'folders' ] = $filePath;

			return;
		}

		$fileContents = file_get_contents( $file->getRealPath() );

		$this->newConfig[ 'files' ][ $filePath ] = $fileContents;
	}

	protected function fetchDirectory()
	{
		$directory = $this->input->getArgument( 'source' );

		if( is_null( $directory ) )
		{
			return getcwd();
		}

		if( $this->isAbsolutePath( $directory ) )
		{
			return $directory;
		}

		return getcwd() . '/' . $directory;
	}
}