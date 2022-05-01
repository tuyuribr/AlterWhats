<?php
require_once("config.php");

newEcho(appendTerminal("/*START SEND MSGSTORE*/"));

newEcho(appendTerminal("KILLING WHATSAPP"));
adbShell("am force-stop com.whatsapp",true);

if($CFG['indirectPullPush']){
	
	newEcho(appendTerminal('RMing '.sanitize($CFG['indirectPullPushPath'])));
	adbShell("rm -rf {$CFG['indirectPullPushPath']}");
	
	newEcho(appendTerminal("Copying to phone"));
	adb("push {$CFG['whatsappDir']}/com.whatsapp/ {$CFG['indirectPullPushPath']}");
	
	
	// we will try to overwrite all hope we dont cause any issues ;
	// rm -rf -d /data/data/com.whatsapp/databases/*.db-shm
	// rm -rf -d /data/data/com.whatsapp/databases/*.db-wal
	
	// would be better if we created something to overwrite in the remote folder
	// newEcho(appendTerminal('RMing '.sanitize($CFG['remoteWhatsappDir'])."databases/*.db-shm")); 
	// adb("shell su -c \"rm -rf {$CFG['remoteWhatsappDir']}databases/*.db-shm\"");
	
	// newEcho(appendTerminal('RMing '.sanitize($CFG['remoteWhatsappDir'])."databases/*.db-wal")); 
	// adb("shell su -c \"rm -rf {$CFG['remoteWhatsappDir']}databases/*.db-wal\"");
	newEcho(appendTerminal("RMing remote msgstore.db-shm"));
	adbShell("rm {$CFG['remoteWhatsappDir']}databases/msgstore.db-shm");
	
	newEcho(appendTerminal("RMing remote msgstore.db-wal"));
	adbShell("rm {$CFG['remoteWhatsappDir']}databases/msgstore.db-wal");
	
	
	newEcho(appendTerminal("Overwriting to Remote whatsDir [databases]"));
	adbShell("cp -r {$CFG['indirectPullPushPath']}databases/. {$CFG['remoteWhatsappDir']}databases/");
	
	newEcho(appendTerminal("Overwriting to Remote whatsDir [files]"));
	adbShell("cp -r {$CFG['indirectPullPushPath']}files/. {$CFG['remoteWhatsappDir']}files/");
	
	newEcho(appendTerminal("RMing remote current log (after overwriting)"));
	adbShell("rm {$CFG['remoteWhatsappDir']}files/Logs/whatsapp.log");
	
	if($CFG['chown']){
        if($CFG['chownUser']=="auto"){
            $CFG['chownUser'] = getCurrentUserAndGroup();
            $CFG['chownGroup'] = $CFG['chownUser'];
        }
        newEcho(appendTerminal("chown all"));
        adbShell("chown -R {$CFG['chownUser']}:{$CFG['chownGroup']} {$CFG['remoteWhatsappDir']}");
	}
	
	newEcho(appendTerminal('RMing '.sanitize($CFG['indirectPullPushPath'])));
	adbShell("rm -rf {$CFG['indirectPullPushPath']}");
	

}else{
	newEcho("alert('not done yet')");
	// adb("push {$CFG['whatsappDir']}com.whatsapp/databases/msgstore.db {$CFG['remoteWhatsappDir']}databases/msgstore.db");
}

newEcho(appendTerminal("Overwriting Whatsapp Backups"));
	adb("push {$CFG['whatsappDir']}/Whatsapp/Databases/ {$CFG['whatsappSdcardLoc']}/");


newEcho(appendTerminal("/*END SEND ALL*/"));





newEcho('document.getElementById("sendAll").innerHTML="Send all the files back to whatsapp";');



if($CFG['noAjax']){
	newEcho('document.getElementById("loader").src = "";');
}