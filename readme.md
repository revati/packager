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
template you have two options - generate from folder structure of fetch remote template config file (TODO: How to 
share generated template config files?).

To create template config file from directory you can cd in it and run 

	packager template:make template-name

Now to use this template you can run

	packager new my-awesome-package template-name

It will initialize package in `my-awesome-package` directory. 

### Variables

Those variables can be used with in files and in directory and file names. Currently are supported 6 variables:

- author_name - Author name (defined with author command or on init) 
- author_email - Author email (defined with author command or on init)
- package_name - Package name (first argument when creating new command)
- package_description - Package description (can be passed as option when creating package),
- package_class - Package name (in CamelCase),
- author_class - Author name (in CamelCase),

All variables are prefixed and suffixed with two underscores.

## TODO

- Figure out a way to share template config files.