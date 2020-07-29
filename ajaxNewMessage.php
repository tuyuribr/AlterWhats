<?php
require_once("config.php");

newEcho(appendTerminal("/*START NEW MESSAGE*/"));
$remoteJid = sanitize($_GET['remoteJid'],'remoteJid');
$b64 = base64_encode($remoteJid);
$data = base64_decode($_GET['data']); // maybe sanitize to not explode the db ? ( current is sanitizing in PDO )
$dataToFtsv = dataToFts($data);

$data = utf8_encode($data);
if(file_exists($CFG['whatsappDir']."com.whatsapp/databases/msgstore.db")){	
	newEcho(appendTerminal("Loading msgstore.db"));
	loadMsgstore($CFG['whatsappDir']."com.whatsapp/databases/msgstore.db");
	
	if(checkRemoteJid($remoteJid)){
	
		if($_GET['fromMe'] == "true"){
			$keyFromMe = "1";
		}else{
			$keyFromMe = "0";
		}
		$keyId = garbage(32);
		$timestamp = time()."000";
		$receivedTimestamp = $timestamp + rand(222,9999);
		if($keyFromMe != "1"){
			$keyFromMe ="0";
			$timestampServer="-1";
			$sendCount = "null";
			$status = 0;
			$receivedTimestamp2 = "-1";
			$remoteRead = "null";
		}else{
			$timestampServer = (time() + rand(1,5)). "000"; //when the server got the message
			$sendCount = 1;
			$status = 13; // Msg seen, change to 5 if the person dont have read enabled ( we need to make an option for this )
			
			
			$receiptDevice = (time() + rand(6,30));
			$remoteRead = $receiptDevice + rand (9,30) ;
			$remoteRead = $remoteRead . "000"; // change to null if the person dont have read enabled
			$receiptDevice = $receiptDevice . "000";
			$receivedTimestamp2 = $receiptDevice;
			$timestamp = $timestamp + rand(1,221);
			
		}
		
		
		
		$stmt = $DB['msgstore']->prepare("SELECT _id FROM messages WHERE key_remote_jid = '{$remoteJid}' order by _id desc limit 1"); 
		$stmt->execute(); 
		$row = $stmt->fetch();
		$lastId = $row['_id'];
		
		
		$sql = $DB['msgstore']->prepare("INSERT INTO messages 
		(key_remote_jid , key_from_me, key_id, data,timestamp,received_timestamp, status, needs_push,media_wa_type,media_size,media_duration,origin,latitude,longitude,send_timestamp,receipt_server_timestamp,receipt_device_timestamp,send_count,recipient_count,quoted_row_id,edit_version,forwarded,preview_type,read_device_timestamp) 
		values 
		('{$remoteJid}', {$keyFromMe}, '{$keyId}',:newMessage,{$timestamp},{$receivedTimestamp},{$status},0,0,0,0,0,0,0,-1,{$timestampServer},{$receivedTimestamp2},{$sendCount},0,0,0,0,0,{$remoteRead})");
		$sql->bindParam(':newMessage', $data, PDO::PARAM_STR);
		$sql->execute();
		$insertId = $DB['msgstore']-> lastInsertId();
		newEcho(appendTerminal("INSERT messages [{$insertId}] (msgstore.db)")); // this need to be after because of Id
		
		if($CFG['ftsv2']){
			
			$stmt = $DB['msgstore']->prepare("SELECT c1fts_jid FROM message_ftsv2_content WHERE docid = '{$lastId}'");  // maybe we should get as keyFromMe
			$stmt->execute(); 
			$row = $stmt->fetch();
			$c1ftsJid = $row['c1fts_jid'];
			if(empty($c1ftsJid) or ( strlen($c1ftsJid) < 2 ) ){$c1ftsJid = "0 d";}
			/*
			this is auto
			// $sql = $DB['msgstore']->prepare("INSERT INTO message_ftsv2_content (docid,c0content,c1fts_jid,c2fts_namespace)
			// values ({$insertId},:newMessage,'{$c1ftsJid}','')");
			// $sql->bindParam(':newMessage', $dataToFtsv, PDO::PARAM_STR);
			// $sql->execute();
			// newEcho(appendTerminal("INSERT message_ftsv2_content (msgstore.db)"));
			*/
			// $size= "020200";
			$size= "0".rand(1,6)."0200";
			
			newEcho(appendTerminal("INSERT message_ftsv2_docsize (msgstore.db)"));
			$sql = $DB['msgstore']->prepare("INSERT INTO message_ftsv2_docsize (docid,size) values ({$insertId},X'{$size}')"); // no doc for this, insert as hex
			$sql->execute();
			
			
			
			
			newEcho(appendTerminal("INSERT message_ftsv2 (msgstore.db)"));
			$sql = $DB['msgstore']->prepare("INSERT INTO message_ftsv2 (content, fts_jid, fts_namespace)
			values (:newMessage,'{$c1ftsJid}','')");
			$sql->bindParam(':newMessage', $dataToFtsv, PDO::PARAM_STR);
			$sql->execute();
			
		}else{
			$stmt = $DB['msgstore']->prepare("SELECT c1fts_jid FROM message_fts_content WHERE docid = '{$lastId}'");  // maybe we should get as keyFromMe
			$stmt->execute(); 
			$row = $stmt->fetch();
			$c1ftsJid = $row['c1fts_jid'];
			if(empty($c1ftsJid) or ( strlen($c1ftsJid) <= 2 ) ){$c1ftsJid = "0 d";}
			
			$size= "0".rand(1,6)."0200";
			newEcho(appendTerminal("INSERT message_fts_docsize (msgstore.db)"));
			$sql = $DB['msgstore']->prepare("INSERT INTO message_fts_docsize (docid,size) values ({$insertId},X'{$size}')"); // no doc for this, insert as hex
			$sql->execute();
			
			
			
			
			newEcho(appendTerminal("INSERT message_fts (msgstore.db)"));
			$sql = $DB['msgstore']->prepare("INSERT INTO message_fts (content, fts_jid, fts_type)
			values (:newMessage,'{$c1ftsJid}','')");
			$sql->bindParam(':newMessage', $dataToFtsv, PDO::PARAM_STR);
			$sql->execute();
			
		}
		newEcho(appendTerminal("INSERT messages_fts (msgstore.db)"));
		$sql = $DB['msgstore']->prepare("INSERT INTO messages_fts (content)
		values (:newMessage)");
		$sql->bindParam(':newMessage', $dataToFtsv, PDO::PARAM_STR);
		$sql->execute();
		
		// newEcho(appendTerminal("INSERT messages_fts_content (msgstore.db)"));
		// $sql = $DB['msgstore']->prepare("INSERT INTO messages_fts_content (docId,c0content)
		// values ({$insertId},:newMessage)");
		// $sql->bindParam(':newMessage', $dataToFtsv, PDO::PARAM_STR);
		// $sql->execute();
		
		$stmt = $DB['msgstore']->prepare("SELECT _id FROM jid WHERE raw_string = '{$remoteJid}'");
		$stmt->execute(); 
		$row = $stmt->fetch();
		$jidId = $row['_id'];
		
		
		$stmt = $DB['msgstore']->prepare("UPDATE chat set display_message_row_id = {$insertId}, last_message_row_id = {$insertId},last_read_message_row_id = {$insertId}, last_read_receipt_sent_message_row_id = {$insertId} where jid_row_id = '{$jidId}'");
		$stmt->execute(); 
		newEcho(appendTerminal("UPDATE chat (msgstore.db)"));
		
		
		$stmt = $DB['msgstore']->query("UPDATE chat_list set message_table_id = {$insertId},last_read_message_table_id = {$insertId},last_read_receipt_sent_message_table_id ={$insertId},last_message_table_id = {$insertId} where key_remote_jid ='{$remoteJid}' ");
		$stmt->execute(); 
		newEcho(appendTerminal("UPDATE chat_list (msgstore.db)"));
			
			
		newEcho(appendTerminal("<b>SUCCESS</b>"));
		
		
	
	}else{
		newEcho(appendTerminal("<b>remote jid not found</b>"));
	}

	newEcho(appendTerminal("closing msgstore.db"));
	closeMsgstore();
	

}else{
	newEcho(appendTerminal("<b>Need to import before running this</b>"));
}

newEcho(appendTerminal("/*END NEW MESSAGE*/"));

if($CFG['noAjax']){
	newEcho('document.getElementById("loader").src = "";');
}
if($CFG['noAjax']){
	newEcho("parent.loadChat('{$b64}');");
}else{
	newEcho("loadChat('{$b64}');");
}