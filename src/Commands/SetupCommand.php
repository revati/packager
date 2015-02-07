<?php namespace Packager\Commands;

use Exception;
use Packager\BaseCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SetupCommand extends BaseCommand {

	public function configure()
	{
		$this->setName( 'setup' )->setDescription( 'Setup base' );
	}

	public function execute( InputInterface $input, OutputInterface $output )
	{
		$this->prepare( $input, $output );

		$author = $this->askAuthorName();

		$this->config->setAuthor( $author )->save();

		$this->writeInfo( 'Default author set to ' . $author );

		$this->showModesTable();
	}

	protected function askAuthorName()
	{
		return $this->ask( 'Provide author name', $this->authorNameValidator() );
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
		$command = $this->getApplication()->find( 'bootstrap:all' );

		return $command->run( new ArrayInput( [ 'command' => 'bootstrap:all' ] ), $this->output );
	}
}