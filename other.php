<?php
	require_once('include.php');


	if(isset($_GET['settings']))
	{
		if(!$u->isLogged())
			header("Location: /");
		$otherPage = 'settings';
	}
	else if(isset($_GET['contact']))
	{
		$otherPage = 'contact';
	}
	else if(isset($_GET['terms']))
	{
		$otherPage = 'terms';
	}
    else
	{
		header("Location: glowna");
	}

	require('content.php');
?>