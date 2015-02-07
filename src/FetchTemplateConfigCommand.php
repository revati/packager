<?php namespace Packager;

use Exception;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class FetchTemplateConfigCommand extends BaseCommand {

	public function configure()
	{
		$this->setName( 'template:fetch' )
		     ->setDescription( 'Fetch package template config file' )
		     ->addArgument( 'name', InputArgument::REQUIRED, 'Template name' )
		     ->addArgument( 'source', InputArgument::REQUIRED, 'Template source' )
		     ->addOption( 'force', 'f', InputOption::VALUE_NONE, 'Overwrite existing packages template if exists' );
	}

	public function execute( InputInterface $input, OutputInterface $output )
	{
		parent::execute( $input, $output );

		$name   = $input->getArgument( 'name' );
		$source = $input->getArgument( 'source' );

		if(
			$this->config->hasTemplate( $name )
			&&
			! $input->getOption( 'force' )
		)
		{
			throw new Exception( 'Template already exists!' );
		}

		$message = "New template '$name' with '$source' source added";

		if( $this->config->hasTemplate( $name ) )
		{
			$message = "Template '$name' overwritten with new source '$source'";
		}

		$source = $this->verifySource( $source );

		$this->config->setTemplate( $name, $source )->save();

		$this->writeInfo( $message );
	}

	protected function verifySource( $source )
	{
		$hasSource = @file_get_contents( $source );

		if( $hasSource === false )
		{
			$this->writeError( "Unexisting source file" );
			exit( 1 );
		}

		if(
			$this->isUrl( $source )
			||
			$this->isFullPath( $source )
		) {
			return $source;
		}

		return getcwd() . '/' . $source;
	}

	protected function isUrl( $source )
	{
		return filter_var( $source, FILTER_VALIDATE_URL );
	}

	protected function isFullPath( $source )
	{
		return ( substr( $source, 0, 1 ) === '/' );
	}
}