# Packager

This package provides template for creating packages. 

## Installation

Install this package globally to access from anywhere

	composer global require revati/packager=dev-master

	// Update path
	export PATH=~/.composer/vendor/bin:$PATH

Now you can call `packager` from your terminal.

First of all you have to initialize package. It will create ~/.Packager folder.

	packager init

## Usage

Out of the box packager does not come with any predefined templates. So you have to define them your self. To create
template you have two options - generate from folder structure or fetch template config file (TODO: How to 
share generated template config files?).

To create template config file from directory cd in in it and run

	packager template:make template-name

Now to use this template you can run

	packager new my-awesome-package template-name

It will initialize package in `my-awesome-package` directory. 

### Variables

Those variables can be used with in files and in directory and file names. Currently are supported 6 variables:

- __author_name__ - Author name (defined with author command or on init) 
- __author_email__ - Author email (defined with author command or on init)
- __package_name__ - Package name (first argument when creating new command)
- __package_description__ - Package description (can be passed as option when creating package),
- __package_class__ - Package name (in CamelCase),
- __author_class__ - Author name (in CamelCase),

All variables are prefixed and suffixed with two underscores (that's why they are bold).

## TODO

- Figure out a way to share template config files.
