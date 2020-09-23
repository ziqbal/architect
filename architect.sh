#!/usr/bin/env bash

#
# kill -9 -SPID
#

if [ ! -f architect.ini ]; then
    echo "architect.ini file not found!"
    exit
fi

if [ ! -d "cache" ]; then
    echo "creating cache directory"
    mkdir cache
fi

if [ ! -d "src" ]; then
    echo "creating src directory"
    mkdir src
fi


if [ ! -f /Applications/XAMPP/bin/php ]; then
    INTERPRETER="/usr/bin/php"
else
    INTERPRETER="/Applications/XAMPP/bin/php"
fi

STARTDIR=$(pwd)
SCRIPTDIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

TIMESTAMP=$(date +%s)
HOSTNAME=$(hostname)
PID=$$
USER=$(whoami)

FILENAME=$(basename "${BASH_SOURCE[0]}")
EXTENSION="${FILENAME##*.}"
FNAME="${FILENAME%.*}"

DATE=$(date +"%Y%m%d%H%M%S")
FUID=$DATE-$$-$RANDOM

#$INTERPRETER $FNAME.php \"$STARTDIR\" "$@" $HOSTNAME $TIMESTAMP $PID
#$INTERPRETER $BOOTFILE "$SCRIPTDIR" "$STARTDIR" "$(uname)" $USER $HOSTNAME $TIMESTAMP $PID "$@"

FLAGWATCH=n

_ARGS=$@
while test $# -gt 0
do
    case "$1" in
        --watch) echo "[WATCHING]"
        FLAGWATCH=y
            ;;
        --opt2) echo "option 2"
            ;;
        --*) echo "bad option $1"
        exit
            ;;
        *) echo "$1 ???"
            ;;
    esac
    shift
done



if [ $FLAGWATCH = "y" ]; then

    
while true
do
fswatch -1 src/*.php
echo \#$((1 + RANDOM % 999999))
date '+%H:%M:%S %Y-%m-%d'
echo BUILDING
$INTERPRETER "$SCRIPTDIR"/src/boot.php "$STARTDIR" "$(uname)" $USER $HOSTNAME $TIMESTAMP $PID $FUID "_argsep_" "$_ARGS"  >>/tmp/$FNAME.log 2>&1  
echo done
play ~/Downloads/beep-01a.wav
sleep 3
done
#fswatch -0 src  -e ".*" -i "\\.php$" | while read -d "" event 
#  do
#date
#echo BUILDING
#$INTERPRETER "$SCRIPTDIR"/src/boot.php "$STARTDIR" "$(uname)" $USER $HOSTNAME $TIMESTAMP $PID $FUID "_argsep_" "$_ARGS"  >>/tmp/$FNAME.log 2>&1  
#echo done
#    sleep 1
#  done


else

$INTERPRETER "$SCRIPTDIR"/src/boot.php "$STARTDIR" "$(uname)" $USER $HOSTNAME $TIMESTAMP $PID $FUID "_argsep_" "$_ARGS"  >>/tmp/$FNAME.log 2>&1  

fi




