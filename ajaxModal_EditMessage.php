<?php
require_once("config.php");


$keyId = sanitize($_GET['keyId'],'alphaNum');
$remoteJid = sanitize($_GET['remoteJid'],'remoteJid');
newEcho(appendTerminal("/*Building modal - Edit Message*/"));

	$fromMe = "error";
	$message = "error";
	$timestamp = time();
	
if(file_exists($CFG['whatsappDir']."com.whatsapp/databases/msgstore.db")){	
	newEcho(appendTerminal("Loading msgstore.db"));
	loadMsgstore($CFG['whatsappDir']."com.whatsapp/databases/msgstore.db");
	
	
	$stmt = $DB['msgstore']->prepare("SELECT text_data,timestamp,from_me FROM message_view WHERE key_id = '{$keyId}' limit 1");
	$stmt->execute(); 
	$row = $stmt->fetch();
	if(empty($row['timestamp'])){
		newEcho('alert("message not found")');
	}else{
	$message=sanitize($row['text_data']);
	$timestamp = substr($row['timestamp'], 0, -3);
		if($row['from_me']){
			$fromMe ="true";
		}else{
			$fromMe ="false";
		}
	}
	newEcho(appendTerminal("closing msgstore.db"));
	closeMsgstore();
	
}else{

	newEcho(appendTerminal("<b>Need to import before running this</b>"));
}



newEcho('document.getElementById("modalTitle").innerHTML="Edit Message";');

$modalBody = '<div class="form-group"><label for="data">Message:</label> <textarea class="form-control" id="dataEdit">'.$message.'</textarea></div>';
$modalBody .= 'From me : '.$fromMe;
$modalBody .= '<input type="hidden" name="keyId" value="'.$keyId.'" id="keyId">';
$modalBody .= '<input type="hidden" name="remoteJid" value="'.$remoteJid.'" id="remoteJidEdit">';
$timestamp = date("Y/m/d H:i:s",$timestamp);
$modalBody .= '<div class="form-group"> <label for="timestamp">Message Timestamp (GMT 0)</label> <input type="text" value="'.$timestamp.'" class="form-control" id="timestamp" disabled></div>';


$modalBody = sanitize($modalBody,"slash");
$modalBody = sanitize($modalBody,"removeLines");
newEcho('document.getElementById("modalBody").innerHTML="'.$modalBody.'";');


$buttons = '<button type="button" onclick="saveMessage()" class="btn btn-success" data-dismiss="modal">Save</button>';
$buttons .= '<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>';
$buttons = sanitize($buttons,"slash");
newEcho('document.getElementById("modalButtons").innerHTML="'.$buttons.'";');
newEcho(appendTerminal("/*End Building Modal*/"));
if($CFG['noAjax']){
	newEcho('document.getElementById("loader").src = "";');
}