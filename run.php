<?php
error_reporting(0);
require('func.php');
function req($url, $data)
{
	$ch  = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    if($data):
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    endif;
    $response = curl_exec($ch);

    return $response;
}
// trying login user bot
echo ">> Input your bot username : ";
$userbot  = trim(fgets(STDIN, 1024));
echo ">> Input your bot password : ";
$passbot  = trim(fgets(STDIN, 1024));
// >__<
$data     = array('aksi' => 'loginbot', 'username' => $userbot, 'password' => $passbot);
$login    = json_decode(req('https://bot.nthanfp.me/action/api().php', $data));
if($login->msg<>'Login Sucesss...'){
	echo $login->msg."\n";
} else {
	echo $login->msg."\n";
	echo ">> Input your instagram username : ";
	$userig   = trim(fgets(STDIN, 1024));
	echo ">> Input your instagram password : ";
	$passig   = trim(fgets(STDIN, 1024));
	// kyaa
	$useragent = generate_useragent();
	$device_id = generate_device_id();
    $user      = $userig;
    $pass      = $passig;
    $login     = proccess(1, $useragent, 'accounts/login/', 0, hook('{"device_id":"' . $device_id . '","guid":"' . generate_guid() . '","username":"'.$userig.'","password":"'.$passig.'","Content-Type":"application/x-www-form-urlencoded; charset=UTF-8"}'), array('Accept-Language: id-ID, en-US', 'X-IG-Connection-Type: WIFI'));
    $ext   = json_decode($login[1]);
    if($ext->status<>'ok'){
    	echo $ext->message."\n";
    } else {
    	preg_match_all('%Set-Cookie: (.*?);%', $login[0], $d);
    	$cookie = '';
    	for ($o = 0; $o < count($d[0]); $o++)
    		$cookie .= $d[1][$o] . ";";
    	$uname = $ext->logged_in_user->username;
    	$req = proccess(1, $useragent, 'feed/timeline/', $cookie);
    	$is_verified = (json_decode($req[1])->status<>ok) ? 0 : 1;
    	$uid = $ext->logged_in_user->pk;
    	echo "Adding Instagram Account....\n";
    	echo "Please Wait\n";
    	sleep(2);
    	$data   = array('aksi' => 'addig', 'id' => $uid, 'cookies' => $cookie, 'useragent' => $useragent, 'device_id' => $device_id, 'verifikasi' => $is_verified, 'username' => $uname, 'uplink' => $userbot);
    	$addig  = json_decode(req('https://bot.nthanfp.me/action/api().php', $data));

    	echo $addig->msg;
    }
}
?>