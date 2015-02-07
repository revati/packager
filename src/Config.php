<?php namespace Packager;

use Exception;

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

	public function getTemplate( $name = null )
	{
		if( is_null( $name ) )
		{
			return $this->config[ 'templates' ];
		}

		if( ! $this->hasTemplate( $name ) )
		{
			throw new Exception( 'Unexisting template ' . $name );
		}

		return $this->config[ 'templates' ][ $name ];
	}

	public function hasTemplate( $name )
	{
		return array_key_exists( $name, $this->config[ 'templates' ] );
	}

	public function setTemplate( $name, $source )
	{
		$this->config[ 'templates' ][ $name ] = $source;

		return $this;
	}

	public function getDefaultTemplate()
	{
		return $this->config[ 'defaultTemplate' ];
	}

	public function setDefaultTemplate( $name )
	{
		$this->config[ 'defaultTemplate' ] = $name;

		return $this;
	}

	public function save()
	{
		file_put_contents( $this->path(), json_encode( $this->config, JSON_PRETTY_PRINT ) );
	}

	protected function path()
	{
		return realpath( config_path() . DIRECTORY_SEPARATOR . 'Packager.json' );
	}

	protected function getConfigFileContents()
	{
		$configFile = $this->path();

		if( $configFile === false )
		{
			throw new Exception( 'Missing config file' );
		}

		$config = (array) json_decode( file_get_contents ( $configFile ) );

		if( $config === false )
		{
			throw new Exception( 'Config file is corrupt' );
		}

		$config[ 'templates' ] = (array) $config[ 'templates' ];

		return $config;
	}
}