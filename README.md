# audio2video

Source code of http://audio2video.me

## See also

CLI version: [ttskch/audio2video-cli](https://github.com/ttskch/audio2video-cli)

## For developers

### Requirements

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

Then browse http://localhost:8888/index_dev.php

### Debugging in Docker container

```bash
$ docker run -p 8888:8888 -v $(pwd):/docroot -it {tag} sh

# default user is a non-root user named "nonroot"
% whoami
nonroot

# can sudo
% sudo whoami
root
```
