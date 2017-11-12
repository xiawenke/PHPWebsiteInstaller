<?php
$installer_url="https://raw.githubusercontent.com/xiawenke/PHPWebsiteInstaller/master/public/demo_installer";
$install_path="./out_put/";

function getweb($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_REFERER, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
  }
  
set_time_limit(0);
if(!is_dir($install_path)){
    if(!mkdir($install_path,"0777")){
        echo("ERROR:Unble to create $install_path, and it's also doesn't exist!");
        exit();
    }
}
file_put_contents($install_path."install.php",getweb("https://raw.githubusercontent.com/xiawenke/PHPWebsiteInstaller/master/public/install_20171111"));
file_put_contents($install_path."installer",getweb($installer_url));
require($install_path."install.php");
?>