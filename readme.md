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

See [Simple example](#simple-example) for more detailed info.

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

- [ ] Figure out a way to share template config files. 
	- `template:share template-name` command to upload template config file somewhere?
- [ ] Ability to run some custom scripts (git init, composer install...) after package is initialized
- [ ] Ability to initialize package in per template predefined custom subdirectory. 
	- runing package initialization from project root, but create package under ./packages directory.
- [x] ~~Ability to create raw template from config file, for template editing purposes.~~
- [ ] Ability to pull composer packages, but not in vendor directory, but as main package, for editing purposes.

## Simple example

Create new directory. With in it create new composer.json file.

	{
	  "name": "__author_name__/__package_name__",
	  "description": "__package_description__",
	  "authors": [
	    {
	      "name": "__author_name__",
	      "email": "__author_email__"
	    }
	  ],
	  "autoload": {
	    "psr-4": {
	      "__author_class__\\__package_class__\\": "src/"
	    }
	  },
	}

Lets create `src` directory and with in `__package_class__Class.php` with fallowing content.

	<?php namespace __author_class__\__package_class__;
	
	class __package_class__Class extends SomeClass {
	
	    // Code
	}

Now when template is done lets make its configuration file.

	packager template:make simple-template

Template is ready. To use it run 

	packager new simple-package simple-template

It will create `simple-package` directory. It will contain `composer.json` file with fallowing content:

	{
	  "name": "revati/simple-package",
	  "description": "",
	  "authors": [
	    {
	      "name": "revati",
	      "email": "email@email.com"
	    }
	  ],
	  "autoload": {
	    "psr-4": {
	      "Revati\\SimplePackage\\": "src/"
	    }
	  }
	}

There also will be `src` folder with `SimplePackageClass.php` file. And its content will be:

	<?php namespace Revati\SimplePackage;
	
	class SimplePackageClass extends SomeClass {
	
	    // Code
	}

Hope this example helps. In [Variables](#variables) section you can find all available variables.