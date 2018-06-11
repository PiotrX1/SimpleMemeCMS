<?php
	require_once('include.php');

	if(!$u->isLogged() || $u->admin == 0)
	{
		header("Location: /");
	}


    if(isset($_GET['delete']))
    {
        $id = $_GET['delete'];
        $stmt = $pdo->prepare("DELETE FROM categories WHERE id=:id");
        $stmt->execute(array('id' => $id));
        header("Location: kategorie");
    }

    if(isset($_POST['add']))
    {
        $name = $_POST['name'];
        $stmt = $pdo->prepare("INSERT INTO categories(`name`) VALUES(:name)");
        $stmt->execute(array('name' => $name));
        header("Location: kategorie");
    }

    
	require('content.php');
?>