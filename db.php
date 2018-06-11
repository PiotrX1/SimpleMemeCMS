<?php
	require_once("configs.php");

	try
	{
		$pdo = new PDO('mysql:host='.$db['host'].';dbname='.$db['name'], $db['user'], $db['pass']);
		$pdo->query("SET CHARSET utf8");
	}
	catch (PDOException $e)
	{
		echo $e->getMessage();
		die();
	}
	
	
?>