rsync -ratuve 'ssh -i /var/www/vlav/data/.ssh/id_rsa' --progress --exclude-from=rsync.exclude  /home/vlav/www/html/wwl vlav@194.67.117.172:/var/www/vlav/data/www/
rsync -ratuve 'ssh -i /var/www/vlav/data/.ssh/id_rsa' --progress --exclude-from=rsync.exclude  /home/vlav/www/html/inc vlav@194.67.117.172:/var/www/vlav/data/www/
