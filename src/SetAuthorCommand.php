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
		     ->addArgument( 'name', InputArgument::OPTIONAL, 'Author name' )
		     ->addArgument( 'email', InputArgument::OPTIONAL, 'Author email' );
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

		$name  = $input->getArgument( 'name' );
		$email = $input->getArgument( 'email' );

		if( is_null( $name ) )
		{
			$name = $this->askAuthorName();
		}

		if( is_null( $email ) )
		{
			$email = $this->askAuthorEmail();
		}
		elseif( ! filter_var( $email, FILTER_VALIDATE_EMAIL ) )
		{
			$this->writeError( 'Author email must be valid email' );

			$email = $this->askAuthorEmail();
		}

		$this->config
			->setAuthorName( $name )
			->setAuthorEmail( $email )
			->save();

		$this->writeInfo( 'Default author set to ' . $name . ' with ' . $email . ' email' );
	}

	protected function askAuthorName()
	{
		return $this->ask( 'Provide author  name', $this->authorNameValidator() );
	}

	protected function askAuthorEmail()
	{
		return $this->ask( 'Provide author email', $this->authorEmailValidator() );
	}

	protected function authorNameValidator()
	{
		return function ( $authorName )
		{
			if( empty( $authorName ) )
			{
				throw new Exception( 'Author name must be provided' );
			}

			return $authorName;
		};
	}

	protected function authorEmailValidator()
	{
		return function ( $authorEmail )
		{
			if( empty( $authorEmail ) )
			{
				throw new Exception( 'Author email must be provided' );
			}

			if( ! filter_var( $authorEmail, FILTER_VALIDATE_EMAIL ) )
			{
				throw new Exception( 'Author email must be valid email' );
			}

			return $authorEmail;
		};
	}
}