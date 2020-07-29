<?php
require_once("config.php");

newEcho(appendTerminal("/*START IMPORTING*/"));


newEcho(appendTerminal("KILLING WHATSAPP"));
adbShell("am force-stop com.whatsapp",true);

newEcho(appendTerminal('DELETING '.sanitize($CFG['whatsappDir'])));
delTree($CFG['whatsappDir']);
mkdir($CFG['whatsappDir']);

if($CFG['indirectPullPush']){
	
	newEcho(appendTerminal('RMing '.sanitize($CFG['indirectPullPushPath'])));
	adbShell("rm -rf {$CFG['indirectPullPushPath']}");
	
	newEcho(appendTerminal('cp to'.sanitize($CFG['indirectPullPushPath'])));
	adbShell("cp -r {$CFG['remoteWhatsappDir']} {$CFG['indirectPullPushPath']}");
	
	// newEcho(appendTerminal('CHMODING'));
	// adb("shell su -c \"chmod 777 -R {$CFG['indirectPullPushPath']}\"");
	
	newEcho(appendTerminal("Copying to Local"));
	adb("pull {$CFG['indirectPullPushPath']}. {$CFG['whatsappDir']}/com.whatsapp/");
	
	newEcho(appendTerminal('RMing '.sanitize($CFG['indirectPullPushPath'])));
	adbShell("rm -rf {$CFG['indirectPullPushPath']}");
}else{
	newEcho(appendTerminal("Copying to Local"));
	adb("pull {$CFG['remoteWhatsappDir']}. \"{$CFG['whatsappDir']}/com.whatsapp/\"");
}

mkdir($CFG['whatsappDir']."/Whatsapp/");
newEcho(appendTerminal("Copying User backup databases"));
adb("pull {$CFG['whatsappSdcardLoc']}Databases/. {$CFG['whatsappDir']}Whatsapp/Databases/");
	
	
if(file_exists($CFG['whatsappDir']."com.whatsapp/databases/msgstore.db")){
	newEcho(appendTerminal("<b>Import was a success</b>"));
	$backupDir = $CFG['whatsappBackupDir'].time();
	mkdir($backupDir);
	newEcho(appendTerminal("Backuping data to {$backupDir}"));
	// recurseCopy($CFG['whatsappDir']."com.whatsapp/",$backupDir);
	recurseCopy($CFG['whatsappDir'],$backupDir);
	
}else{
	newEcho(appendTerminal("<b>Import failed</b>"));
}


newEcho('document.getElementById("importWhatsapp").innerHTML="Import whatsappDir from adb";');
newEcho(appendTerminal("/*END IMPORTING*/"));




if($CFG['noAjax']){
	newEcho('document.getElementById("loader").src = "";');
}
