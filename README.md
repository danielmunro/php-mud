[![Build Status](https://semaphoreci.com/api/v1/dmunro/php-mud/branches/master/badge.svg)](https://semaphoreci.com/dmunro/php-mud)
[![Code Climate](https://codeclimate.com/github/danielmunro/php-mud/badges/gpa.svg)](https://codeclimate.com/github/danielmunro/php-mud)
[![Test Coverage](https://codeclimate.com/github/danielmunro/php-mud/badges/coverage.svg)](https://codeclimate.com/github/danielmunro/php-mud/coverage)
[![Issue Count](https://codeclimate.com/github/danielmunro/php-mud/badges/issue_count.svg)](https://codeclimate.com/github/danielmunro/php-mud)

# php-mud

## Setup

Git clone this repository and cd to the new repo.

```
$ git clone https://github.com/danielmunro/php-mud.git
...
$ cd php-mud
```

Create world fixtures:

```
$ ./bin/create-world
[2016-12-20 07:45:23] phpmud.INFO: drop schema [] []
[2016-12-20 07:45:23] phpmud.INFO: create schema [] []
[2016-12-20 07:45:23] phpmud.INFO: persist initial fixtures [] []
```

Run the instance:

```
$ ./bin/run-world
[2016-12-20 07:46:38] phpmud.INFO: php-mud is up and running {"start":"2016-12-20 07:46:38","port":9000} []
```

Telnet to the running instance:

```
$ telnet localhost 9000
Trying ::1...
telnet: connect to address ::1: Connection refused
Trying 127.0.0.1...
Connected to localhost.
Escape character is '^]'.
By what name do you wish to be known?
```
