<?php
require_once("config.php");

newEcho(appendTerminal("/*START SEND MSGSTORE*/"));

newEcho(appendTerminal("KILLING WHATSAPP"));
adbShell("am force-stop com.whatsapp",true);

if($CFG['indirectPullPush']){
	
	newEcho(appendTerminal('RMing '.sanitize($CFG['indirectPullPushPath'])));
	adbShell("rm -rf {$CFG['indirectPullPushPath']}");
	
	newEcho(appendTerminal("Copying to phone"));
	adb("push {$CFG['whatsappDir']}com.whatsapp/databases/msgstore.db  {$CFG['indirectPullPushPath']}/msgstore.db");
	
	
	newEcho(appendTerminal("RMing remote msgstore.db"));
	adbShell("rm {$CFG['remoteWhatsappDir']}databases/msgstore.db");
	
	newEcho(appendTerminal("RMing remote msgstore.db-shm"));
	adbShell("rm {$CFG['remoteWhatsappDir']}databases/msgstore.db-shm");
	
	newEcho(appendTerminal("RMing remote msgstore.db-wal"));
	adbShell("rm {$CFG['remoteWhatsappDir']}databases/msgstore.db-wal");
	
	
	newEcho(appendTerminal("Copying to Remote whatsDir"));
	adbShell("mv {$CFG['indirectPullPushPath']}/msgstore.db {$CFG['remoteWhatsappDir']}databases/msgstore.db");
	
	if($CFG['chown']){
		newEcho(appendTerminal("chown msgstore"));
		adbShell("chown -R {$CFG['chownUser']}:{$CFG['chownGroup']} {$CFG['remoteWhatsappDir']}/databases");
	}
	newEcho(appendTerminal('RMing '.sanitize($CFG['indirectPullPushPath'])));
	adbShell("rm -rf {$CFG['indirectPullPushPath']}");
	

}else{
	newEcho(appendTerminal("Copying to Local"));
	adb("push {$CFG['whatsappDir']}com.whatsapp/databases/msgstore.db {$CFG['remoteWhatsappDir']}databases/msgstore.db");
}



newEcho(appendTerminal("/*END SEND MSGSTORE*/"));





newEcho('document.getElementById("sendMsgstore").innerHTML="Send only msgstore.db";');



if($CFG['noAjax']){
	newEcho('document.getElementById("loader").src = "";');
}