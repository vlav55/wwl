rsync -ratuve 'ssh -i /var/www/vlav/data/.ssh/id_rsa' --progress --include='*.php' --include='*/'  --exclude='*'  /home/vlav/www/html/wwl/ vlav@194.67.117.172:/var/www/vlav/data/www/wwl/
rsync -ratuve 'ssh -i /var/www/vlav/data/.ssh/id_rsa' --progress --include='*.php' --include='*/'  --exclude='*'  /home/vlav/www/html/wwl/inc/ vlav@194.67.117.172:/var/www/vlav/data/www/wwl/inc/
