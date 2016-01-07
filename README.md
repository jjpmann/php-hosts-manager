# php-hosts-manager

[![Latest Stable Version](https://poser.pugx.org/jjpmann/php-hosts-manager/v/stable)](https://packagist.org/packages/jjpmann/php-hosts-manager) 
[![Build Status](https://travis-ci.org/jjpmann/php-hosts-manager.svg?branch=master)](https://travis-ci.org/jjpmann/php-hosts-manager)
[![StyleCI](https://styleci.io/repos/40988670/shield)](https://styleci.io/repos/40988670)
[![License](https://poser.pugx.org/jjpmann/php-hosts-manager/license)](https://packagist.org/packages/jjpmann/php-hosts-manager)
[![Total Downloads](https://poser.pugx.org/jjpmann/php-hosts-manager/downloads)](https://packagist.org/packages/jjpmann/php-hosts-manager) 

PHP CLI for managing local hosts file

using https://gist.github.com/mikeflynn/4278796


## install

    composer require jjpmann/php-hosts-manager

__or global__
    
    composer global require jjpmann/php-hosts-manager

## quick intro

    Hosts Manager version 0.5

    Usage:
      command [options] [arguments]

    Options:
      -h, --help            Display this help message
      -q, --quiet           Do not output any message
      -V, --version         Display this application version
          --ansi            Force ANSI output
          --no-ansi         Disable ANSI output
      -n, --no-interaction  Do not ask any interactive question
      -v|vv|vvv, --verbose  Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug

    Available commands:
      add       Add domain/host to hosts file.
      check     Check to see if domain/host exists in host file
      help      Displays help for a command
      list      Lists commands
      remove    Remove domain/host from hosts file.
      rollback  Reverts the last change.
      update    Update domain/host in hosts file.

## ToDo

0. ~~Stop "Add" when host exists~~
0. ~~Add Tests~~
0. Add some features

