<?php
/**
ATD Installer.
V1.0.0Beta.
需要php扩展fileinfo。
**/
if(isset($_POST['response'])!="s2"){
    if(!isset(get_extension_funcs("fileinfo")[0])){
        echo('<div data-alert class="alert-box alert">');
        echo("<strong>Warning:</strong>  For pack the files successfully, please install PHP extension called php_fileinfo!");
        echo("</div>");
    }
    else{
        echo('<div data-alert class="alert-box success"><strong>Success:</strong> php_fileinfo can be founded!</div>');
    }
    if(!is_dir("./pack/")){
        mkdir("./pack/","0777");
    }
}
else{
    if($_POST['response']=="s2"){
        if(!is_dir("./pack/")){
            echo('<div class="panel">Ohh! We cannot find the folder in ./pack/, please go back and check it!</div>');
            exit();
        }
        else{
            $date=date("Y-m-d h:i:sa");
            $verson=[
                'installer_verson'=>'1.0.0_Beta',
                'date'=>$date
            ];
            
            function MulitarraytoSingle($array){
                $temp=array();
                if(is_array($array)){
                  foreach ($array as $key=>$value )
                  {
                    if(is_array($value)){
                      $temp=array_merge($temp,MulitarraytoSingle($value));
                    }
                    else{
                      $temp[]=$value;
                    }
                  }
                  return($temp);
                }
             }
            
            
            
            function recurFolders($pathName)
            {
                //将结果保存在result变量中
                $result = array();
                $temp = array();
                //判断传入的变量是否是目录
                if(!is_dir($pathName) || !is_readable($pathName)) {
                    return null;
                }
                //取出目录中的文件和子目录名,使用scandir函数
                $allFiles = scandir($pathName);
                //遍历他们
                foreach($allFiles as $fileName) {
                    if(in_array($fileName, array('.', '..'))) {
                        continue;
                    }
                    //路径加文件名
                    $fullName = $pathName.'/'.$fileName;
                    //如果是目录的话就继续遍历这个目录
                    if(is_dir($fullName)) {
                        //将这个目录中的文件信息存入到数组中
                        $result[$fullName] = recurFolders($fullName);
                        $temp[]=$fullName;
                    }else {
                        //如果是文件就先存入临时变量
                        //$temp[] = $fullName;
                    }
                }
                //取出文件
                if($temp) {
                    foreach($temp as $f) {
                        $result[] = $f;
                    }
                }
                $result=MulitarraytoSingle($result);
                return $result;
            }
            
            function recurAllFiles($pathName)
            {
                //将结果保存在result变量中
                $result = array();
                $temp = array();
                //判断传入的变量是否是目录
                if(!is_dir($pathName) || !is_readable($pathName)) {
                    return null;
                }
                //取出目录中的文件和子目录名,使用scandir函数
                $allFiles = scandir($pathName);
                //遍历他们
                foreach($allFiles as $fileName) {
                    if(in_array($fileName, array('.', '..'))) {
                        continue;
                    }
                    //路径加文件名
                    $fullName = $pathName.'/'.$fileName;
                    //如果是目录的话就继续遍历这个目录
                    if(is_dir($fullName)) {
                        //将这个目录中的文件信息存入到数组中
                        $result[] = recurAllFiles($fullName);
                    }else {
                        //如果是文件就先存入临时变量
                        $temp[] = $fullName;
                    }
                }
                //取出文件
                if($temp) {
                    foreach($temp as $f) {
                        $result[] = $f;
                    }
                }
                $result=MulitarraytoSingle($result);
                return $result;
            }
            
            function del_DirAndFile($dirName){
                if(is_dir($dirName)){
                    if ( $handle = opendir( "$dirName" ) ) {  
                      while ( false !== ( $item = readdir( $handle ) ) ) {  
                          if ( $item != "." && $item != ".." ) {  
                              if ( is_dir( "$dirName/$item" ) ) {  
                                  del_DirAndFile( "$dirName/$item" );  
                              } else {  
                                  if( unlink( "$dirName/$item" ) )echo "";  
                              }  
                          }  
                      }  
                  closedir( $handle );  
                 if( rmdir( $dirName ) ) echo "";  
                    }
                }
            }
            
            function is_text($dir){
                if(!file_exists($dir)){
                    echo("The path $dir doesn't exist!");
                    exit();
                }
                $type=mime_content_type($dir);
                $type=($type[0].$type[1].$type[2].$type[3].$type[4]);
                if($type=="text/"){
                    return(true);
                }
                else{
                    return(false);
                }
            }
            
            function file_type($dir){
                $t=is_file($dir);
                if(!$t){
                    return("dir");//；检查目录是否为文件夹
                }
                else{
                    if(is_text($dir)){
                        return("text");
                    }
                    else{
                        return("other");
                    }
                }
            }
            
            function note_files($dir_path){
                $count=count($dir_path);
                $i=0;
                $return=null;
                while(1){
                    $count=$count-1;
                    $file=$dir_path[$count];
            
                    if(!file_exists($file)){
                        echo("ERROR:$file doesn't exist!");
                        exit();
                    }
                    if(!is_readable($file)){
                        echo("ERROR:$file is unreadable!");
                        exit();
                    }
            
                    if(is_text($file)){
                        $text=file_get_contents($file);
                        $file=mb_convert_encoding($file, 'UTF-8','GB2312,UTF-8');
                        $text=mb_convert_encoding($text, 'UTF-8','GB2312,UTF-8');
                        $text=[
                            'path'=>$file,
                            'type'=>"text",
                            "content"=>$text
                        ];
                        $return[$i]=$text;
                        $i=$i+1;
                    }
                    else{
                        $md5=md5($file);
                        if(!copy($file,"./pack_temp/".$md5)){
                            echo("ERROR:Failed to copy the files to temp folder!");
                            exit();
                        }
                        $file=mb_convert_encoding($file, 'UTF-8','GB2312,UTF-8');
                        $md5=mb_convert_encoding($md5, 'UTF-8','GB2312,UTF-8');
                        $text=[
                            'path'=>$file,
                            'type'=>'other',
                            'content'=>$md5
                        ];
                        $return[$i]=$text;
                        $i=$i+1;
                    }
                    if($count<=0){
                        return($return);
                        break;
                    }
                }
            }
            
            
            //创建临时文件夹
            if(!mkdir("./pack_temp/", 0777)){
                del_DirAndFile("./pack_temp/");
                if(!mkdir("./pack_temp/", 0777)){
                    echo("ERROR:Cannot create temp folder!");
                    exit();
                }
            }
            if(!$file=fopen("./pack_temp/_install.json", "w+")){
                echo("ERROR:Cannot create main files folder!");
                exit();
            }
            if(!$file=fopen("./pack_temp/_verson.json", "w+")){
                echo("ERROR:Cannot create main files folder!");
                exit();
            }
            fclose($file);
            
            $folder='pack';
            $file_array = recurAllFiles($folder);
            $folder_array=recurFolders($folder);
            
            $json['folders']=$folder_array;
            $json['files']=note_files($file_array);
            
            $json=json_encode($json);
            //echo("json,$json,json");
            file_put_contents('./pack_temp/_install.json',$json);
            file_put_contents('./pack_temp/_verson.json',$verson);
            
            //打包文件。
            $datalist=recurAllFiles('./pack_temp/');
            $filename = "./installer"; //最终生成的文件名（含路径）   
            if(!file_exists($filename)){   
            //重新生成文件   
                $zip = new ZipArchive();//使用本类，linux需开启zlib，windows需取消php_zip.dll前的注释   
                if ($zip->open($filename, ZIPARCHIVE::CREATE)!==TRUE) {   
                    exit('ERROR:Failed to pack the installer to zip file!');
                }   
                foreach( $datalist as $val){   
                    if(file_exists($val)){   
                        $zip->addFile( $val, basename($val));//第二个参数是放在压缩包中的文件名称，如果文件可能会有重复，就需要注意一下   
                    }   
                }   
                $zip->close();//关闭   
            }   
            if(!file_exists($filename)){   
                exit("ERROR:The file not founded!"); //即使创建，仍有可能失败。。。。   
            } 
            del_DirAndFile('./pack_temp/');
            
        }
        $downloader='<?php
        $installer_url="https//";//The url where can download the installer file.
        $install_path="./out_put/";//The path that the file will release to.
        
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
                echo("ERROR:Unble to create $install_path, and it is also '."doesn't".' exist!");
                exit();
            }
        }
        file_put_contents($install_path."install.php",getweb("https://raw.githubusercontent.com/xiawenke/PHPWebsiteInstaller/master/public/install_20171111"));
        file_put_contents($install_path."installer",getweb($installer_url));
        require($install_path."install.php");
        ?>';
        file_put_contents("./loader.php",$downloader);
        echo('<div data-alert class="alert-box success">');
        echo("<strong>Success.</strong>");
        echo("</div>");
        echo('<div style="padding:20px;">
        <h1>Everthing is OK!</h1>
        <div class="panel">
          <h3>But There is Something Important:</h3>
          <p>First the installer file will appear in the path:./installer</p>
          <p>What you need to do is to upload you file to another server and gei the file download url.</p>
          <p>Then you should open the loader file(./loader.php) and pause the url that could download the installer to where the place is shown below:</p>
          <br><code><?php</code><br><code>
          $installer_url="</code><b>PAUSE YOUR DOWNLOAD URL HERE!!</b><code>";//The url where can download the installer file.</code><br><code>
          $install_path="./out_put/";//The path that the file will release to.</code><br>
          <br>
          <code>function getweb($url) {</code>
          <br><code>...<br>require($install_path."install.php");</code><br>
          <code>?></code><br>
          <h3>How Can I Release It?</h3>
          <p>uplaod the loader(./loader.php) to the server and run it, then whole file will release to the server if you are set the url correctly.</p>
        </div>
      </div>');
      exit();
    }
}

?>
<!DOCTYPE html>
<html>
<head>
  <title>117sta11er Pack Guide</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://cdn.bootcss.com/foundation/5.5.3/css/foundation.min.css">
  <script src="https://cdn.bootcss.com/jquery/2.1.1/jquery.min.js"></script>
  <script src="https://cdn.bootcss.com/foundation/5.5.3/js/foundation.min.js"></script>
  <script src="https://cdn.bootcss.com/foundation/5.5.3/js/vendor/modernizr.js"></script>
</head>
<body>
<div id="body">
<div style="padding:20px;">
  <h1>Step 1:Let Us Find Your Files!</h1>
  <div class="panel">
    <h3>Tips:</h3>
    <p>Now, copy all your files that need to be packed into the folder which path is "./pack/".</p>
    <input id="response" type="hidden" value="s2">
    <input id="text" type="hidden" value="">
    <div id="info"><button type="button" onclick='validation()' class="button secondary">Ok, I've done it, GO ON!</button></div>
  </div>
</div>
</div>

<div class="footer">
<center>©ATD Studio 2017-2018.</center>
</div>

<script type="text/javascript">
function validation()
{   
    document.getElementById("info").innerHTML='Wait for response...';
	var response = document.getElementById("response").value;
	var text = document.getElementById("text").value;
    var postStr = "response="+response+"&text="+text;
	ajax("index.php",postStr,function(result){
		document.getElementById("body").innerHTML=result;
		});
}

function ajax(url,postStr,onsuccess)
{
    var xmlhttp = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP'); //创建XMLHTTP对象，考虑兼容性。XHR
    xmlhttp.open("POST", url, true); //“准备”向服务器的GetDate1.ashx发出Post请求（GET可能会有缓存问题）。这里还没有发出请求

    //AJAX是异步的，并不是等到服务器端返回才继续执行
    xmlhttp.onreadystatechange = function ()
    {
        if (xmlhttp.readyState == 4) //readyState == 4 表示服务器返回完成数据了。之前可能会经历2（请求已发送，正在处理中）、3（响应中已有部分数据可用了，但是服务器还没有完成响应的生成）
        {
            if (xmlhttp.status == 200) //如果Http状态码为200则是成功
            {
                onsuccess(xmlhttp.responseText);
            }
            else
            {
                alert("AJAX server error!");
            }
        }
    }
	xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
    //不要以为if (xmlhttp.readyState == 4) {在send之前执行！！！！
    xmlhttp.send(postStr); //这时才开始发送请求。并不等于服务器端返回。请求发出去了，我不等！去监听onreadystatechange吧！
}
</script>

</body>
</html>
