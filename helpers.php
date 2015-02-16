<?php

function local_path( $appendPath = null )
{
	$packagerDirectory = '.packager';

	if( isset( $_SERVER[ 'HOME' ] ) )
	{
		return make_path( $_SERVER[ 'HOME' ], $packagerDirectory, func_get_args() );
	}

	return make_path( $_SERVER[ 'HOMEDRIVE' ], $_SERVER[ 'HOMEPATH' ], $packagerDirectory, func_get_args() );
}

function package_path( $paths = null )
{
	return make_path( __DIR__, func_get_args() );
}

function current_path( $path = null )
{
	return make_path( getcwd(), func_get_args() );
}

function stub_path( $file )
{
	return make_path( package_path(), 'stubs', func_get_args() );
}

function make_path( $paths )
{
	return multi_implode( DIRECTORY_SEPARATOR, func_get_args() );
}

function absolute_path( $path, $allowUrl = false )
{
	if( $allowUrl && filter_var( $path, FILTER_VALIDATE_EMAIL ) )
	{
		return $path;
	}

	if( is_absolute_path( $path ) )
	{
		return $path;
	}

	return make_path( current_path(), $path );
}

/**
 * Returns whether the path is an absolute path.
 * Copied from Symfony\Component\Filesystem\Filesystem
 *
 * @param string $path A path
 *
 * @return bool
 */
function is_absolute_path( $path )
{
	if( strspn( $path, '/\\', 0, 1 )
	    || ( strlen( $path ) > 3 && ctype_alpha( $path[ 0 ] )
	         && substr( $path, 1, 1 ) === ':'
	         && ( strspn( $path, '/\\', 2, 1 ) )
		)
	    || null !== parse_url( $path, PHP_URL_SCHEME )
	)
	{
		return true;
	}

	return false;
}

function multi_implode( $delimiter, array $array, $removeEmpty = true )
{
	$returnArray = [ ];

	if( $removeEmpty )
	{
		$array = array_filter( $array );
	}

	foreach( $array as $item )
	{
		if( is_array( $item ) )
		{
			$returnArray[ ] = multi_implode( $delimiter, $item, $removeEmpty );

			continue;
		}

		$returnArray[ ] = $item;
	}

	return implode( $delimiter, $returnArray );
}

function put_config( $path, $content )
{
	file_put_contents( $path, json_encode( $content, JSON_PRETTY_PRINT ) );
}

function get_config( $path, $required = true )
{
	if( is_file( $path ) )
	{
		return json_decode( file_get_contents( $path ), true );
	}

	if( $required )
	{
		throw new Exception( "Missing config file: $path" );
	}

	return false;
}

function get_global_config()
{
	return get_config( local_path( 'packager.json' ) );
}

function put_global_config( $content )
{
	put_config( local_path( 'packager.json' ), $content );
}

function array_get( array $array, $key, $default = null )
{
	if( ! is_array( $key ) )
	{
		$key = (array) explode( '.', $key );
	}
	foreach( $key as $part )
	{
		if( array_key_exists( $part, $array ) )
		{
			return array_get( $array, $part, $default );
		}
	}

	return $default;
}