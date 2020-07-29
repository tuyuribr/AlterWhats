<?php
require_once("config.php");

newEcho(appendTerminal("/*START EDIT MESSAGE*/"));
$keyId = sanitize($_GET['keyId'],'alphaNum');
$data = base64_decode($_GET['message']); // maybe sanitize to not explode the db ? ( current is sanitizing in PDO )
$dataToFtsv = dataToFts($data);
// $dataToFtsv = utf8_encode($dataToFtsv);
$data = utf8_encode($data);
if(file_exists($CFG['whatsappDir']."com.whatsapp/databases/msgstore.db")){	
	newEcho(appendTerminal("Loading msgstore.db"));
	loadMsgstore($CFG['whatsappDir']."com.whatsapp/databases/msgstore.db");
	
	$stmt = $DB['msgstore']->prepare("SELECT _id,data,key_remote_jid FROM messages WHERE key_id = '{$keyId}' limit 1"); // limit 1 just to be sure 
	$stmt->execute(); 
	$row = $stmt->fetch();
	$messageId = $row['_id'];
	$messageOldData = $row['data'];
	$b64 = base64_encode($row['key_remote_jid']);
	if(!empty($messageId)){
		newEcho(appendTerminal("UPDATE messages [{$messageId}] (msgstore.db)"));
		$stmt = $DB['msgstore']->prepare("Update messages set data = :newMessage where _id ='{$messageId}' ");
		$stmt->bindParam(':newMessage', $data, PDO::PARAM_STR);
		$stmt->execute();
		if($CFG['ftsv2']){
			$stmt = $DB['msgstore']->prepare("SELECT c0content FROM message_ftsv2_content WHERE docid = '{$messageId}' limit 1"); // limit 1 just to be sure 
			$stmt->execute(); 
			$row = $stmt->fetch();
			$messageContent = $row['c0content'];
			// $c1ftsJid = $row['c1fts_jid'];

			newEcho(appendTerminal("UPDATE message_ftsv2_content [{$messageId}] (msgstore.db)"));
			$stmt = $DB['msgstore']->prepare("UPDATE message_ftsv2_content set c0content = :newMessage where docid ='{$messageId}' ");
			$stmt->bindParam(':newMessage', $dataToFtsv, PDO::PARAM_STR);
			$stmt->execute();
			//message_ftsv2 dont have any keys so we need to use "rowid" to update
			newEcho(appendTerminal("UPDATE message_ftsv2 (msgstore.db)"));
			// $stmt = $DB['msgstore']->prepare("UPDATE message_ftsv2 set content = :newMessage where content = :oldContent and fts_jid = :ftsJid ");
			$stmt = $DB['msgstore']->prepare("UPDATE message_ftsv2 set content = :newMessage where rowid = '{$messageId}' ");
			$stmt->bindParam(':newMessage', $dataToFtsv, PDO::PARAM_STR);
			// $stmt->bindParam(':oldContent', $messageContent, PDO::PARAM_STR);
			// $stmt->bindParam(':ftsJid', $c1ftsJid, PDO::PARAM_STR);
			$stmt->execute();
			
		}else{
			$stmt = $DB['msgstore']->prepare("SELECT c0content FROM message_fts_content WHERE docid = '{$messageId}' limit 1"); // limit 1 just to be sure 
			$stmt->execute(); 
			$row = $stmt->fetch();
			$messageContent = $row['c0content'];
			// $c1ftsJid = $row['c1fts_jid'];
			
			newEcho(appendTerminal("UPDATE message_fts_content [{$messageId}] (msgstore.db)"));
			$stmt = $DB['msgstore']->prepare("UPDATE message_fts_content set c0content = :newMessage where docid ='{$messageId}' ");
			$stmt->bindParam(':newMessage', $dataToFtsv, PDO::PARAM_STR);
			$stmt->execute();
			//message_fts dont have any keys so we need to use "rowid" to update
			newEcho(appendTerminal("UPDATE message_fts (msgstore.db)"));
			$stmt = $DB['msgstore']->prepare("UPDATE message_fts set content = :newMessage where rowid = '{$messageId}' ");
			$stmt->bindParam(':newMessage', $dataToFtsv, PDO::PARAM_STR);
			$stmt->execute();
			
		}
		//messages_fts dont have any keys so we need to use "rowid" to update
		newEcho(appendTerminal("UPDATE messages_fts (msgstore.db)"));
		$stmt = $DB['msgstore']->prepare("UPDATE messages_fts set content = :newMessage where rowid = '{$messageId}'");
		$stmt->bindParam(':newMessage', $dataToFtsv, PDO::PARAM_STR);
		$stmt->execute();
		
		newEcho(appendTerminal("UPDATE messages_fts_content (msgstore.db)"));
		$stmt = $DB['msgstore']->prepare("UPDATE messages_fts set content = :newMessage where docid = '{$messageId}'");
		$stmt->bindParam(':newMessage', $dataToFtsv, PDO::PARAM_STR);
		$stmt->execute();
		newEcho(appendTerminal("<b>Message updated</b>"));
		
	}else{
		newEcho("alert('message not found');");
	}
	
	newEcho(appendTerminal("closing msgstore.db"));
	closeMsgstore();
	

	
}else{
	newEcho(appendTerminal("<b>Need to import before running this</b>"));
}


newEcho(appendTerminal("/*END EDIT MESSAGE*/"));

if($CFG['noAjax']){
	newEcho('document.getElementById("loader").src = "";');
}

if($CFG['noAjax']){
	newEcho("parent.loadChat('{$b64}');");
}else{
	newEcho("loadChat('{$b64}');");
}