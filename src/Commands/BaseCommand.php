<?php namespace Packager\Commands;

use Closure;
use Packager\Config;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

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

	protected function prepare( InputInterface $input, OutputInterface $output )
	{
		$this->input  = $input;
		$this->output = $output;

		$this->config = new Config( $output );
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

	protected function write( $message, $tag )
	{
		$this->output->writeln( "<$tag>$message</$tag>" );
	}
}