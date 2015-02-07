<?php namespace Packager;

use Closure;
use Packager\Config;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class BaseCommand extends Command {

	/**
	 * @type \Symfony\Component\Console\Input\InputInterface
	 */
	protected $input;

	/**
	 * @type \Symfony\Component\Console\Output\OutputInterface
	 */
	protected $output;

	/**
	 * @type \Packager\Config
	 */
	protected $config;

	/**
	 * @type \Symfony\Component\Finder\Finder
	 */
	protected $finder;

	/**
	 * @type \Symfony\Component\Filesystem\Filesystem
	 */
	protected $fs;

	protected function execute( InputInterface $input, OutputInterface $output )
	{
		$this->input  = $input;
		$this->output = $output;

		$this->config = new Config( $output );
		$this->finder = new Finder();
		$this->fs = new Filesystem();
	}

	protected function ask( $message, Closure $validation = null )
	{
		$dialog = $this->getHelper( 'dialog' );

		$message = "<question>$message:</question> ";

		if( is_null( $validation ) )
		{
			return $dialog->ask( $this->output, $message );
		}

		return $dialog->askAndValidate( $this->output, $message, $validation );
	}

	protected function writeInfo( $message )
	{
		$this->write( $message, 'info' );
	}

	protected function writeQuestion( $message )
	{
		$this->write( $message, 'question' );
	}

	protected function writeError( $message )
	{
		$this->write( $message, 'error' );
	}

	protected function writeComment( $message )
	{
		$this->write( $message, 'comment' );
	}

	protected function write( $message, $tag )
	{
		$this->output->writeln( "<$tag>$message</$tag>" );
	}

	protected function isAbsolutePath( $path )
	{
		return $this->fs->isAbsolutePath( $path );
	}
}