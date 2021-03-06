<?php
function get_zip_originalsize($filename, $path) {
    //先判断待解压的文件是否存在
    //mkdir(('tools/doc_read/temp/'.$filename.'/'),0777,true);
    if(!file_exists($filename)){
     die("Installer $filename doesn't exist!");
    } 
    $starttime = explode(' ',microtime()); //解压开始的时间
   
    //将文件名和路径转成windows系统默认的gb2312编码，否则将会读取不到
    $filename = iconv("utf-8","gb2312",$filename);
    $path = iconv("utf-8","gb2312",$path);
    //打开压缩包
    $resource = zip_open($filename);
    $i = 1;
    //遍历读取压缩包里面的一个个文件
    while ($dir_resource = zip_read($resource)) {
     //如果能打开则继续
     if (zip_entry_open($resource,$dir_resource)) {
      //获取当前项目的名称,即压缩包里面当前对应的文件名
      $file_name = $path.zip_entry_name($dir_resource);
      //以最后一个“/”分割,再用字符串截取出路径部分
      $file_path = substr($file_name,0,strrpos($file_name, "/"));
      //如果路径不存在，则创建一个目录，true表示可以创建多级目录
      if(!is_dir($file_path)){
       mkdir($file_path,0777,true);
      }
      //如果不是目录，则写入文件
      if(!is_dir($file_name)){
       //读取这个文件
       $file_size = zip_entry_filesize($dir_resource);
       
        $file_content = zip_entry_read($dir_resource,$file_size);
        file_put_contents($file_name,$file_content);
      }
      //关闭当前
      zip_entry_close($dir_resource);
     }
    }
    //关闭压缩包
    zip_close($resource); 
    $endtime = explode(' ',microtime()); //解压结束的时间
    $thistime = $endtime[0]+$endtime[1]-($starttime[0]+$starttime[1]);
    $thistime = round($thistime,3); //保留3为小数
    echo "<p>Release temp successfully, in $thistime s。</p>";
    //mkdir("./temp/$filename/","0777",true);
    //unlink($filename);
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

if(!mkdir("./release_temp/", 0777)){
    del_DirAndFile("./release_temp/");
    if(!mkdir("./release_temp/", 0777)){
        echo("ERROR:Cannot create release temp folder!");
        exit();
    }
}
get_zip_originalsize('installer','./release_temp/');


$install=json_decode(file_get_contents('./release_temp/_install.json'));

//还原文件夹结构。
$folder=$install->folders;
$count=count($folder);
$i=0;
while(1){
    $count=$count-1;
    echo $folder[$count];
    if(!mkdir($folder[$count],0777,true)){
        echo("ERROR:Failed to create the folders!(Path:".$folder[$count].")");
        //exit();
    }
    if($count<=0){
        break;
    }
}


//还原文件结构。
$file=$install->files;
//print_r($file);

$count=count($file);
while(1){
    $count=$count-1;
    $temp=$file[$count];
    $type=$temp->type;
    if($type=='text'){
        $path=$temp->path;
        if(!fopen($path,'w+')){
            echo("Failed to create file in $path !");
            //exit();
        }
        file_put_contents($path,$temp->content);
    }//如果文件类型为文本文件。
    if($type=='other'){
        if(!copy("./release_temp/".$temp->content,$temp->path)){
            echo("Failed to release file in $temp->path");
            exit();
        }
    }//如果文件为二进制文件。

    if($count<=0){
        break;
    }
}

del_DirAndFile("./release_temp/");
echo("Install successfully!");
