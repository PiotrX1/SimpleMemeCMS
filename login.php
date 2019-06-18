<?php
	require_once('include.php');

	if(isset($_POST['login']))
	{
		if(isset($_POST['username']) && isset($_POST['password']))
		{
            $u = new user();
            $u->username = strip_tags($_POST['username']);
            $u->password = $_POST['password'];
            exit($u->login());
		}
	}
    if(isset($_GET['logout']))
    {
        session_destroy();
        header('Location: index.php');
    }




?>
