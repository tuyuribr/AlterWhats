<?php
require_once("config.php");

newEcho(appendTerminal("/*START CHECK FTSE*/"));

if(file_exists($CFG['whatsappDir']."com.whatsapp/databases/msgstore.db")){	
	newEcho(appendTerminal("Loading msgstore.db"));
	loadMsgstore($CFG['whatsappDir']."com.whatsapp/databases/msgstore.db");
	
	//v1 = message_fts_content
	//v2 = message_ftsv2_content
	
		

	
	newEcho(appendTerminal("closing msgstore.db"));
	closeMsgstore();
	
}else{
	newEcho(appendTerminal("<b>Need to import before running this</b>"));
}


newEcho('document.getElementById("corruptLogs").innerHTML="Check fts version";');
newEcho(appendTerminal("/*END CHECK FTS*/"));

if($CFG['noAjax']){
	newEcho('document.getElementById("loader").src = "";');
}

