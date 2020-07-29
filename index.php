<?php

include("config.php");
include($CFG['systemPath']."head.php"); // this file "load" the HTML resources

?>
	<body>
		<div class="container-fluid">
			<div class="row">
				<?php include($CFG['systemPath']."leftBar.php"); ?>
					<div class="col-md-6 text-center" align="center">
						<h2>Select a chat</h2>
						<div id="chatTable" class="table-responsive">
						</div>
					</div>
				<?php include($CFG['systemPath']."rightBar.php"); ?>
			</div>
		</div>
		<iframe id="loader" style="display:none"></iframe>
		
		
		
		
		<div id="defaultModal" class="modal fade" role="dialog">
		  <div class="modal-dialog">
			<div class="modal-content">
			  <div class="modal-header">
				<h4 id="modalTitle" class="modal-title"></h4>
			  </div>
			  <div id="modalBody" class="modal-body">
				
			  </div>
			  <div id="modalButtons" class="modal-footer">
				<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
			  </div>
			</div>

		  </div>
		</div>
	</body>

	
	
	<script>
	function loadChat(remoteJid){
		remoteJid = atob(remoteJid);
		<?php if ($CFG['noAjax']){?>
		// load in an iframe ???? ahahahhahaha
		document.getElementById('loader').src = '<?php echo $CFG['systemUrl'] ?>ajaxLoadChat.php?remoteJid='+remoteJid;
		<?php }else{ ?> 
			$.ajax({
				type: 'GET',
				url: '<?php echo $CFG['systemUrl'] ?>ajaxLoadChat.php?remoteJid='+remoteJid,
				success: function(data) {
					 eval(data);
				}
			});

		<?php } ?>
	}
		
	function newMessage(remoteJid){
		remoteJid = atob(remoteJid);
		$('#defaultModal').modal('show'); 
		
		<?php if ($CFG['noAjax']){?>
		// load in an iframe ???? ahahahhahaha
		document.getElementById('loader').src = '<?php echo $CFG['systemUrl'] ?>ajaxModal_NewMessage.php?remoteJid='+remoteJid;
		<?php }else{ ?> 
			$.ajax({
				type: 'GET',
				url: '<?php echo $CFG['systemUrl'] ?>ajaxModal_NewMessage.php?remoteJid='+remoteJid,
				success: function(data) {
					 eval(data);
				}
			});

		<?php } ?>

	}
	function newMessageSave(){
		var remoteJid = document.getElementById("remoteJidForm").value;
		var data = document.getElementById("data").value;
		var fromMe = document.getElementById("fromMe").checked;
		data = escape(btoa(data));
		
		<?php if ($CFG['noAjax']){?>
		document.getElementById('loader').src = '<?php echo $CFG['systemUrl'] ?>ajaxNewMessage.php?remoteJid='+remoteJid+'&data='+data+'&fromMe='+fromMe;
		<?php }else{ ?> 
		//GET because is easier to do in the iframe
			$.ajax({
				type: 'GET',
				url: '<?php echo $CFG['systemUrl'] ?>ajaxNewMessage.php?remoteJid='+remoteJid+'&data='+data+'&fromMe='+fromMe,
				success: function(data) {
					 eval(data);
				}
			});
		<?php } ?>
		
	}
	
		function editMsg(keyId){
		$('#defaultModal').modal('show'); 
		
		<?php if ($CFG['noAjax']){?>
		// load in an iframe ???? ahahahhahaha
		document.getElementById('loader').src = '<?php echo $CFG['systemUrl'] ?>ajaxModal_EditMessage.php?keyId='+keyId;
		<?php }else{ ?> 
			$.ajax({
				type: 'GET',
				url: '<?php echo $CFG['systemUrl'] ?>ajaxModal_EditMessage.php?keyId='+keyId,
				success: function(data) {
					 eval(data);
				}
			});

		<?php } ?>

	}
	
	function saveMessage(){
		var keyId = document.getElementById("keyId").value;
		var data = document.getElementById("data").value;
		data = escape(btoa(data));
		
		<?php if ($CFG['noAjax']){?>
		// load in an iframe ???? ahahahhahaha
		document.getElementById('loader').src = '<?php echo $CFG['systemUrl'] ?>ajaxEditMessage.php?keyId='+keyId+'&message='+data;
		<?php }else{ ?> 
			$.ajax({
				type: 'GET',
				url: '<?php echo $CFG['systemUrl'] ?>ajaxEditMessage.php?keyId='+keyId+'&message='+data,
				success: function(data) {
					 eval(data);
				}
			});

		<?php } ?>

	}
	
	</script>
</html>