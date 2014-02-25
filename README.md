#Quixr - Apache2 virtual host analysis

[![Build Status](https://travis-ci.org/derhansen/quixr.png?branch=develop)](https://travis-ci.org/derhansen/quixr)
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/derhansen/quixr/badges/quality-score.png?s=11465c0dd3d311aee21755773ce8d6cdee6b6a6f)](https://scrutinizer-ci.com/g/derhansen/quixr/)
[![Code Coverage](https://scrutinizer-ci.com/g/derhansen/quixr/badges/coverage.png?s=25346efac4d6a7dd41a73e3745027229becdd797)](https://scrutinizer-ci.com/g/derhansen/quixr/)
[![Dependency Status](https://www.versioneye.com/user/projects/53098aa5ec1375991b000016/badge.png)](https://www.versioneye.com/user/projects/53098aa5ec1375991b000016)

Quixr is a command line tool which analyzes traffic and used diskspace for Apache2 virtual hosts. Quixr outputs its
analysis data into a JSON file, which can be used for reporting and data visualization.

Quixr is still under **development**, so some major features are missing and expect things to change during development.

##Todos

* Quota reporting
* Webtool for data visualization

##Who should use Quixr?

Quixr is for administrators who have multiple virtual hosts on an Apache2 webserver and want to get a global overview
about the traffic and diskspace usage of virtual host.

##Requirements

In order to use Quixr the following requirements must be met

* PHP 5.3 or higher installed on the server
* All Apache2 virtual hosts must be inside the same directory (e.g. `/var/www/`)
* Logfiles for each virtual host must be located inside a subfolder for each virtual host (e.g. `/var/www/vhost1/logfiles`)
* Document root for each virtual host must be located inside a subfolder for each virtual host (e.g. `/var/www/vhost1/htdocs`)

##Traffic analysis

The Quixr logfile analysis checks the logfile for each virtual host and accumulates the daily traffic. If the target
JSON file already contains historical data, new traffic data gets merged.

###Usage

```
Usage:
 analyze:traffic vhost-path logfile-path logfile target-file [logformat]

Arguments:
 vhost-path         Path to virtial hosts (e.g. /var/www/)
 logfile-path       Path to logfiles of each virtual host (e.g. logs)
 logfile            Logfile (e.g. access.log)
 target-file        Target JSON file for analysis results (e.g. quixr.json
 logformat          Apache2 Logfile format. Allowed values: common, combined (default: "combined")
```

###Example

``` sh
$ quixr analyze:traffic /var/www/ logfiles access_log /some/path/quixr.json common
```

##Diskspace analysis

The Quixr diskspace analysis checks the given document root for the virtual host and created a new entry in the target
JSON file for the current day. If the JSON file already contains historical data, new diskspace data gets appended.

###Usage

```
Usage:
 analyze:diskspace vhost-path document-root target-file

Arguments:
 vhost-path         Path to virtial hosts (e.g. /var/www/)
 document-root      Path to document root of each virtual host (e.g. htdocs)
 target-file        Target JSON file for analysis results (e.g. quixr.json
```

###Example

``` sh
$ quixr analyze:diskspace /var/www/ htdocs /some/path/quixr.json
```

##License
Quixr is licensed under the MIT License - see the LICENSE file for details
