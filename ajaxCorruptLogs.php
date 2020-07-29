<?php
require_once("config.php");

newEcho(appendTerminal("/*START CORRUPT LOGS*/"));


if(file_exists($CFG['whatsappDir']."com.whatsapp/databases/msgstore.db")){
	//we have the msgStore, so we may have the log files
	$corruptedOne = false;
	$logDir = $CFG['whatsappDir']. "com.whatsapp/files/Logs/";
		if ($dh = opendir($logDir)) {
			while (($file = readdir($dh)) !== false) {
				if($file != "." and $file != ".."){
					$size = filesize($logDir.$file);
					// file_put_contents($logDir.$file."_backup",file_get_contents($logDir.$file));
					$garbage =  hex2str(garbage($size));
					$garbage .= $garbage;
					file_put_contents($logDir.$file, $garbage  );
					$corruptedOne = true;
					newEcho(appendTerminal("Corrupted : {$file}"));
				}
			}
			closedir($dh);
		}
		if(!$corruptedOne){
			newEcho(appendTerminal("<b>No logs detected, Maybe something went wrong</b>"));
		}else{
			newEcho(appendTerminal("<b>Corrupt was a success</b>"));
		}
		
}else{
	newEcho(appendTerminal("<b>Need to import before running this</b>"));
}


newEcho('document.getElementById("corruptLogs").innerHTML="Corrupt log files";');
newEcho(appendTerminal("/*END CORRUPT LOGS*/"));




if($CFG['noAjax']){
	newEcho('document.getElementById("loader").src = "";');
}
// pull -a /sdcard/getWhatsapp/ 