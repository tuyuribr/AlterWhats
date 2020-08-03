<?php
/*
AlterWhats.
Copyright (C) 2020  yuri at tadeu dot work

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <https://www.gnu.org/licenses/>.
*/
ini_set('memory_limit', '512M'); // change to your needs

date_default_timezone_set('UTC'); // make that will be 0 always ( for inserting messages )
$CFG['noAjax']=false;
/* start flush
this make the commands "more responsive" << output when executed 
if you encounter some problem, comment this block */
@apache_setenv('no-gzip', 1);
@ini_set('zlib.output_compression', 0);
@ini_set('implicit_flush', 1);
for ($i = 0; $i < ob_get_level(); $i++) { ob_end_flush(); }
ob_implicit_flush(1);
$CFG['noAjax'] = true; // noAjax dont kill ALL ajax in the website
/* end flush */


/*CONFIGS*/
$CFG['systemPath'] = "C:/xampp/htdocs/alterWhats/"; // the current path of the system ( php files )
$CFG['systemUrl'] = "http://127.0.0.1/alterWhats/"; // the system url ( to access via web ) ( default will be localhost or 127.0.0.1 ) we need the "http:" or "https:"



$CFG['adbPath']=$CFG['systemPath']."adb/adb.exe"; //the path to the adb handler ( if you are on a linux distro try only "adb" )
$CFG['whatsappDir']="C:/xampp/htdocs/alterWhats/currentWhatsapp/"; // the FULL PATH to the whatsapp dir
$CFG['whatsappBackupDir']="C:/xampp/htdocs/alterWhats/whatsappBackups/"; // the FULL PATH to the backups dir ( if you are going to import with the system )




$CFG['remoteWhatsappDir'] = "/data/data/com.whatsapp/"; //if you are having troubles, uncomment the line bellow
// $CFG['remoteWhatsappDir'] = "/data/user/0/com.whatsapp/"; 

$CFG['indirectPullPush'] = true; //if you cant pull/push to whatsapp dir set this to true to do via su commands and pull/push from /sdcard/ 
$CFG['indirectPullPushPath'] ="/sdcard/com.whatsapp/"; //path to pull/push if indirect IT cant end in "." because we add "." in the adb command it have to end in "/"

$CFG['tempPath']= "C:/xampp/htdocs/alterWhats/tempMedia/"; //path to the contact photos
$CFG['tempPathUrl']= "http://127.0.0.1/alterWhats/tempMedia/"; //url to the photos


$CFG['whatsappSdcardLoc'] = "/sdcard/Whatsapp/"; // path to the common user could access the whatsapp folder ( the folder that stores the media and backups )


$CFG['customSuPath']=false; // if not false set the path
$CFG['chown']=false;  //try to change the owner of the new transfered files
$CFG['ftsv2'] = true; // if you have the message_ftsv2 table keep this true, new whatsapp versions have this by default
$CFG['chownUser']="u0_a11"; //user << see who owns the whatsapp files in the phone: ls -l /data/data/com.whatsapp/
$CFG['chownGroup']="u0_a11"; //group



/* CONFIG MI 8 */
// $CFG['chown']=true;//try to change the owner of the new transfered files
// $CFG['chownUser']="u0_a292";//try to change the owner of the new transfered files
// $CFG['chownGroup']="u0_a292";//try to change the group owner of the new transfered files
// $CFG['ftsv2'] = true; // if you have the message_ftsv2 table keep this true, new whatsapp versions have this by default
/* END CONFIG MI 8 */


/* CONFIG ANDROID STUDIO EMULATOR (PIXEL)*/
// $CFG['chown']=false;//try to change the owner of the new transfered files
// $CFG['ftsv2'] = true; // if you have the message_ftsv2 table keep this true, new whatsapp versions have this by default
/* END CONFIG ANDROID STUDIO EMULATOR (PIXEL)*/



/* CONFIG bluestacks*/
// $CFG['chown']=true;
// $CFG['customSuPath']="/system/xbin/bstk/su"; 
// $CFG['ftsv2'] = true;
// $CFG['chownUser']="u0_a59";
// $CFG['chownGroup']="u0_a59";
/* END CONFIG bluestacks*/

require_once("functions.php");

$DB['msgstore']=null;
$DB['wa']=null;