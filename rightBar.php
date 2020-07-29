<div class="col-md-4 text-center" align="center">
<h2>Last commands</h2>
<br>
<br>
<button type="button" onclick="autoScrollC()" id="autoScrollB" class="btn btn-info">Enable autoScroll</button>&nbsp;&nbsp;
<button type="button" onclick="clearLog()" class="btn btn-warning">Clear Log</button><br>
<div id="terminal" style="background-color:#c2c2c2;overflow:auto;height:500px;" >&nbsp;</div>
</div>
<script>
function clearLog(){
	document.getElementById("terminal").innerHTML = "&nbsp;";
}
var autoScroll =0;

function autoScrollC(){
	if(autoScroll == 0){
		document.getElementById("autoScrollB").innerHTML = "Disable autoScroll";
		autoScroll =1;
	}else{
		document.getElementById("autoScrollB").innerHTML = "Enable autoScroll";
		autoScroll =0;
	}
	
}
 var terminalDiv = document.getElementById('terminal');
window.setInterval(function() {
 if(autoScroll){
  terminalDiv.scrollTop = terminalDiv.scrollHeight;
 }
}, 150);

</script>