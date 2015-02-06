<?php namespace Package\Commands;

use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SetupCommand extends Command {

	protected $input;

	protected $output;

	public function configure()
	{
		$this->setName( 'setup' )->setDescription( 'Setup base' );
	}

	public function execute( InputInterface $input, OutputInterface $output )
	{
		$this->input  = $input;
		$this->output = $output;

		$config = new Config( $output );

		$author = $this->fetchAuthorName();

		$config->set( 'author', $author )->save();

		$message = 'Default author set to ' . $author;
		$output->writeln( "<info>{$message}</info>" );

		$this->showModesTable();
	}

	protected function fetchAuthorName()
	{
		$message = '<question>Please set author name:</question> ';

		return $this->getHelper( 'dialog' )
		            ->askAndValidate( $this->output, $message, $this->authorNameValidator() );
	}

	protected function authorNameValidator()
	{
		return function ( $answer )
		{
			if( empty( $answer ) )
			{
				throw new Exception( "Author name must be provided" );
			}

			return $answer;
		};
	}

	protected function showModesTable()
	{
		$command = $this->getApplication()->find( 'type:all' );

		return $command->run( new ArrayInput( [ 'command' => 'type:all' ] ), $this->output );
	}
}