<?php
	function formSecure($value)
	{
		return htmlspecialchars(addslashes($value));
	}


	function strip_script($string)
	{
		$string = preg_replace("/<script[^>]*>.*?< *script[^>]*>/i", "", $string);
		$string = preg_replace("/<script[^>]*>/i", "", $string);
		$string = preg_replace("/<style[^>]*>.*<*style[^>]*>/i", "", $string);
		$string = preg_replace("/<style[^>]*>/i", "", $string);
		$string = htmlentities($string);
		return $string;
	}

	class post
	{	
		private $data; 
		
		function __construct($d)
		{
			$this->data = $d;	
		}	
		public function display()
		{

			$date = new DateTime($this->data['date']);
           	$this->data['date'] = $date->format('d.m.Y H:i');
			$this->data['diff'] = timeDiff($date);

			if(strlen($this->data['tags']) > 0)
				$tags = explode(',', $this->data['tags']);
			else
				$tags = array();

			/* Wygląd 1 pojedynczego posta */
			echo '<article>		
					<div class="col-md-8 float-left">
						<div class="card card-block post">
						
							<header>
								<a href="post/'.$this->data['id'].'/'. str_replace('.', '', str_replace(' ', '-', $this->data['title'])) .'"><h4 class="card-title float-left">'.$this->data['title'].'</h4></a>'

								.(isset($_SESSION['user']) ? '<a class="changeFavorite" data-id="'.$this->data['id'].'"><h4 class="card-title float-right"><i class="fa '.($this->data['fav'] ? 'fa-star' : 'fa-star-o') .'" aria-hidden="true"></i></h4></a>' : '').
								'
							</header>
							<div>
								<div class="padding-0">
									<p class="tags float-left">';

			foreach($tags as $value)
			{
				echo '<a class="tagLink" href="tag/'.$value.'">#'.$value.'</a> ';
			}
									echo '</p>
									<p class="date float-right" title="'. $this->data['date'] . '"><a href="u/'. $this->data['username'] . '"><strong>'. $this->data['username'] . '</strong></a> dodał ' .$this->data['diff'].'</p>
								</div>
							</div>'. 
							(strlen($this->data['text']) > 0 ? '<p class="card-text clearfix">'.$this->data['text'].'</p>' : '') .	
							'<div class="postContent padding-0">';

			if($this->data['type']=='yt')
			{
				echo '<iframe src="'.$this->data['object'].'?rel=0" allowfullscreen="allowfullscreen"></iframe>';
			}
			else if($this->data['type']=='video')
			{
				echo '<video width="100%"  controls><source src="'.$this->data['object'].'" type="video/mp4"></video>';
			}
			else
			{
				echo '<img src="'.$this->data['object'].'" alt="" onerror="this.style.height = 0;">';
			}

			global $address;
				echo '<button class="col-md-12">Rozwiń</button>
							</div>
							<div class="rate">
								<button class="btn btn-plus col-1 border-0 rate-post" data-id="'. $this->data['id'] .'" data-mark="+"><i class="fa fa-thumbs-up" aria-hidden="true"></i></button>
								<span class="rating" data-id="'.$this->data['id'].'">'.$this->data['rate'].'</span>
								<button class="btn btn-minus col-1 border-0  rate-post" data-id="'. $this->data['id'] .'" data-mark="-"><i class="fa fa-thumbs-down" aria-hidden="true"></i></button>
								
								<a class="float-right showComments" data-id="'. $this->data['id'] .'" data-toggle="collapse" href="#comments'.$this->data['id'].'">
									<span class="fa fa-comments" aria-hidden="true"></span> Komentarze 
									<span class="badge badge-default" data-id="'. $this->data['id'] .'">
									'. $this->data['commentsCount'] .'</span>
								</a>
								<a class="float-right share" data-id="'.$this->data['id'].'"><i class="fa fa-share-alt" aria-hidden="true"></i> Udostępnij</a>
							</div>
							<div class="col-12 shareLink" data-id="'.$this->data['id'].'" style="display: none;">
								<input class="bg-dark col-12 text-center" type="text" value="'.$address.'/post/'.$this->data['id'].'/'. str_replace('.', '', str_replace(' ', '-', $this->data['title'])) .'" disabled>
							</div>
							';
			if(isset($_SESSION['user']) && $_SESSION['user']['admin'] > 0)
			{
				global $categories;
				




				echo'<div class="col-12"><h6>Akcje administracyjne</h6></div>
				<div data-id="'. $this->data['id'] .'" class="col-12 row margin-padding-0 adminAction" style="padding: 0 5px 0 5px !important;">';

				echo '
				<select id="moveToCategory" name="moveToCategory" class="col-md-3 btn-dark">';
					foreach($categories as $row)
					{
						echo '<option value="'.$row['0'].'">' . $row[1] . '</option>';
					}
				echo '</select>';
						
				echo 	'<button id="buttonMoveToCategory" class="btn btn-dark col-md-2" data-action="move" data-to="1">Przenieś <i title="Przenieś" class="fa fa-exclamation" aria-hidden="true"></i></button>
						<button class="btn btn-dark col-md-2" data-action="delete">Usuń <i title="Usuń" class="fa fa-trash" aria-hidden="true"></i></button>
						<button class="btn btn-dark col-md-2" data-action="ban">Banuj <i title="Banuj użytkownika" class="fa fa-ban" aria-hidden="true"></i></button>
						
					</div><div class="col-12"></div>';
			}
				echo '<div class="collapse comments" id="comments'.$this->data['id'].'">
								<h5>Komentarze &nbsp;<a class="reloadComments" data-id="'. $this->data['id'] .'" title="Przeładuj"><i class="fa fa-refresh" aria-hidden="true"></i></a></h5>
								<div class="c">
								</div>
								<div class="row addComment">
									'. (!isset($_SESSION['user']) ? '<div class="col-12">Zaloguj się, aby dodawać komentarze</div>' : '
									<div class="col-10">
										<textarea class="bg-dark col-12" data-id="'.$this->data['id'].'" maxlength="500" data-minlength="3" data-error="Wpisz minimum 3 znaki" placeholder="Wiadomość"></textarea>
									</div>
									<div class="col-2">
										<button type="submit" class="btn-dark col-12 addComment" data-id="'.$this->data['id'].'"><i class="fa fa-commenting-o" aria-hidden="true"></i><span class="hidden-sm-down"> Skomentuj</span></button>
									</div>
									<span data-id="'.$this->data['id'].'" data-type="commentLenght" class="col-12">0/500</span>
									').'
				
								</div>
								
							</div>
						</div>
					</div>
				</article>';


			/*******************************/
		}
	}
	
	class postList
	{
		private $list = array();
		public $pages;
		private $itemsOnPage = 10;	 // Memy na stronę
 		private $category = 1;
		private $where = '', $tag = '';
		private $fav = null;


		// $a - czy zaakceptowane, $id - jeśli wybrany post, $tag - o konkretnym tagu, $order - konkretna kolejność, $user - tylko posty konkretnego użytkownika, $fav - ulubione tej osoby
		function __construct($a = 1, $id = null, $tag = '', $order = '', $user = null, $fav = null)
		{

			global $pdo;
			$this->category = $a;

			$id = (int)$id;
			$this->tag = $tag;
			$this->fav = $fav;

			$this->where = ($fav ? "INNER JOIN favorites ON posts.id=favorites.post AND favorites.user='".$this->fav."' " : '').	
			"WHERE ". ($a != null ? 'category=:category ' : 'category IS NOT NULL '). ($id != null ? " AND posts.id=$id " : '') . (strlen($tag) > 0 ? " AND (tags LIKE :tag1 OR tags LIKE :tag2 OR tags LIKE :tag3 OR tags LIKE :tag4)" : '') . ($user ? " AND posts.user=$user " : ''). 'ORDER BY ' . (strlen($order) > 0 ? $order : 'posts.id DESC');


			$query = "SELECT COUNT(1) FROM posts ". $this->where;
			$stmt = $pdo->prepare($query);
			
			$array = array();

			if($this->category != null)
			{
				$array['category'] = $this->category;
			}
			if(strlen($this->tag) > 0)
			{
				$array['tag1'] = $this->tag;
				$array['tag2'] = $this->tag . ',%';
				$array['tag3'] = '%,' .$this->tag . ',%';
				$array['tag4'] = '%,' .$this->tag;
			}
				
			
			$stmt->execute($array);
			$items = $stmt->fetch()['COUNT(1)'];
			
			$this->pages = ceil($items/$this->itemsOnPage);	
		}
				
		public function load($page)
		{
			global $pdo;
			$page = (int)$page;	

			$first = ($page-1) * $this->itemsOnPage;		// Pierwszy element

			$query = "SELECT posts.id, posts.title, posts.text, posts.object, posts.type, posts.date, posts.rate, posts.tags, users.username, (SELECT COUNT(1) FROM comments as c WHERE c.post = posts.id) as commentsCount " .(isset($_SESSION['user']) ? ", (SELECT id FROM favorites as f WHERE f.user='".$_SESSION['user']['id']."' AND f.post = posts.id) as fav " : "") . "FROM posts INNER JOIN users ON posts.user=users.id ". $this->where . " LIMIT $first, $this->itemsOnPage";

			$stmt = $pdo->prepare($query);


			$array = array();

			if($this->category != null)
			{
				$array['category'] = $this->category;
			}
			if(strlen($this->tag) > 0)
			{
				$array['tag1'] = $this->tag;
				$array['tag2'] = $this->tag . ',%';
				$array['tag3'] = '%,' .$this->tag . ',%';
				$array['tag4'] = '%,' .$this->tag;
			}

			$stmt->execute($array);

			foreach ($stmt->fetchAll() as $value)
			{
				$this->list[] = new post($value);	
			}
			
		}
		public function display()
		{
			foreach($this->list as $l)
			{
				$l->display();
			}
		}
		
	}









	

	/***************** USER ********************************/
	
	class user
	{
		public $username, $password, $email, $id, $avatar, $admin;
		private $logged = false;

		
		function __construct()
		{
			$this->isLogged();
		}


		public function login()
		{
			global $pdo;

			$pass = $this->makePassword($this->password);

			$stmt = $pdo->prepare("SELECT id, ban FROM users WHERE username=:username AND password=:password");
			$stmt->execute(array('username' => $this->username, 'password' => $pass));

			$data = $stmt->fetch();
			$id = $data['id'];

			
			if(isset($id))
			{
				if($data['ban'] == 1)
				 return json_encode(array('code' => '0', 'message' => 'Konto jest zablokowane'));

				$_SESSION['uid'] = $id;
				return json_encode(array('code' => '1'));
			}
			return json_encode(array('code' => '0', 'message' => 'Błędne dane logowania'));
		}
		
				
		private function makePassword($pass)
		{
			return md5('MB3\%q9UQw' . $pass);
		}
		
		public function register()
		{
			global $pdo;
			if(isset($this->username) && isset($this->password) && isset($this->email))
			{

				$stmt = $pdo->prepare("SELECT id FROM users WHERE username=:username OR email=:email");
				$stmt->execute(array('username' => $this->username, 'email' => $this->email));
				$id = $stmt->fetch()['id'];

				if(!$id)
				{
					$pass = $this->makePassword($this->password);
					$date = new DateTime();
					$date = $date->format('Y-m-d H:i:s');
					$stmt = $pdo->prepare("INSERT INTO users(`username`, `password`, `email`, `registration_date`) VALUES(:username, :password, :email, :date)");
					$stmt->execute(array('username'=> $this->username, 'password' => $pass, 'email' => $this->email, 'date' => $date));
					return 1;
				}
			}
			return 0;
		}
		
		public function isLogged()
		{
			global $pdo;

			if(isset($_SESSION['uid']))
			{
				$id = $_SESSION['uid'];
				if(!$this->logged)
				{
					$val = $pdo->query("SELECT id, username, email, registration_date, avatar, admin FROM users WHERE id=$id")->fetch();
					if(isset($val['id']))
					{
						$_SESSION['user'] = $val;
						$this->logged = true;
						$this->id = $val['id'];
						$this->email = $val['email'];
						$this->username = $val['username'];
						$this->avatar = $val['avatar'];
						$this->admin = $val['admin'];
						return 1;
					}
					else
					{
						unset($_SESSION['uid']);
						return 0;
					}
				}
				else
				{
					return 1;
				}
			}
			else
			{
				return 0;
			}
			
		}

		public function getStats()
		{
			if($this->logged)
			{
				global $pdo;


				/*
				$query = "
				SELECT COUNT(case when posts.type = 'video' OR posts.type = 'yt' then 1 else null end) as videos,  COUNT(case when posts.type = 'image' then 1 else null end) as images, c.comments, sum(rate) as rate FROM users
				INNER JOIN posts ON posts.user = users.id
				INNER JOIN (SELECT COUNT(id) as comments FROM comments WHERE comments.user = $this->id) as c
				WHERE users.id = $this->id

				";*/
				$val = $pdo->query("SELECT COUNT(case when posts.type = 'video' OR posts.type = 'yt' then 1 else null end) as videos, COUNT(case when posts.type = 'image' then 1 else null end) as images, sum(rate) as rate FROM posts WHERE posts.user = $this->id")->fetch();

				$val['comments'] = $pdo->query("SELECT COUNT(id) as comments FROM comments WHERE comments.user = $this->id")->fetch()['comments'];

				return $val;
			}
		}

		public function changePassword($password, $newpassword)
		{
			global $pdo;
			
			$stmt = $pdo->prepare("SELECT id FROM users WHERE id=:id AND password=:pass");
			$stmt->execute(array('id' => $this->id, 'pass' => $this->makePassword($password)));

			if($stmt->fetch()['id'])
			{
				$stmt = $pdo->prepare("UPDATE users SET password=:pass WHERE id=:id");
				$stmt->execute(array('id' => $this->id, 'pass' => $this->makePassword($newpassword)));
				return true;
			}	

			return false;
		}
		public function changeAvatar($file = 'img/default_avatar.png')
		{
			global $pdo;


			if($this->avatar != 'img/default_avatar.png')
			{
				$realpath = realpath(dirname(getcwd()));
				unlink($realpath . '/' . $this->avatar);
			}


			$stmt = $pdo->prepare("UPDATE users SET avatar=:avatar WHERE id=:id");
			$stmt->execute(array('id' => $this->id, 'avatar' => $file));
			$_SESSION['user']['avatar'] = $file;
		}
		public function sendCode($email)
		{
			global $pdo;
			global $siteDomain;

			$stmt = $pdo->prepare("SELECT id, username FROM users WHERE email=:email");
			$stmt->execute(array('email' => $email));

			$usr = $stmt->fetch();
			if($usr['id'])
			{

				$d = new DateTime();
				$code = substr(md5('Y3YR6HA4' . $d->format('Y-m-d H:i:s')), 0, 20);

				$stmt = $pdo->prepare("UPDATE users SET passwdResetCode=:code WHERE email=:email");
				$stmt->execute(array('code' => $code, 'email' => $email));

				$message = "Witaj, " . $usr['username'] . "<br>Twój kod resetowania hasła to: <b>" . $code . "</b><br><br>Jeśli nie próbowałeś resetować swojego hasła, zignoruj tą wiadomość";

				$headers[] = 'MIME-Version: 1.0';
				$headers[] = 'Content-type: text/html; charset=utf-8';

				
				$headers[] = 'To: '. $usr['username'] . ' <'.$email.'>';
				$headers[] = 'From: '.$siteDomain.'<noreply@'.$siteDomain.'>';

				
				if(mail($email, "Resetowanie hasła w serwisie " . $siteDomain, $message, implode("\r\n", $headers)))
				{
					return true;
				}
				

			}	

			return false;
		}
		public function resetPassword($email, $code)
		{
			global $pdo;
			global $siteDomain;

			$stmt = $pdo->prepare("SELECT id, username FROM users WHERE email=:email AND passwdResetCode=:code");
			$stmt->execute(array('email' => $email, 'code' => $code));

			$usr = $stmt->fetch();
			if($usr['id'])
			{
				$d = new DateTime();

				$newPass = substr(md5('*6vy>J[}q)&tKHU7' . $d->format('YmdHis')), 0, 8);
				$newPassHash = $this->makePassword($newPass);

				$stmt = $pdo->prepare("UPDATE users SET password=:password, passwdResetCode=NULL WHERE email=:email");
				$stmt->execute(array('password' => $newPassHash, 'email' => $email));


				$message = "Witaj, " . $usr['username'] . "<br>Twoje nowe hasło to: <b>" . $newPass . "</b>";

				$headers[] = 'MIME-Version: 1.0';
				$headers[] = 'Content-type: text/html; charset=utf-8';

				
				$headers[] = 'To: '. $usr['username'] . ' <'.$email.'>';
				$headers[] = 'From: '.$siteDomain.'<noreply@'.$siteDomain.'>';

				
				if(mail($email, "Nowe hasło w serwisie " . $siteDomain, $message, implode("\r\n", $headers)))
				{
					return true;
				}

			}

			return false;

		}
		public function getFollowedTags()
		{
			global $pdo;
			$stmt = $pdo->prepare("SELECT * FROM followedtags WHERE user=:user");
			$stmt->execute(array('user' => $this->id));

			$tags = $stmt->fetchAll();
			return $tags;
		}
		public function changeEmail($email)
		{
			global $pdo;
			$stmt = $pdo->prepare("UPDATE users SET email=:email WHERE id=:user");
			$stmt->execute(array('email' => $email,'user' => $this->id));
			return true;
		}
	}

	/* FUNKCJA WYŚWIETLAJĄCA ILOŚĆ UPŁYNIĘTEGO CZASU OD JAKIEŚ DATY */

	function timeDiff($date1)
	{
		$date2 = new DateTime();
		$diff=$date2->diff($date1);
        $value['diff'] = $diff->format("Różnica: %m miesięcy, %d dni, %h godzin, %i minut i %s sekund");

		//return $date2->format('d.m.Y H:i');
		if($diff->format('%m') > 0)
		{
			if($diff->format('%m') == 1)
				return $diff->format('ponad miesiąc temu');
			
			return $diff->format('ponad %m miesiące temu');
		}
		else if ($diff->format('%d') > 0)
		{
			if($diff->format('%d') == 1)
				return $diff->format('dzień temu');

			return $diff->format('%d dni temu');
		}
		else if ($diff->format('%h') > 0)
		{
			if($diff->format('%h') == 1)
				return $diff->format('godzinę temu');
			else if($diff->format('%h') <= 4)
				return $diff->format('%h godziny temu');

			return $diff->format('%h godzin temu');
		}
		else if ($diff->format('%i') > 0)
		{
			if($diff->format('%i') == 1)
				return $diff->format('minutę temu');
			else if($diff->format('%i') <= 4)
				return $diff->format('%i minuty temu');

			return $diff->format('%i minut temu');
		}
		else
		{
			return 'przed chwilą';
		}

	}

	       

	function YTEmbed($link)
	{
		$data = explode("?v=", $link);

		if(count($data) == 2 && $data[1])
			return 'https://youtube.com/embed/'.$data[1];
		
		$data = explode("/", $link);

		return 'https://youtube.com/embed/'.$data[count($data)-1];
	}
	
	
	class listUsers
	{
		public $pages;
		private $where;

		function __construct($where = null)
		{
			global $pdo;

			$this->where = $where;
			$stmt = $pdo->prepare('SELECT COUNT(1) as count FROM users' . ($where ? ' WHERE '.$where.' ' : ''));
			$stmt->execute();
			$this->pages = ceil($stmt->fetch()['count']/50);
		}

		function load($page = 1)
		{
			global $pdo;

			$first = ($page-1) * 50;
			$query = 'SELECT * FROM users ' . ($this->where ? ' WHERE '.$this->where .' ': '') . "ORDER BY id DESC LIMIT $first, 50";
			$stmt = $pdo->prepare($query);


			$stmt->execute();
			return $stmt->fetchAll();
		}

	};


	function AddTolog($user, $description)
	{
		global $pdo;
		$date = new DateTime();
		$date = $date->format('Y-m-d H:i:s');
		$stmt = $pdo->prepare("INSERT INTO logs(`user`, `description`, `date`) VALUES(:user, :description, :date)");
		$stmt->execute(array('user' => $user, 'description' => $description, 'date' => $date));
	}



?>