<?php
	require_once('include.php');

	if(!$u->isLogged() || $u->admin == 0)
	{
		header("Location: /");
	}


	if(isset($_GET['ban']))
	{
		$users = new listUsers('ban=1');
		$pageType = "zbanowani";
	}
	else
	{
		$users = new listUsers();
		$pageType = "uzytkownicy";
	}

	$pages = $users->pages;
	if(isset($_GET['page']))
	{
		$page = intval($_GET['page']);
	}
	else
	{
		$page = 1;
	}

	$users = $users->load($page);

	require('content.php');
?>