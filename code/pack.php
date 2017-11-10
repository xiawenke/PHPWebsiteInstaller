<?php
/**
ATD Installer.
V1.0.0Beta.(20171111)
需要php扩展fileinfo。
**/
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
        echo "<br /> ";
        if ( $handle = opendir( "$dirName" ) ) {  
          while ( false !== ( $item = readdir( $handle ) ) ) {  
              if ( $item != "." && $item != ".." ) {  
                  if ( is_dir( "$dirName/$item" ) ) {  
                      del_DirAndFile( "$dirName/$item" );  
                  } else {  
                      if( unlink( "$dirName/$item" ) )echo "已删除临时文件: $dirName/$item<br /> ";  
                  }  
              }  
          }  
      closedir( $handle );  
     if( rmdir( $dirName ) ) echo "已删除临时目录: $dirName<br /> ";  
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

$folder='test';
$file_array = recurAllFiles($folder);
$folder_array=recurFolders($folder);

$json['folders']=$folder_array;
$json['files']=note_files($file_array);

$json=json_encode($json);var_dump(json_last_error());
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
/**  
header("Cache-Control: public"); 
header("Content-Description: File Transfer"); 
header('Content-disposition: attachment; filename='.basename($filename)); //文件名   
header("Content-Type: application/zip"); //zip格式的   
header("Content-Transfer-Encoding: binary"); //告诉浏览器，这是二进制文件    
header('Content-Length: '. filesize($filename)); //告诉浏览器，文件大小   
@readfile($filename);
**/
del_DirAndFile('./pack_temp/');

?>
