# audio2video

[![Build Status](https://travis-ci.org/ttskch/audio2video.svg?branch=master)](https://travis-ci.org/ttskch/audio2video)

Source code of http://audio2video.me

## See also

CLI version: [ttskch/audio2video-cli](https://github.com/ttskch/audio2video-cli)

## For developers

### Requirements

* PHP 7.1.3+
* npm
* Docker

### Installation

```bash
$ docker build . -t {tag}
$ composer install
```

### Running

```bash
$ docker run -p 8888:8888 -v $(pwd):/docroot {tag}
```

Then browse http://localhost:8888

#### Debugging in Docker container

```bash
$ docker run -p 8888:8888 -v $(pwd):/docroot -it {tag} sh

# default user is a non-root user named "nonroot"
% whoami
nonroot

# can sudo
% sudo whoami
root
```
