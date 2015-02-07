<?php namespace Packager\Commands\Bootstrap;

use Packager\Commands\BaseCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class FetchBootstrapConfigCommand extends BaseCommand {

	public function configure()
	{
		$this->setName( 'bootstrap:fetch' )
		     ->setDescription( 'Fetch package bootstrap config file' )
		     ->addArgument( 'name', InputArgument::REQUIRED, 'Bootstrap name' )
		     ->addArgument( 'source', InputArgument::REQUIRED, 'Bootstrap source' )
		     ->addOption( 'force', 'f', InputOption::VALUE_NONE, 'Overwrite existing packages bootstrap if exists' );
	}

	public function execute( InputInterface $input, OutputInterface $output )
	{
		$this->prepare( $input, $output );

		$name   = $input->getArgument( 'name' );
		$source = $input->getArgument( 'source' );

		if(
			$this->config->hasBootstrap( $name )
			&&
			! $input->getOption( 'force' )
		)
		{
			$this->writeError( 'Bootstrap already exists!' );
			exit( 1 );
		}

		$message = "New bootstrap '$name' with '$source' source added";

		if( $this->config->hasBootstrap( $name ) )
		{
			$message = "Bootstrap '$name' overwritten with new source '$source'";
		}

		$source = $this->verifySource( $source );

		$this->config->setBootstrap( $name, $source )->save();

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