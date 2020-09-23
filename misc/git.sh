#!/bin/bash

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
cd $DIR/..


if [ $# -eq 0 ]
  then
    echo "ERROR! NO git commit message supplied!"
    exit
fi

DATE=$(date +"%Y%m%d%H%M%S")

FUID=$DATE-$$-$RANDOM

git add .
git commit -m "[auto] $1" .
git pull && git push 
