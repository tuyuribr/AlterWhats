<?php if(empty($CFG['systemUrl'])){echo "we need to fill the system Url"; die;} ?>
<div class="col-md-2 text-center" align="center">
<h2>Select a function</h2>
<br>
<br>
<button type="button" id="importWhatsapp" onclick="importWhatsapp();" class="btn btn-info">Import whatsappDir from adb</button><br><br>
<button type="button" id="loadChats" onclick="loadChats();" class="btn btn-success">Load Chats</button><br><br>
<button type="button" id="checkFts" onclick="checkFts();" class="btn btn-warning">Check fts version</button><br><br>
<button type="button" id="corruptLogs" onclick="corruptLogs();" class="btn btn-danger">Corrupt log files</button><br><br>
<button type="button" id="corruptBackups" onclick="corruptBackups();" class="btn btn-danger">Corrupt Whatsapp backup files</button><br><br>
<button type="button" onclick="sendMsgstore()" id="sendMsgstore" class="btn btn-success">Send only msgstore.db</button><br><br>
<button type="button" class="btn btn-success" onclick="sendAll();" id="sendAll">Send all the files back to whatsapp</button><br><br>
</div>
<script>
function checkFts(){
document.getElementById("checkFts").innerHTML="Loading...";
<?php if ($CFG['noAjax']){?>
// load in an iframe ???? ahahahhahaha
document.getElementById('loader').src = '<?php echo $CFG['systemUrl'] ?>ajaxCheckFts.php';
<?php }else{ ?> 

	$.ajax({
		type: 'GET',
		url: '<?php echo $CFG['systemUrl'] ?>ajaxCheckFts.php',
		success: function(data) {
			 eval(data);
		}
	});

<?php } ?>
}


function importWhatsapp(){
document.getElementById("importWhatsapp").innerHTML="Loading...";
<?php if ($CFG['noAjax']){?>
// load in an iframe ???? ahahahhahaha
document.getElementById('loader').src = '<?php echo $CFG['systemUrl'] ?>ajaxImportWhatsappDir.php';
<?php }else{ ?> 

	$.ajax({
		type: 'GET',
		url: '<?php echo $CFG['systemUrl'] ?>ajaxImportWhatsappDir.php',
		success: function(data) {
			 eval(data);
		}
	});

<?php } ?>
}

function corruptLogs(){
document.getElementById("corruptLogs").innerHTML="Loading...";
<?php if ($CFG['noAjax']){?>
// load in an iframe ???? ahahahhahaha
document.getElementById('loader').src = '<?php echo $CFG['systemUrl'] ?>ajaxCorruptLogs.php';
<?php }else{ ?> 

	$.ajax({
		type: 'GET',
		url: '<?php echo $CFG['systemUrl'] ?>ajaxCorruptLogs.php',
		success: function(data) {
			 eval(data);
		}
	});

<?php } ?>
}
function corruptBackups(){
document.getElementById("corruptBackups").innerHTML="Loading...";
<?php if ($CFG['noAjax']){?>
// load in an iframe ???? ahahahhahaha
document.getElementById('loader').src = '<?php echo $CFG['systemUrl'] ?>ajaxCorruptBackups.php';
<?php }else{ ?> 

	$.ajax({
		type: 'GET',
		url: '<?php echo $CFG['systemUrl'] ?>ajaxCorruptBackups.php',
		success: function(data) {
			 eval(data);
		}
	});

<?php } ?>
}
function loadChats(){
	document.getElementById("loadChats").innerHTML="Loading...";
<?php if ($CFG['noAjax']){?>
// load in an iframe ???? ahahahhahaha
document.getElementById('loader').src = '<?php echo $CFG['systemUrl'] ?>ajaxLoadChats.php';
<?php }else{ ?> 

	$.ajax({
		type: 'GET',
		url: '<?php echo $CFG['systemUrl'] ?>ajaxLoadChats.php',
		success: function(data) {
			 eval(data);
		}
	});

<?php } ?>
}
function sendMsgstore(){
	document.getElementById("sendMsgstore").innerHTML="Loading...";
<?php if ($CFG['noAjax']){?>
// load in an iframe ???? ahahahhahaha
document.getElementById('loader').src = '<?php echo $CFG['systemUrl'] ?>ajaxSendMsgstore.php';
<?php }else{ ?> 

	$.ajax({
		type: 'GET',
		url: '<?php echo $CFG['systemUrl'] ?>ajaxSendMsgstore.php',
		success: function(data) {
			 eval(data);
		}
	});

<?php } ?>
}

function sendAll(){
	document.getElementById("sendAll").innerHTML="Loading...";
<?php if ($CFG['noAjax']){?>
// load in an iframe ???? ahahahhahaha
document.getElementById('loader').src = '<?php echo $CFG['systemUrl'] ?>ajaxSendAll.php';
<?php }else{ ?> 

	$.ajax({
		type: 'GET',
		url: '<?php echo $CFG['systemUrl'] ?>ajaxSendAll.php',
		success: function(data) {
			 eval(data);
		}
	});

<?php } ?>
}

</script>
