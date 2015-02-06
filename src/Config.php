<?php namespace Package\Commands;

class Config {

	protected $output;

	protected $config = null;

	public function __construct( $output )
	{
		$this->output = $output;

		$this->get();
	}

	public function set( $field, $value )
	{
		$this->config[ $field ] = $value;

		return $this;
	}

	public function get( $field = null )
	{
		if( is_null( $this->config ) )
		{
			$this->config = $this->getConfigFileContents();
		}

		if( ! $field )
		{
			return $this->config;
		}

		if( array_key_exists( $field, $this->config ) )
		{
			return $this->config[ $field ];
		}

		$message = 'Trying to fetch unexisting config property: ' . $field;
		$this->output->writeln( "<error>{$message}</error>" );
		exit( 1 );
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

		$config[ 'types' ] = (array) $config[ 'types' ];

		return $config;
	}
}