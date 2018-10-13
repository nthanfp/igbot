#!/system/xbin/bash
clear
sleep 1
echo -e "\e[1;36m
  _   _ _____ _   _    _    _   _ _____ ____  
 | \ | |_   _| | | |  / \  | \ | |  ___|  _ \ 
 |  \| | | | | |_| | / _ \ |  \| | |_  | |_) |
 | |\  | | | |  _  |/ ___ \| |\  |  _| |  __/ 
 |_| \_| |_| |_| |_/_/   \_\_| \_|_|   |_|    
                                              "
sleep 1
echo "===================== CLI Tools ======================";
echo "================ NTHANFP.ME | axec0de ================";
echo -e "==================== Version 0.1 =====================\e[0m";
sleep 1
echo ""
echo -e "\e[1;35m[!] Select tools by number : "
echo "1) Spotify Checker"
echo "2) Spin to Cash Mining"
echo "3) Mass Check Username Insta"
echo "4) Mass Check HTTP Code"
echo -e "0) keluar\e[0m"
echo -e "\e[1;32m"
read -p "root@CLI-tools : " bro
echo -e "\e[0m"

if [ $bro = 1 ] || [ $bro = 1 ]
then
clear
cd spotifyCheck
php run.php
fi

if
[ $bro = 2 ] || [ $bro = 2 ]
then
clear
cd spinToCash
php run.php
fi

if [ $bro = 3 ] || [ $bro = 3 ]
then
clear
cd checkUsernameIG
php run.php
fi

if [ $bro = 4 ] || [ $bro = 4 ]
then
clear
cd HTTPheader
php run.php
fi

if [ $bro = 0 ] || [ $bro = 00 ]
then
clear
echo "Exit......"
sleep 1
exit
fi
