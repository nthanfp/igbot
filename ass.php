<?php
function getuid($username)
{
    $url = 'https://nthanfp.me/api/get/instagramUserdata?apikey=NTHANFP150503&username=' . $username . '';
    $fgc = file_get_contents($url);
    if(!$fgc)
        die('Connection error');
    $id = json_decode($fgc)->data->user->id;
    
    return $id;
}

function req($url, $data)
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    if($data):
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    endif;
    $response = curl_exec($ch);
    
    return $response;
}

function version()
{
    return "Ver 1";
}

function banner1()
{
    $banner  = "==== Instagram Tools ".version()." ====\n";

    return $banner;

}

function banner2()
{
    $banner  = "=========== Code by ===========\n";
    $banner2 = "====== NTHANFP | AXEC0DE ======\n\n";

    return $banner."".$banner2;
}
?>