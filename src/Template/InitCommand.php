<?php namespace Revati\Packager\Template;

use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InitCommand extends Command {

	public function configure()
	{
		$this->setName( 'template:init' )
		     ->setDescription( 'Initialize template' )
		     ->addArgument( 'name', InputArgument::REQUIRED, 'Template name' );
	}

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

		copy( stub_path( 'local-config.json' ), make_path( $templatePath, 'packager.json' ) );

		$output->writeln( '<info>✔ Template initialized</info>' );
	}

}