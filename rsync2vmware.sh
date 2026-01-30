rsync -ratuve 'ssh -i /var/www/vlav/data/.ssh/id_rsa' --progress --exclude-from rsync.exclude vlav@194.67.117.172:/var/www/vlav/data/www/wwl/d/1000/ /home/vlav/www/html/wwl/d/1000
rsync -ratuve 'ssh -i /var/www/vlav/data/.ssh/id_rsa' --progress --exclude-from rsync.exclude vlav@194.67.117.172:/var/www/vlav/data/www/inc/ /home/vlav/www/html/inc
rsync -ratuve 'ssh -i /var/www/vlav/data/.ssh/id_rsa' --progress --exclude-from rsync.exclude vlav@194.67.117.172:/var/www/vlav/data/www/wwl/css/ /home/vlav/www/html/wwl/css
rsync -ratuve 'ssh -i /var/www/vlav/data/.ssh/id_rsa' --progress --exclude-from rsync.exclude vlav@194.67.117.172:/var/www/vlav/data/www/wwl/scripts/ /home/vlav/www/html/wwl/scripts
