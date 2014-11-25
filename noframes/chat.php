<?php
$ignore_no_login = true;
$session_not_close = true;
require_once('common.php');
if(!userLoggedIn())
	redirect(urlLogin(chatOptions()));

if(isset($_GET['layout']) && $_GET['layout'] == 'mobile')
	readfile('mobilelayout.html');
else if(isset($_GET['layout']) && $_GET['layout'] == 'frames')
	redirect('https://chat.qed-verein.de/noframes/chat.php?' . http_build_query(chatOptions()));
else if(!isset($_GET['layout']) || $_GET['layout'] == 'screen')
	readfile('screenlayout.html');
?>
