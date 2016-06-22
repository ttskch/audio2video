#!/bin/sh

CMD="composer run"
if [ $# -ne 0 ]; then CMD=$1; fi

docker run -it -v $(pwd):/opt/work -w /opt/work -p 8888:8888 -e COMPOSER_PROCESS_TIMEOUT=100000 ttskch/audio2video $CMD
