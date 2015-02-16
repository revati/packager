<?php namespace Revati\Packager\Template;

use Exception;
use Revati\Packager\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InitCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'template:init';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Initialize template';

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return [
			[ 'name', InputArgument::OPTIONAL, 'Template name' ],
			[ 'description', InputArgument::OPTIONAL, 'Template description' ],
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
		$this->comment( 'Creating template...' );

		$name = $this->argument( 'name' );
		$path = current_path( $name );

		if(
			is_dir( $path )
			&&
			! empty( $name )
		)
		{
			throw new Exception( 'Folder is taken' );
		}

		if(
			empty( $name )
			&&
		    get_config( make_path( $path, 'packager.json' ), false )
		)
		{
			throw new Exception( 'Packager is initialized' );
		}

		if( ! empty( $name ) )
		{
			mkdir( $path );
		}

		$this->saveTemplateConfig( $path );

		$this->info( "✔ Template created" );
	}

	protected function isCurrentDirectory()
	{
		return empty( $this->argument( 'name' ) );
	}

	protected function isPackagerInitialized()
	{
		$path = current_path( $this->argument( 'name' ) );

		return file_exists( make_path( $path, 'packager.json' ) );
	}

	/**
	 * Add global config to template config
	 *
	 * @param $path
	 *
	 * @throws \Exception
	 */
	protected function saveTemplateConfig( $path )
	{
		$templateConfig = get_config( stub_path( 'local-config.json' ) );

		$templateConfig[ 'name' ]        = $this->argument( 'name' );
		$templateConfig[ 'description' ] = $this->argument( 'description' );

		if( ! empty( $this->getConfig( 'author' ) ) )
		{
			$templateConfig[ 'authors' ][ ] = [
				'name'  => $this->getConfig( 'author.name' ),
				'email' => $this->getConfig( 'author.email' ),
			];
		}

		put_config( make_path( $path, 'packager.json' ), $templateConfig );
	}
}