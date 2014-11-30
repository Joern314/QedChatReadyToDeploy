<?php
$ignore_no_login = true;
$session_not_close = true;

require_once('common.php');

if(!userLoggedIn())
	redirect(urlLogin(chatOptions()));

$layout = uriParamString('layout', 'screen');
$frame = uriParamString('frame', '');

if($layout == 'mobile')
{
	$parts = array('mobile', 'receiver', 'send_mobile', 'settings', 'logs');
	require('template.php');
}
elseif($layout == 'screen')
{
	$parts = array('receiver', 'send', 'settings', 'logs');
	require('template.php');
}
elseif($layout == 'frames')
{
	if(in_array($frame, array('receiver', 'send', 'settings', 'logs')))
	{
		$parts = array('frames', $frame);
		require('template.php');
	}
	else require('framesneu.html');
}
else die("Unbekanntes Layout");

?>
