<?php namespace Packager;

use Exception;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SetAuthorCommand extends BaseCommand {

	/**
	 * Configure the command options.
	 *
	 * @return void
	 */
	public function configure()
	{
		$this->setName( 'author' )
		     ->setDescription( 'Setup author' )
		     ->addArgument( 'name', InputArgument::OPTIONAL, 'Author name' );
	}

	/**
	 * Execute the command.
	 *
	 * @param  \Symfony\Component\Console\Input\InputInterface   $input
	 * @param  \Symfony\Component\Console\Output\OutputInterface $output
	 *
	 * @return void
	 */
	public function execute( InputInterface $input, OutputInterface $output )
	{
		parent::execute( $input, $output );

		$author = $input->getArgument( 'name' );

		if( is_null( $author ) )
		{
			$author = $this->askAuthorName();
		}

		$this->config->setAuthor( $author )->save();

		$this->writeInfo( 'Default author set to ' . $author );
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

}