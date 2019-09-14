<?php

/*
Project Name: Vishesh Auto Index
Project Vendor: Vishesh Grewal
Project Version: 5.0
Licence: GPL v3
*/

if(file_exists('lock'))

{

die('<script language="JavaScript">alert("Do not Try To Patch Vishesh Auto Index Security. If you admin of This Website. Please delete lock file first")</script>');

}



/* Defines the root directory for VK.



	Uncomment the below line and set the path manually

	if you experience problems.



	Always add a trailing slash to the end of the path.



	* Path to your copy of VK

 */

//define('VK_ROOT', "./");



// Attempt autodetection

if(!defined('VK_ROOT'))

{

define('VK_ROOT', dirname(dirname(__FILE__))."/");

}



define("TIME_NOW", time());



if(function_exists('date_default_timezone_set') && !ini_get('date.timezone'))

{

date_default_timezone_set('GMT');

}

$message = '';



require_once VK_ROOT."inc/functions.php";



require_once VK_ROOT."inc/class_core.php";

$vk = new VK;



if(isset($vk->input['action'])  && $vk->input['action']  == 'do_install')

{

$fp = fopen(VK_ROOT.'inc/config.php','w');

$content = '<?php



// Database details

$config[\'database\'][\'type\'] = "mysqli";

$config[\'database\'][\'database\'] = "'.$vk->get_input('dbname').'";

$config[\'database\'][\'table_prefix\'] = "'.$vk->get_input('prefix').'";



$config[\'database\'][\'hostname\'] = "'.$vk->get_input('dbhost').'";

$config[\'database\'][\'username\'] = "'.$vk->get_input('dbuser').'";

$config[\'database\'][\'password\'] = "'.$vk->get_input('dbpassword').'";



/**

 * Admin CP directory

 *  For security reasons, it is recommended you

 *  rename your Admin CP directory. You then need

 *  to adjust the value below to point to the

 *  new directory.

 */



$config[\'admin_dir\'] ="vkadmin";



/**

 * Database Encoding

 *  If you wish to set an encoding for VK uncomment

 *  the line below (if it isnt already) and change

 *  the current value to the mysql charset:

 *  http://dev.mysql.com/doc/refman/5.1/en/charset-mysql.html

 */



$config[\'database\'][\'encoding\'] = "utf8";';



if(!fwrite($fp,trim($content)))

$message = 'Config.php can not be opened';

fclose($fp);



if(empty($message))

{

require_once VK_ROOT."inc/config.php";

require_once VK_ROOT."inc/db_".$config['database']['type'].".php";



switch($config['database']['type'])

{

case "sqlite":

$db = new DB_SQLite;

break;

case "pgsql":

$db = new DB_PgSQL;

break;

case "mysqli":

$db = new DB_MySQLi;

break;

default:

$db = new DB_MySQL;

}



// Check if our DB engine is loaded

if(!extension_loaded($db->engine))

{

// Throw our super awesome db loading error

$vk->trigger_generic_error("sql_load_error");

}



// Connect to Database

define("TABLE_PREFIX", $config['database']['table_prefix']);

$db->connect($config['database']);

$db->set_table_prefix(TABLE_PREFIX);

$db->type = $config['database']['type'];





$db->query("CREATE TABLE `".TABLE_PREFIX. "download_history` (

  `did` int(10) unsigned NOT NULL AUTO_INCREMENT,

  `fid` int(10) unsigned NOT NULL,

  `date` varchar(8) NOT NULL,

  `hits` int(5) unsigned NOT NULL DEFAULT '0',

  PRIMARY KEY (`did`)

) ENGINE=MyISAM DEFAULT CHARSET=utf8");



$db->query("CREATE TABLE `".TABLE_PREFIX. "files` (

  `fid` int(10) unsigned NOT NULL AUTO_INCREMENT,

  `pid` int(10) unsigned NOT NULL DEFAULT '0',

  `name` varchar(255) CHARACTER SET utf8 NOT NULL,

  `path` text CHARACTER SET utf8 NOT NULL,
   `title` text CHARACTER SET utf8 NOT NULL,
   `artist` text CHARACTER SET utf8 NOT NULL,
   `lyrics` text CHARACTER SET utf8 NOT NULL,
   `music` text CHARACTER SET utf8 NOT NULL,
   `label` text CHARACTER SET utf8 NOT NULL,

  `size` int(10) unsigned NOT NULL DEFAULT '0',

  `description` text CHARACTER SET utf8 NOT NULL,

  `views` int(10) unsigned NOT NULL DEFAULT '0',

  `dcount` int(10) unsigned NOT NULL DEFAULT '0',

  `time` int(10) unsigned NOT NULL DEFAULT '0',

  `disporder` smallint(5) unsigned NOT NULL,

  `isdir` tinyint(1) unsigned NOT NULL DEFAULT '0',

  `tag` tinyint(1) unsigned NOT NULL DEFAULT '0',

  `use_icon` tinyint(1) unsigned NOT NULL DEFAULT '0',

  PRIMARY KEY (`fid`)

) ENGINE=MyISAM DEFAULT CHARSET=utf8");



$db->query("CREATE TABLE `".TABLE_PREFIX. "settings` (

  `sid` smallint(5) unsigned NOT NULL AUTO_INCREMENT,

  `gid` smallint(5) unsigned NOT NULL,

  `title` varchar(120) NOT NULL,

  `name` varchar(120) NOT NULL,

  `value` text NOT NULL,

  `type` varchar(250) NOT NULL,

  `description` text NOT NULL,

  `optionscode` text NOT NULL,

  `disporder` smallint(5) unsigned NOT NULL DEFAULT '0',

  PRIMARY KEY (`sid`),

  UNIQUE KEY `name` (`name`)

) ENGINE=MyISAM AUTO_INCREMENT=37 DEFAULT CHARSET=utf8");



$db->query("INSERT INTO `".TABLE_PREFIX. "settings` VALUES ('1','1','Cookie Prefix','cookieprefix','vk_','text','Prefix to be added in cookies','','1')");



$db->query("INSERT INTO `".TABLE_PREFIX."settings` VALUES ('2','1','Cookie Domain','cookiedomain','','text','Cookie domain for which cookies will work','','2')");



$db->query(" INSERT INTO `".TABLE_PREFIX. "settings` VALUES ('3','1','Cookie Path','cookiepath','/','text','Path where the cookies will work','','3')");



$db->query("INSERT INTO `".TABLE_PREFIX. "settings` VALUES ('4','2','Site URL','url','{$vk->get_input('url')}','text','Site url to be used','','1')");

$title = $vk->get_input('title');



$db->query("INSERT INTO `".TABLE_PREFIX. "settings` VALUES ('5','2','Site Title','title','{$title} ','text','Site Title will be displayed on title bar,header,footer etc..','','2')");



$db->query("INSERT INTO `".TABLE_PREFIX. "settings` VALUES ('6','2','Site Logo','logo','{$vk->get_input('logo')}','text','Logo image path or url...','','3')");

$db->query(" INSERT INTO `".TABLE_PREFIX. "settings` VALUES ('7','2','Fb Page Link','fbpagename','','text','Fb page username without slash(/)..','','4')");



$db->query("INSERT INTO `".TABLE_PREFIX. "settings` VALUES ('8','2','Show Searchbox','show_searchbox','1','yesno','Show searchbox on index & filelist pages?','','5')");



$db->query("INSERT INTO `".TABLE_PREFIX. "settings` VALUES ('9','2','Maximum Paging Link','maxmultipagelinks','5','text','Number of page links to show','','6')");



$db->query("INSERT INTO `".TABLE_PREFIX. "settings` VALUES ('11','3','Updates on Page','updates_per_page','12','select','Total number of update messages to show on updates page?','\r\n10=10\r\n11=11\r\n12=12\r\n13=13\r\n14=14\r\n15=15','2')");



$db->query("INSERT INTO `".TABLE_PREFIX. "settings` VALUES ('10','3','Updates on Index','updates_on_index','8','select','Total number of update messages to show on index page?','\r\n5=5\r\n6=6\r\n7=7\r\n8=8\r\n9=9\r\n10=10','1')");



$db->query("INSERT INTO `".TABLE_PREFIX. "settings` VALUES ('12','4','Related files per Page','related_files_per_page','12','select','Select total number of folders to show','\r\n5=5\r\n6=6\r\n7=7\r\n8=8\r\n9=9\r\n10=10\r\n11=11\r\n12=12\r\n13=13','1')");



$db->query("INSERT INTO `".TABLE_PREFIX. "settings` VALUES ('13','4','Files Per Page','files_per_page','8','select','Number of files to show in filelist page','\r\n5=5\r\n6=6\r\n7=7\r\n8=8\r\n9=9\r\n10=10\r\n11=11\r\n12=12\r\n13=13','3')");



$db->query("INSERT INTO `".TABLE_PREFIX. "settings` VALUES ('14','4','Default Sort Option','sort','new2old','select','Select default sorting option for files','\r\nnew2old=New to Old\r\na2z=A to Z\r\nz2a=Z to A\r\ndownload=Most Download','3')");



$db->query("INSERT INTO `".TABLE_PREFIX. "settings` VALUES ('15','0','Admin Password','adminpass','".sha1($vk->get_input('adminpass'))."','ap','The admincp password','','1')");



$db->query("INSERT INTO `".TABLE_PREFIX. "settings` VALUES ('16','4','Show Total File','show_filecount','0','yesno','Show total number of files after folder name?','','3')");



$db->query("INSERT INTO `".TABLE_PREFIX. "settings` VALUES ('17','5','Watermark Thumb','watermark_thumb','1','yesno','Watermark generated/uploaded thumbs','','1')");



$db->query("INSERT INTO `".TABLE_PREFIX. "settings` VALUES ('18','5','Watermark Images','watermark_images','1','yesno','Watermark uploaded images?','','2')");



$db->query("INSERT INTO `".TABLE_PREFIX. "settings` VALUES ('19','5','Watermark Videos','watermark_videos','1','yesno','Watermark uploaded videos?','','3')");



$db->query("INSERT INTO `".TABLE_PREFIX. "settings` VALUES ('20','5','Watermark Image','watermark_image','/assets/images/logo.png','text','Image to be watermarked on video...','','4')");



$db->query("INSERT INTO `".TABLE_PREFIX. "settings` VALUES ('22','6',' Auto Tag','auto_tag','1','yesno','Auto tags mp3 files with default tags','','1')");



$db->query("INSERT INTO `".TABLE_PREFIX. "settings` VALUES ('24','6','Song Year','mp3_year','2019','text','','','3')");



$db->query("INSERT INTO `".TABLE_PREFIX. "settings` VALUES ('23','6','Auto Bitrate','auto_bitrate','1','yesno','Auto convert bitrate of MP3 files','','2')");



$db->query("INSERT INTO `".TABLE_PREFIX. "settings` VALUES ('25','6','Composer','mp3_composer','{$title}','text','','','4')");



$db->query("INSERT INTO `".TABLE_PREFIX. "settings` VALUES ('21','5','Watermark text','watermark_text','{$title}','text','Text to be watermarked on thumbs/images','','5')");



$db->query("INSERT INTO `".TABLE_PREFIX. "settings` VALUES ('26','6','Publishers','mp3_publisher','{$title}','text','','','5')");



$db->query("INSERT INTO `".TABLE_PREFIX. "settings` VALUES ('27','6','Artist','mp3_artist','{$title}','text','','','6')");



$db->query("INSERT INTO `".TABLE_PREFIX. "settings` VALUES ('28','6','Album Art','mp3_albumart','assets/images/logo.png','text','','','7')");



$db->query("INSERT INTO `".TABLE_PREFIX. "settings` VALUES ('29','6','Genre','mp3_genre','{$title}','text','','','7')");



$db->query("INSERT INTO `".TABLE_PREFIX. "settings` VALUES ('30','6','Band','mp3_band','{$title}','text','','','8')");



$db->query("INSERT INTO `".TABLE_PREFIX. "settings` VALUES ('31','6','Track','mp3_track','{$title}','text','','','9')");



$db->query("INSERT INTO `".TABLE_PREFIX. "settings` VALUES ('32','4','Related Files','related_files','1','yesno','','','2')");



$db->query("INSERT INTO `".TABLE_PREFIX. "settings` VALUES ('33','6','Encoded By','mp3_encoded_by','{$title}','text','','','11')");



$db->query("INSERT INTO `".TABLE_PREFIX. "settings` VALUES ('34','6','Original Artist','mp3_original_artist','{$title}','text','','','12')");



$db->query("INSERT INTO `".TABLE_PREFIX. "settings` VALUES ('35','6','Comment','mp3_comment','Downloaded From {$title}','text','','','13')");



$db->query("INSERT INTO `".TABLE_PREFIX. "settings` VALUES ('36','6','User url','mp3_url_user','{$title}','text','','','14')");



$db->query("INSERT INTO `".TABLE_PREFIX. "settings` VALUES ('37','2','EMAIL','email','{$email}','text','Site Admin EMAIL Address','','7')");


$db->query("CREATE TABLE `".TABLE_PREFIX. "settingsgroups` (

  `gid` smallint(5) unsigned NOT NULL AUTO_INCREMENT,

  `title` varchar(120) NOT NULL,

  `description` text NOT NULL,

  `disporder` smallint(5) unsigned NOT NULL DEFAULT '0',

  PRIMARY KEY (`gid`)

) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8");



$db->query("INSERT INTO `".TABLE_PREFIX. "settingsgroups` VALUES ('1','Cookie Settings','Set cookie prefix,path or domain...','1')");



$db->query("INSERT INTO `".TABLE_PREFIX. "settingsgroups` VALUES ('2','General Settings','Edit various settings like title,url etc..','2')");



$db->query("INSERT INTO `".TABLE_PREFIX. "settingsgroups` VALUES ('3','Updates Settings','Updates settings like updates per page etc...','3')");



$db->query("INSERT INTO `".TABLE_PREFIX. "settingsgroups` VALUES ('4','Files Settings','Change file per page,sort order etc..','4')");



$db->query("INSERT INTO `".TABLE_PREFIX."settingsgroups` VALUES ('5','Watermark Settings','Set various options for watermark','5')");



$db->query("INSERT INTO `".TABLE_PREFIX. "settingsgroups` VALUES ('6','Mp3 Settings','Set various options for mp3 files','6')");



$db->query("CREATE TABLE `".TABLE_PREFIX. "updates` (

  `uid` int(10) unsigned NOT NULL AUTO_INCREMENT,

  `description` text NOT NULL,

  `created_at` int(10) unsigned NOT NULL DEFAULT '0',

  `status` varchar(1) NOT NULL DEFAULT 'A',

  PRIMARY KEY (`uid`)

) ENGINE=MyISAM DEFAULT CHARSET=utf8");
$error=500;
if($error)
echo "Fatal Error!<br/> Check if <b>/inc/settings.php</b> is writable and if your prefix is correct.";
$fp = fopen('lock', 'w');
fclose($fp);
header('Location: http://vkay.tk');
}}
else
{
echo '
<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Vishesh Auto Index Installation Wizard</title>
<meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;" />
<link rel="icon" href="https://3.bp.blogspot.com/-z1jE3E7RHXc/Wz2zMWlSNGI/AAAAAAAAAos/V4ZP8vmpZDIm4RW5ZdsOrVvJUuuKFzbOwCPcBGAYYCw/s1600/sitelogo.png" sizes="16x16" type="image/png">
<link rel="stylesheet" type="text/css" href="https://raw.githubusercontent.com/vkaygrewal/Vishesh-Auto-Index-files/master/install/vishesh.css" />
<style>
body{ background-color: #000000; font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 11px; }body,td,th { font-size: 11px; color: #ffffff;
 -webkit-user-select: none;  /* Chrome all / Safari all */
  -moz-user-select: none;     /* Firefox all */
  -ms-user-select: none;      /* IE 10+ */
  user-select: none;          /* Likely future */
}
form{ background-color: #000000;}
vishesh{ background-color: #000000;}
.headervishesh { background-color: #797979; border:none; color:#fff; height:39px; padding:0px; line-height: 39px; border-bottom: solid 3px #E4A915; font-weight: bold; font-size:20px; font-color:white; } 
.header { padding:10px;  background: linear-gradient(to right, #33ccff 0%, #ff99cc 100%); repeat-x; color:#F5F5F5; border:1px solid #595959; font-weight:bold; font-size:15px; margin-top:1px; } 
.header:hover{
    background: linear-gradient(to top right, #ff0000 0%, #000000 100%);
}
 .google_search { min-height: 100px;}
 .google_search2 { min-height: 60px; width:100%; }
 .google_search2 form { padding: 17px; padding-left: 0; }
 .google_search2 input[type=text] { width: 80%; max-width:300px; padding: 7px; border-radius: 9px; border: 2px solid #eee; } 
.google_search2 input[type=submit] { width: 100%; padding: 6px; background: #ff0000 none repeat scroll 0 0; border: 4px solid #0063c9; font-size: 20px; border-radius: 9px; color: #fff; box-shadow: 1px 2px 5px #003; min-width: 60px; cursor: pointer; }
.red{background: none repeat scroll 0% 0% #000000;
border-bottom: 1px solid #DDD;
padding: 7px 11px;
font-size: 15px;
font-weight: bold;
color: #118B71;
line-height: 24px;}
.footer { background-color: #797979; border:none; color:#fff; height:35px; padding:0; line-height: 35px; border-bottom: solid 2px red; text-align: center; }
.footer::after{ content: "Designed by Vishesh Grewal";
}
</style>
</head>
<body>

<div class="headervishesh"> &nbsp; <a href="http://visheshgrewal.blogspot.com"><font color="white">Vishesh Auto Index</font></a>&nbsp;</div>
<br/>
<div class="header"><center>Installation Wizard<br/>
</center></div>
<div class="google_search2">
<form action="#" method="post">
<center>
<div class="red">
DataBase Host:-
<input type="text" name="dbhost" value="localhost" /><br/>
<vishesh>Insert your MySQL Database Host. Usually its <em>localhost</em> or <em>mysql.xxxx.ext</em></vishesh>
</div>
<div class="red">
<font color="green">DataBase User:-</font>
<input type="text" name="dbuser" value="" /><br/>
<vishesh>Insert your Database User Name</vishesh>
</div>
<div class="red">
<font color="green">DataBase Password:-</font>
<input type="text" name="dbpassword" value="" /><br/>
<vishesh>Insert your Database Password</vishesh>
</div>
<div class="red">
<font color="green">DataBase Name:-</font>
<input type="text" name="dbname" value="" /><br/>
<vishesh>Insert your Database Name</div>
</vishesh>
<div class="red">
<font color="green">DataBase Table Prefix:-</font>
<input type="text" name="prefix" value="" /><br/>
<vishesh>If you do not know this then keep it blank.</div>
</vishesh>
<div class="header">WebSite Setup</div>
<div class="red">
<font color="green">WebSite Title:-</font> 
<input type="text" name="title" value="" /><br/>
<vishesh>The name of your WebSite</div>
</vishesh>
<div class="red">
<font color="green">WebSite Url:- </font>
<input type="text" name="url" value="http://" /><br/>
<vishesh>The full URL to your WebSite <b>without</b> any slash(/) at end.</div>
</vishesh>
<div class="red">
<font color="green">WebSite Logo:- </font>
<input type="text" name="logo" value="/assets/images/logo.png" /><br/>
<vishesh>The full URL to your logo. You can always change this later.</div>
</vishesh>
<div class="red">
<font color="green">Admin Password:- </font><br/>
<input type="password" name="adminpass" value="" /><br/>
<vishesh>Insert your Admin Password for WebSite Index</vishesh>
</div><br/>
<input type="hidden" name="action" value="do_install" />
<input type="submit"  value="Install" /></center>
</form>
</div>
</div>';
}
?>
<div class="footer">
 <?php echo date('Y'); ?>  &#169; <a href="http://visheshgrewal.blogspot.com/"  id="#visheshcredit" alt="Vishesh-Auto-Index"><font color="white">A Vishesh Grewal Project</font></a> |&nbsp;</div> <br/>

<script type="text/javascript">
//<![CDATA[
$(document).ready(function()
{
var aa=$("#visheshcredit").val();
if (aa == null) {
window.location.href = "http://visheshgrewal.blogspot.com/";
};
$("#visheshcredit").attr("href","http://visheshgrewal.blogspot.com/");
});
//]]>
</script>

</body>
</html>
