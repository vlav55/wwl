#!/bin/bash

#curl -s -X POST "https://api.telegram.org/bot1820548789:AAGejAyt2oBcru_EsvVwU6JGlUNj_SyYvo8/sendMessage" -d chat_id=315058329 -d text="system_test_reboot.sh : called Ok"

file="system_test/mysql_test.txt"

if [ ! -f "$file" ]; then
    echo "File $file does not exist. Reboot"
    curl -s -X POST "https://api.telegram.org/bot1820548789:AAGejAyt2oBcru_EsvVwU6JGlUNj_SyYvo8/sendMessage" -d chat_id=315058329 -d text="system_test_reboot.sh : SYSTEM GONE TO REBOOT"
    sudo reboot
else
    echo "File $file exists. Skipping"
fi

