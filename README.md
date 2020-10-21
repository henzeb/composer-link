# composer-link
Simplifies development of libraries by linking them into your project. loosely based on npm link and [ro0NL/composer-link](https://github.com/ro0NL/composer-link), which is currently no longer updated and has some dependency errors in some cases.
## Installation
If you want to install this globally:

    composer global require henzeb/composer-link

If you want to install this per project:

    composer require henzeb/composer-link

## Usage
Every link made will be transformed to a relative path. This is useful in situations where you want to test inside a docker container or virtual machine.

### Link
To link a local package you must be sure to have it required first. After that you can just point to the location where your package resides:

    composer link ../path/to/your/package

### Unlink

### Linking/unlinking previously linked packages
If you have linked a package before, composer-link has stored the path inside a file called `linked.json`.  Whenever you are not developing and need the package version that was actually installed, just call the following:

    composer unlink
 
And when you want to continue developing

    composer link
  
  you can add the package name to specify a specific package if needed.

    composer link yourname/your-package

### configuration
This package requires no configuration and works out of the box. You can however change the location/filename of this file by adding `link` with the path where you want the configuration to be stored inside the `extra` parameter of your composer.json of your project.

    ...
    "extra": {
        "link":"path/to/your/linked.json"
    },
    ...


