<?php
	require_once('include.php');

	if(isset($_POST['checkName']))
	{
		$name = $_POST['checkName'];
		
		$stmt = $pdo->prepare('SELECT id FROM users WHERE username=:username');
		$stmt->execute(array('username' => $name));
		
		if($stmt->fetch()['id'])
			echo json_encode('Nazwa użytkownika jest zajęta');
		else
			echo json_encode(true);
		
		
	}
	else if(isset($_POST['checkEmail']))
	{
		$email = $_POST['checkEmail'];
		
		$stmt = $pdo->prepare('SELECT id FROM users WHERE email=:email');
		$stmt->execute(array('email' => $email));
		
		if($stmt->fetch()['id'])
			echo json_encode('Adres email jest już używany');
		else
			echo json_encode(true);
		
		
	}

	if(isset($_POST['register']))
	{
		if(isset($_POST['username']) && isset($_POST['email']) && isset($_POST['password1']) && isset($_POST['password2']))
		{
			$username = $_POST['username'];
			$email = $_POST['email'];

			$password1 = $_POST['password1'];
			$password2 = $_POST['password2'];

			if($password1 == $password2 && strlen($password1) >= 6)
			{
				$u = new user();
				$u->username = $username;
				$u->email = $email;
				$u->password = $password1;
				if($u->register())
				{
					echo json_encode(true);
				}

			}
			else
			{
				echo json_encode(false);
			}
		}
	}




?>