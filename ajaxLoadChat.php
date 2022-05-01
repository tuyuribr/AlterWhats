<?php
require_once("config.php");


$remoteJid = sanitize($_GET['remoteJid'],'remoteJid');
if(empty($_GET['limit'])){
	$limit = 20;
}else{
	$limit = sanitize($_GET['limit'],"numbers");
}
if($limit > 1000 or $limit <= 0 ){ $limit = 20; }

newEcho(appendTerminal("/*START LOADCHAT*/"));


if(!empty($remoteJid)){

	if(file_exists($CFG['whatsappDir']."com.whatsapp/databases/msgstore.db")){	
		newEcho(appendTerminal("Loading msgstore.db"));
		loadMsgstore($CFG['whatsappDir']."com.whatsapp/databases/msgstore.db");
		$b64 = base64_encode($remoteJid);
		$table = "
		<h3> Conversation with : {$remoteJid} <button class='btn btn-info' onclick='loadChat(\"{$b64}\")'>[RELOAD]</button></h3>
		<table class='table table-bordered'>
		<thead>
		  <tr>
			<th>Contact Message</th>
			<th>Your Message</th>
			<th>Timestamp ( Y/m/d H:i:s )</th>
		  </tr>
		</thead>
		<tbody>";
		$i=0;
		$result = $DB['msgstore']->query("SELECT key_id,from_me,text_data,timestamp,message_type from message_view where chat_row_id in (select chat_view._id from chat_view where raw_string_jid ='{$remoteJid}') order by _id desc limit {$limit}");
		$data = array();
		foreach ($result as $row){
			$data[]=$row;
		}
		
		$data = array_reverse($data, true);
		foreach ($data as $row){
			$row['timestamp'] = substr($row['timestamp'], 0, -3); // need to remove last 3 chars ( ms timestamp)
			$keyId = sanitize($row['key_id'],"alphaNum");
			$fromMe = $row['from_me'];
			$data = $row['text_data'];
			$timestamp = $row['timestamp'];
			$timestamp =  date("Y/m/d H:i:s",$row['timestamp']);
			
				$data = sanitize($data);
				if($row['message_type'] != 0){
                    continue;
//                    $data = "<b>[MEDIA]</b><br>" . sanitize($row['media_caption']);
                }else{
					if(empty($data)){continue;}
				}
			
			
			if($row['message_type'] == 0){
				$table .="<tr onclick='editMsg(\"$keyId\")' style='cursor:pointer'>";
			}else{
				$table .="<tr>";
			}
			
			if($fromMe){
				$table .="<td></td><td>$data</td>";
			}else{
				$table .="<td>$data</td><td></td>";
			}
			
			$table .="
			<td>{$timestamp}</td>
			</tr>";
			$i++;
		}
		$table .="</tbody></table>
		<button class='btn btn-success' onclick='newMessage(\"{$b64}\")'>New Message</button>
		";
		$table = sanitize($table,"removeLines");
		$table = sanitize($table,"slash");
		newEcho(appendTerminal("<b>Loaded {$i} Message(s)</b>"));
		newEcho(appendTerminal("closing msgstore.db"));
		closeMsgstore();
		if($i >0){
		newEcho('document.getElementById("chatTable").innerHTML="'.$table.'";');
		}
	}else{
		newEcho(appendTerminal("<b>Need to import before running this</b>"));
	}
}else{
	newEcho(appendTerminal("<b>remoteJid is empty</b>"));
}

newEcho(appendTerminal("/*END LOADCHAT*/"));




if($CFG['noAjax']){
	newEcho('document.getElementById("loader").src = "";');
}