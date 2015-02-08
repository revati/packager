<?php namespace Revati\Packager\Template;

use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InitCommand extends Command {

	/**
	 * Configure the command options.
	 *
	 * @return void
	 */
	public function configure()
	{
		$this->setName( 'template:init' )
		     ->setDescription( 'Initialize template' )
		     ->addArgument( 'name', InputArgument::REQUIRED, 'Template name' );
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
		$templateName = $input->getArgument( 'name' );
		$templatePath = current_path( $templateName );

		if( is_dir( $templatePath ) )
		{
			throw new Exception( 'Folder is taken' );
		}

		$output->writeln( '<comment>Creating template...</comment>' );

		mkdir( $templatePath );

		$templateConfigPath = make_path( $templatePath, 'packager.json' );

		copy( stub_path( 'local-config.json' ), $templateConfigPath );

		$this->saveTemplateConfig( $templateConfigPath );

		$output->writeln( '<info>✔ Template initialized</info>' );
	}

	/**
	 * Add global config to template config
	 *
	 * @param $templateConfigPath
	 */
	protected function saveTemplateConfig( $templateConfigPath )
	{
		$globalConfig = get_global_config();

		if( empty( $globalConfig[ 'author' ] ) )
		{
			return;
		}

		$templateConfig = get_config( $templateConfigPath );

		$templateConfig[ 'authors' ][ ] = [
			'name'  => array_get( 'name', $globalConfig[ 'author' ] ),
			'email' => array_get( 'email', $globalConfig[ 'author' ] ),
		];

		put_config( $templateConfigPath, $templateConfig );
	}
}