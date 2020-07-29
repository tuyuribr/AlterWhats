<?php
require_once("config.php");


$remoteJid = sanitize($_GET['remoteJid'],'remoteJid');
newEcho(appendTerminal("/*Building modal - newMessage*/"));

newEcho('document.getElementById("modalTitle").innerHTML="New Message";');

$modalBody = '<div class="form-group"><label for="data">Message:</label> <textarea class="form-control" id="data"></textarea></div>';
$modalBody .= '<div class="checkbox"><label><input type="checkbox" id="fromMe" value=""> From me ?</label></div>';
$modalBody .= '<input type="hidden" name="remoteJid" value="'.$remoteJid.'" id="remoteJidForm">';
$timestamp = date("Y/m/d H:i:s");
$modalBody .= '<div class="form-group"> <label for="timestamp">Message Timestamp (GMT 0)</label> <input type="text" value="'.$timestamp.'" class="form-control" id="timestamp" disabled></div>';

$modalBody = sanitize($modalBody,"slash");
$modalBody = sanitize($modalBody,"removeLines");
newEcho('document.getElementById("modalBody").innerHTML="'.$modalBody.'";');


$buttons = '<button type="button" onclick="newMessageSave()" class="btn btn-success" data-dismiss="modal">Save</button>';
$buttons .= '<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>';
$buttons = sanitize($buttons,"slash");
newEcho('document.getElementById("modalButtons").innerHTML="'.$buttons.'";');
// newEcho(appendTerminal("/*End Building Modal*/"));