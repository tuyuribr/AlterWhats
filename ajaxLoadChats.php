<?php
require_once("config.php");

newEcho(appendTerminal("/*START LOADCHATS*/"));

$i=0;
if(file_exists($CFG['whatsappDir']."com.whatsapp/databases/msgstore.db")){
	newEcho(appendTerminal("Loading msgstore.db"));
	loadMsgstore($CFG['whatsappDir']."com.whatsapp/databases/msgstore.db");
	$table = "<table class='table table-bordered'><tbody>";

	$result = $DB['msgstore']->query("SELECT c.key_remote_jid,c.subject,(SELECT m.data FROM messages as m where m._id = c.message_table_id) FROM chat_list as c order by c.message_table_id desc");
	$getNameAndPic=array();
	foreach($result as $row){
		$i++;
		$remoteJid=$row[0];
		$subject=sanitize($row[1]);
		$data=sanitize($row[2]);
		if(empty($subject)){
			$b64 = base64_encode($remoteJid);
			if(empty($data)){
				$data = "<b>[MEDIA]</b>";
			}
			// $table .="<tr  onclick='location.href=\"{$CFG['systemUrl'] }chat.php?remoteJid={$b64}\"' style='cursor:pointer'>";
			$table .="<tr  onclick='loadChat(\"{$b64}\")' style='cursor:pointer'>";
			$getNameAndPic[]= $remoteJid;
			$table .="<td>[PICTUREOF{$remoteJid}]</td>
			<td>[NAMEOF{$remoteJid}]<br>{$data}</td>
			</tr>";
		}else{
			$table .="<tr class='forceDanger'>";
			$table .="<td>GROUP</td>
			<td>{$subject}<br>{$data}</td>
		  </tr>";
		}
		
	}
	$table .="</tbody></table>";
	$table = sanitize($table,"removeLines");
	$table = sanitize($table,"slash");
	newEcho(appendTerminal("<b>Loaded {$i} chat(s)</b>"));
	
	newEcho(appendTerminal("closing msgstore.db"));
	closeMsgstore();
	
	if(count($getNameAndPic) > 0 ){
		
		if(file_exists($CFG['whatsappDir']."com.whatsapp/databases/wa.db")){
			newEcho(appendTerminal("Loading wa.db"));
			loadWA($CFG['whatsappDir']."com.whatsapp/databases/wa.db");
			
			foreach($getNameAndPic as $contact){
				//maybe sanitize
				
				$stmt = $DB['wa']->prepare("SELECT wa_name,sort_name FROM wa_contacts WHERE jid = '{$contact}'"); 
				$stmt->execute(); 
				$row = $stmt->fetch();
				if(!empty($row['sort_name'])){
					$table = str_replace("[NAMEOF{$contact}]",$row['sort_name']." - ".$contact,$table);
				}else{
					$table = str_replace("[NAMEOF{$contact}]",$row['wa_name']." - ".$contact,$table);
				}
				//copy photo
				//appears that is only ended in .j
				$photoPath = $CFG['whatsappDir']."com.whatsapp/files/Avatars/".$contact.".j";
				if(file_exists($photoPath)){
					newEcho(appendTerminal("Copying photo"));
					$newPhotoPath = $CFG['tempPath'].$contact.".j";
					copy($photoPath,$newPhotoPath);
					$newPhotoPath = $CFG['tempPathUrl'].$contact.".j";
				}else{
					$newPhotoPath = $CFG['systemUrl']."nophoto.png";
				}
				$table = str_replace("[PICTUREOF{$contact}]","<img src='{$newPhotoPath}' style='width:60px;height:60px;'>",$table);
				
				
			}
			newEcho(appendTerminal("closing wa.db"));
			closeWa();
		}else{
			newEcho(appendTerminal("<b>wa.db doesnt exists D: </b>"));
		}
	
	}
	newEcho('document.getElementById("chatTable").innerHTML="'.$table.'";');
		
}else{
	newEcho(appendTerminal("<b>Need to import before running this</b>"));
}


newEcho('document.getElementById("loadChats").innerHTML="Load Chats";');
newEcho(appendTerminal("/*END LOADCHATS*/"));




if($CFG['noAjax']){
	newEcho('document.getElementById("loader").src = "";');
}
