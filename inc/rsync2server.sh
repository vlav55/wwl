#!/bin/bash
cp -uRv . /mnt/SATA3/files/backup/inc/vklist
rsync -e ssh --progress -lzuogthvr --compress-level=9 /home/vlav/www/html/pini/inc/vklist/  webmaster@1-info.ru:/var/www/html/pini/inc/vklist



