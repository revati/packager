<?php namespace Packager;

class Config {

	protected $output;

	protected $config = null;

	public function __construct( $output )
	{
		$this->output = $output;

		$this->config = $this->getConfigFileContents();
	}

	public function getAuthor()
	{
		return $this->config[ 'author' ];
	}

	public function setAuthor( $author )
	{
		$this->config[ 'author' ] = $author;

		return $this;
	}

	public function getBootstrap( $name = null )
	{
		if( is_null( $name ) )
		{
			return $this->config[ 'bootstraps' ];
		}

		if( ! $this->hasBootstrap( $name ) )
		{
			$this->output->writeln( "<error>Unexisting bootstrap {$name}</error>" );
			exit( 1 );
		}

		return $this->config[ 'bootstraps' ][ $name ];
	}

	public function hasBootstrap( $name )
	{
		return array_key_exists( $name, $this->config[ 'bootstraps' ] );
	}

	public function setBootstrap( $name, $source )
	{
		$this->config[ 'bootstraps' ][ $name ] = $source;

		return $this;
	}

	public function getDefaultBootstrap()
	{
		return $this->config[ 'defaultBootstrap' ];
	}

	public function setDefaultBootstrap( $name )
	{
		$this->config[ 'defaultBootstrap' ] = $name;

		return $this;
	}

	public function save()
	{
		file_put_contents( $this->path(), json_encode( $this->config, JSON_PRETTY_PRINT ) );
	}

	protected function path()
	{
		return realpath( __DIR__ . '/../config.json' );
	}

	protected function getConfigFileContents()
	{
		$configFile = $this->path();

		if( $configFile === false )
		{
			$this->output->writeln( '<error>Config file is missing</error>' );
			exit( 1 );
		}

		$config = (array) json_decode( file_get_contents ( $configFile ) );

		if( $config === false )
		{
			$this->output->writeln( '<error>Config file is corrupt</error>' );
			exit( 1 );
		}

		$config[ 'bootstraps' ] = (array) $config[ 'bootstraps' ];

		return $config;
	}
}