# Packager

This package provides bootstrap for creating packages. 

## Installation

Install this package globally to access from anywhere

	composer global require revati/packager=dev-master

	// Update path
	export PATH=~/.composer/vendor/bin:$PATH

Now you can call `packager` from your terminal.

First of all you have to initialize package. It will create ~/.Packager folder.

	packager init
