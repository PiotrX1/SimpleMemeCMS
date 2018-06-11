<?php
	require_once('include.php');


	if(isset($_GET['category']))
	{
		$stmt = $pdo->prepare("SELECT * FROM categories WHERE name=:name LIMIT 1");
		$stmt->execute(array('name' => $_GET['category']));

		$data = $stmt->fetch();

		if($_GET['category'] != 'oczekujace')
		{

			if($data == null)
			{
				// Brak takiej kategorii
				$data[0] = 1;
				$data[1] = 'strona';
			}

		}
		else
		{
			$data[0] = '0';
			$data[1] = 'c/oczekujace';
		}

		$list = new postList($data[0]);
		$pageType = $data[1];
		$menu = $data[0];


	}
	else if(isset($_GET['tag']))
	{
		$tag = $_GET['tag'];
		$pageType = "tag/$tag";
		$list = new postList(null, null, $tag);


		if($u->isLogged())
		{
			$stmt = $pdo->prepare("SELECT * FROM followedtags WHERE user=:user AND tag=:tag");
			$stmt->execute(array('user' => $u->id, 'tag' => $tag));

			if($stmt->fetch()['id'])
				$followed = true;
			else
				$followed = false;
		}

	}
	else if(isset($_GET['user']))
	{
		$userP = $_GET['user'];
		$pageType = "u/$userP";
		


		$stmt = $pdo->prepare("SELECT id FROM users WHERE username=:user");
		$stmt->execute(array('user' => $userP));

		$id = $stmt->fetch()['id'];
		if($id)
			$list = new postList(null, null, '', 'posts.id DESC', $id);
		else
			header("Location: ../");
		

	}
	else if(isset($_GET['top']))
	{
		$pageType = "top";
		$list = new postList(null, null, '', 'rate DESC');
		$menu = -1;
	}

	else if(isset($_GET['id']))
	{
		$id = (int)$_GET['id'];
		$list = new postList(null, $id);


		$stmt = $pdo->prepare('SELECT title FROM posts WHERE id=:id');
		$stmt->execute(array('id' => $id));
		$title = $stmt->fetch()['title'] . ' - ' . $title;
	}
	else if(isset($_GET['own']))
	{
		if(!$u->isLogged())
			header("Location: /");

		$pageType = "moje";
		$list = new postList(null, null, '', 'posts.id DESC', $u->id);
	}
	else if(isset($_GET['favorites']))
	{
		if(!$u->isLogged())
			header("Location: /");
		
		$pageType = "ulubione";
		$list = new postList(null, null, '', 'posts.id DESC', null, $u->id);

	}
	else
	{
		$pageType = 'strona';
		$list = new postList();
		$menu = 1;
	}


	$pages = $list->pages;
	if(isset($_GET['page']))
	{
		$page = intval($_GET['page']);
	}
	else
	{
		$page = 1;
	}
	$list->load($page);

	require('content.php');
?>