#!/bin/bash
rsync -av --progress * git/ --exclude=git/ --exclude=constants.php
cd git/
git add .
git commit -m $1
#git push origin master
