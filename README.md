#Quixr - Apache2 virtual host analysis

[![Build Status](https://travis-ci.org/derhansen/quixr.png?branch=develop)](https://travis-ci.org/derhansen/quixr)
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/derhansen/quixr/badges/quality-score.png?s=11465c0dd3d311aee21755773ce8d6cdee6b6a6f)](https://scrutinizer-ci.com/g/derhansen/quixr/)

Quixr is a command line tool which can analyze traffic and used diskspace for Apache2 virtual hosts. Quixr outputs its
analysis data into a JSON file.

Quixr is still under **development**, so some major features are missing and expect things to change during development.

##Todos

* Vhost diskspace analysis
* Quota reporting
* Webtool for data visualization

##Who should use Quixr?

Quixr is for administrators who have multiple virtual hosts on an Apache2 webserver and want to get a global overview
about the traffic and diskspace usage of virtual host.

##Requirements

In order to use Quixr the following requirements must be met

* PHP 5.3 or higher installed on the server
* All Apache2 virtual hosts must be inside a directory (e.g. `/var/www/`)
* Logfiles for each virtual host must be located inside a subfolder for each virtual host (e.g. `vhost1/logfiles`)

##Traffic analysis

The Quixr logfile analysis checks the logfile for each virtual host and accumulates the daily traffic. If the target
JSON file already contains historical data, new traffic data gets merged.

``` sh
$ quixr analyze:traffic /var/www/ logfiles access_log /some/path/quixr.json common
```

##License
Quixr is licensed under the MIT License - see the LICENSE file for details
