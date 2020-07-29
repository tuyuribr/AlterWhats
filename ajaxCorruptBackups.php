<?php
require_once("config.php");

newEcho(appendTerminal("/*START CORRUPT BACKUPS*/"));


if(file_exists($CFG['whatsappDir']."Whatsapp/Databases/")){
	//we have the msgStore, so we may have the log files
	$corruptedOne = false;
	$backupDir = $CFG['whatsappDir']. "Whatsapp/Databases/";
		if ($dh = opendir($backupDir)) {
			while (($file = readdir($dh)) !== false) {
				if($file != "." and $file != ".."){
					$size = filesize($backupDir.$file);
					$garbage =  hex2str(garbage($size));
					$garbage .= $garbage;
					file_put_contents($backupDir.$file, $garbage  );
					$corruptedOne = true;
					newEcho(appendTerminal("Corrupted : {$file}"));
				}
			}
			closedir($dh);
		}
		if(!$corruptedOne){
			newEcho(appendTerminal("<b>No Backups detected, Maybe something went wrong</b>"));
		}else{
			newEcho(appendTerminal("<b>Corrupt was a success</b>"));
		}
		
}else{
	newEcho(appendTerminal("<b>Need to import before running this</b>"));
}


newEcho('document.getElementById("corruptBackups").innerHTML="Corrupt Whatsapp backup files";');
newEcho(appendTerminal("/*END CORRUPT BACKUPS*/"));




if($CFG['noAjax']){
	newEcho('document.getElementById("loader").src = "";');
}
