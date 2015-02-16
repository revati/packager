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
			[ 'name', InputArgument::REQUIRED, 'Template name' ],
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
		$name = $this->argument( 'name' );
		$path = current_path( $name );

		if( is_dir( $path ) )
		{
			throw new Exception( 'Folder is taken' );
		}

		$this->comment( 'Creating template...' );

		mkdir( $path );

		$this->saveTemplateConfig( $path );

		$this->info( "✔ Template '$name' created" );
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
		$globalConfig   = get_global_config();
		$templateConfig = get_config( stub_path( 'local-config.json' ) );

		$templateConfig[ 'name' ]        = $this->argument( 'name' );
		$templateConfig[ 'description' ] = $this->argument( 'description' );

		if( ! empty( $globalConfig[ 'author' ] ) )
		{
			$templateConfig[ 'authors' ][ ] = [
				'name'  => array_get( 'name', $globalConfig[ 'author' ] ),
				'email' => array_get( 'email', $globalConfig[ 'author' ] ),
			];
		}

		put_config( make_path( $path, 'packager.json' ), $templateConfig );
	}
}