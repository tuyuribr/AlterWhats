<?php


function newEcho($string,$type="js"){
	global $CFG;
	if($type == "js"){
		if($CFG['noAjax']){
			//sanitize ? <>
			$string = str_replace("document.getElementById","parent.document.getElementById",$string);
			echo "<script>$string</script>".PHP_EOL;
        }else{
			echo $string;
        }
    }
	
}
function delTree($dir) {
	// nbari at dalmp dot com 
	// https://www.php.net/manual/en/function.rmdir.php
   $files = array_diff(scandir($dir), array('.','..'));
    foreach ($files as $file) {
      (is_dir("$dir/$file")) ? delTree("$dir/$file") : unlink("$dir/$file");
    }
    return rmdir($dir);
} 

function sanitize($string,$type="default"){
	if(is_array($string)){
		$string = $string[0];//make recursive in future
	}
    switch ($type){
        case("default"):{
            return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
        }
        case("alphaNum"):{
            return preg_replace("/[^A-Za-z0-9?!]/",'',$string);
        }
        case("numbers"):{
            return preg_replace("/[^0-9?!]/",'',$string);
        }
        case("remoteJid"):{
            return preg_replace("/[^A-Za-z0-9\.@?!]/",'',$string);
        }
        case("file"):{
            $string = preg_replace("/[^A-Za-z0-9\/\\\.\?\=?!]/",'',$string);
            $string = str_replace("..",".",$string);
            return str_replace("..",".",$string);
        }
        case("removeLines"):{
            return preg_replace("/[\n\r]/"," ",$string);
        }
        case("slash"):{
            $string = str_replace("\\","\\\\",$string);
            $string = str_replace("'","\\'",$string);
            return str_replace('"','\\"',$string);
        }
    }

	return false;
}
function adb($command){
	global $CFG;
	exec($CFG['adbPath']." ".$command);
	return;
}
function getCurrentCustomSuPath(){
    global $CFG;
    $test = array("su","/system/xbin/bstk/su");
    foreach($test as $command){
        $result = exec($CFG['adbPath'] . " shell {$command} -c \"whoami\"");
        $result = strtolower($result);
        if(strpos($result,"root") !==false){
            $CFG['customSuPath'] = $command;
            return $command;
        }
    }
    die('[auto fail] invalid su path, set one in $CFG');
}
function getCurrentUserAndGroup(){
    global $CFG;
    if($CFG['customSuPath'] == false) {
        $CFG['customSuPath']="su";
    }elseif($CFG['customSuPath'] =="auto"){
        $CFG['customSuPath'] = getCurrentCustomSuPath();
    }

    $result = exec($CFG['adbPath'] . " shell {$CFG['customSuPath']} -c \"stat -c '%U' {$CFG['remoteWhatsappDir']}databases\"");
    $result = escapeshellarg(sanitize(trim($result),"removeLines"));
    $result = str_replace('"',"",$result);
    $result = str_replace("'","",$result);
    if(strlen($result) > 2){
        $CFG['chownUser'] = $result;
        $CFG['chownGroup'] = $result;
        return $result;
    }else{
        die('[auto fail] invalid chownUser, set one in $CFG');
    }


}
function adbShell($command,$onlyAdb=false){
	global $CFG;
	$exec = $CFG['adbPath'] . " shell ";
	if($onlyAdb){
		exec($exec . "\"{$command}\"");
		return;
	}
	if($CFG['customSuPath'] == false){
		$exec .='su -c "' . $command . '"';
	}else{
        if($CFG['customSuPath'] != "auto") {
            $exec .= $CFG['customSuPath'] . ' -c "' . $command . '"';
        }else{
            $exec .= getCurrentCustomSuPath() . ' -c "'.$command.'"';
        }
	}
	// echo $exec;die;
	exec($exec);
}
function appendTerminal($string){
	$string = sanitize($string,"slash");
	$string = 'document.getElementById("terminal").innerHTML=document.getElementById("terminal").innerHTML+"<br>'.$string.'";';
	return $string;
}
function garbageBackup($size){
	$size = (int)$size;
	$charset = "0123456789ABCDEF";
    $rand = substr(str_shuffle(str_repeat($charset, $size)), 0, $size);
	return $rand;
}
function garbage($size){
	$size = (int)$size;
	$charset = "0123456789ABCDEF";
    $charactersLength = 16;
    $randomString = '';
    for ($i = 0; $i < $size; $i++) {
        $randomString .= $charset[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}
function hex2str($hex) {
    $str = '';
    for($i=0;$i<strlen($hex);$i+=2) $str .= chr(hexdec(substr($hex,$i,2)));
    return $str;
	// (From http://www.linux-support.com/cms/php-convert-hex-strings-to-ascii-strings/) 
}

function recurseCopy($src,$dst) {
	 // gimmicklessgpt at gmail dot com ¶
	 // https://www.php.net/manual/en/function.copy.php#91010
    $dir = opendir($src);
    @mkdir($dst);
    while(false !== ( $file = readdir($dir)) ) {
        if (( $file != '.' )  && ( $file != '..' )) {
            if ( is_dir($src . '/' . $file) ) {
                recurseCopy($src . '/' . $file,$dst . '/' . $file);
            }
            else {
                copy($src . '/' . $file,$dst . '/' . $file);
            }
        }
    }
    closedir($dir);
} 

function checkRemoteJid($remoteJid){
	global $DB;
	$remoteJid = sanitize($remoteJid,"remoteJid");
	
	$stmt = $DB['msgstore']->prepare("SELECT _id FROM messages WHERE key_remote_jid = '{$remoteJid}' limit 1"); 
	$stmt->execute(); 
	$row = $stmt->fetch();
	if(empty($row['_id'])){
		return 0;
	}else{
		return 1;
	}
}

function loadMsgstore($dir){
	global $DB;
	
	try {$DB['msgstore'] = new PDO('sqlite:'.$dir);
		$DB['msgstore']->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);}catch(PDOException $e) {echo $e->getMessage();die;}
	
}
function closeMsgstore(){global $DB; $DB['msgstore']=null;}
function loadWA($dir){
	global $DB;
	
	try {$DB['wa'] = new PDO('sqlite:'.$dir);
		$DB['wa']->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);}catch(PDOException $e) {echo $e->getMessage();die;}		
}
function closeWa(){global $DB; $DB['wa']=null;}

function dataToFts($string){
	
	while(true){ //not hacky ahhahaha
		$tempS = $string;
	$string = preg_replace_callback("/(\S)[^a-zA-Z0-9 . \ \'](\s)/","checkWhiteSpace",$string);
	$string = preg_replace_callback("/(\s)[^a-zA-Z0-9 . \ \'](\S)/","checkWhiteSpace",$string);
	$string = preg_replace_callback("/(\S)[^a-zA-Z0-9 . \ \'](\S)/","checkWhiteSpace",$string);
		if($tempS == $string){	break;	}
	}

	$string = strtolower($string);
	$string = utf8_encode($string);
	
	return $string;
}
function checkWhiteSpace($matches){
	
	// print_r($matches);
	
	if(($matches[1]==" ") and ($matches[2]==" ")){
		return $matches[0];
	}
	if($matches[1]==" "){
		return str_replace($matches[2]," ".$matches[2],$matches[0]);
	}
	if($matches[2]==" "){
		return str_replace($matches[1],$matches[1]." ",$matches[0]);
	}
	if( !($matches[1]==" ") and !($matches[2]==" ")){
		$specialChar = str_replace($matches[1],"",$matches[0]);
		$specialChar = str_replace($matches[2],"",$specialChar);
		return $matches[1]." ".$specialChar." ".$matches[2];
	}
	
	// die;
}