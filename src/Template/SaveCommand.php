<?php namespace Revati\Packager\Template;

use Exception;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Revati\Packager\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SaveCommand extends Command {

	/**
	 * Template configuration
	 *
	 * @type array
	 */
	protected $localConfig = [ ];

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'template:save';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Save current directory as package template';

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return [
			[ 'force', 'f', InputOption::VALUE_NONE, 'Overwrite existing template if exists' ],
			[ 'default', 'd', InputOption::VALUE_NONE, 'Set template as default' ],
		];
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
	protected function execute( InputInterface $input, OutputInterface $output )
	{
		$this->comment( 'Saving template...' );

		$this->validateTemplateName();

		$this->makeTemplateConfig();

		$this->registerTemplate();

		$this->info( "✔ Template saved" );
	}

	protected function validateTemplateName()
	{
		$name = $this->getLocalConfig( 'name' );

		if(
			$this->getConfig( "templates.$name" )
			&&
			! $this->option( 'force' )
		)
		{
			throw new Exception( 'Template is taken. Use --force if you want to overwrite' );
		}
	}

	protected function makeTemplateConfig()
	{
		$templateConfig = $this->getLocalConfig();

		list( $folders, $files ) = $this->readDirectory();

		$templateConfig[ 'folders' ] = $folders;
		$templateConfig[ 'files' ]   = $files;

		$this->localConfig = $templateConfig;

		$name         = $this->getLocalConfig( 'name' );
		$templatePath = make_path( local_path( 'templates', "$name.json" ) );
		put_config( $templatePath, $templateConfig );
	}

	protected function registerTemplate()
	{
		$name         = $this->getLocalConfig( 'name' );
		$templatePath = make_path( local_path( 'templates', "$name.json" ) );

		$this->config[ 'templates' ][ $name ] = $templatePath;
		$this->saveConfig();
	}

	protected function getLocalConfig( $key = null )
	{
		if( ! $this->localConfig )
		{
			$this->localConfig = get_config( current_path( 'packager.json' ) );
		}

		if( $key )
		{
			return array_get( $this->localConfig, $key );
		}

		return $this->localConfig;
	}

	protected function readDirectory()
	{
		$excludePaths = [ current_path() ];

		$folders = [ ];
		$files   = [ ];

		foreach( new RecursiveIteratorIterator( new RecursiveDirectoryIterator( current_path() ) ) as $file )
		{
			if( in_array( $file->getPath(), $excludePaths ) )
			{
				continue;
			}

			if( $file->isDir() )
			{
				$folders[ $file->getPath() ] = '';

				continue;
			}

			$files[ $file->getRealPath() ] = file_get_contents( $file->getRealPath() );
		}

		return [ array_keys( $folders ), $files ];
	}
}