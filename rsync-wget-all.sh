rsync -ratuve 'ssh -i /var/www/vlav/data/.ssh/id_rsa' --progress --exclude='/d/' --exclude='/help/' --exclude='/tmp/' --exclude='/1/'  --exclude='/.git/' vlav@194.67.117.172:/var/www/vlav/data/www/wwl/ /home/vlav/www/html/wwl/
mkdir /home/vlav/www/html/wwl/d
mkdir /home/vlav/www/html/wwl/d/1000
mkdir /home/vlav/www/html/wwl/d/1416650876
rsync -ratuve 'ssh -i /var/www/vlav/data/.ssh/id_rsa' --progress --exclude='tg_files/' vlav@194.67.117.172:/var/www/vlav/data/www/wwl/d/1000/ /home/vlav/www/html/wwl/d/1000/
rsync -ratuve 'ssh -i /var/www/vlav/data/.ssh/id_rsa' --progress --exclude='tg_files/' vlav@194.67.117.172:/var/www/vlav/data/www/wwl/d/1416650876/ /home/vlav/www/html/wwl/d/1416650876/
