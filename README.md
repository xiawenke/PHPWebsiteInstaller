# PHPWebsiteInstaller / 117sta11er
Pack up all the code and files, then release in the server.<br>
For how to use it please learn about it from the PackGuide(./Pack_Guide.php).<br>\
<b>The most impaotant is that the code based on PHP and you must add the PHP extension called php_fileinfo, or you will fail to pack the installer.<br>
  
# Introduction
The program pack all your code together into a installer.<br>
It will put all the text file together into one file "instapp.json".<br>
Then rename other binary files by their MD5 value, and mark their path before they be packed.<br>
When release them, the program will follow the path then restore all the files, however it's a text or binary file.<br>
In fact, the installer is a ZIP file, so that's why you need add php_fileiinfo!<br>
ＦＯＲ　ＨＯＷ　ＴＯ　ＵＳＥ　ＩＴ，　ＰＬＥＡＳＥ　ＲＥＡＤ　ＢＥＬＯＷ，　ＡＮＤ  ＴＲＹ　ＴＯ　ＵＳＥ　ＴＨＥ　ＰａｃｋＧｕｉｄｅ！<br>

# PackGuide
That file is save in ./Pack_Guide.php<br>
You can download this file and run it with PHP, Then follow the instraction to create a installer and learn how to use it.<br>

# code
./code/install.php  This code is used to install the Installer, but you donnot need to download and run it on your own, with the loader that create by the PackGuide will download and run it automatically.<br>
./code/loader.php  This code is used to do download and install automatically in the server, you donnot need to download it if you use the PackGuide, because the PackGuide will create this file automatically without you do anything, and the PackGuide will also teach you how to use it.<br>
./pack.php  This code is used to pack the all you code into a Installer, but you also donnot need to download it if you use the PackGuide, because this code is included in PackGuide(Pack_Guide.php).<br>

# public
./public/demo_intaller  This file just a demo of the Installer, you can use it to try how the code use, but you may net care about it.<br>
./public/install_20171111/  This code is just the copy of ./code/install when it's in 2017.11.11, and it used for the loader(loader.php) to download the install.php from Github, so you don't need to care about it.<br>
./public/releasefilepath.json  This file just used for developer to check the verson of the code so you needn't care about it.
