<!doctype html>
<html>
	<head>
		<title><?php echo $title ?></title>
		<meta charset="UTF-8">
		<base href="<?php echo $address ?>">
		<script src="js/jquery-3.1.1.min.js"></script>
		<script src="js/jquery.form.js"></script>
		<link rel="stylesheet" href="css/bootstrap.min.css">
		<link rel="stylesheet" href="css/font-awesome.min.css">
		<script src="js/bootstrap.min.js"></script>
		<script src="js/tether.js"></script>
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<link rel="stylesheet" href="css/style.css">
		<script src="js/scripts.js"></script>
		<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
		<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
		<meta name="Description" content="<?php echo $description ?>">
		<link rel="shortcut icon" type="image/png" href="img/favicon.png"/>
		<link rel="icon" type="image/png" href="img/favicon.png"/>
		<meta property="og:url"           content="<?php echo $address ?>" />
		<meta property="og:type"          content="website" />
		<meta property="og:title"         content="<?php echo $title ?>" />
		<meta property="og:description"   content="<?php echo $description ?>" />
		<meta property="og:image"         content="<?php echo $address ?>/img/logo.png" />



	</head>
	<body class="bg-dark">
		<script>
		(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
		(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
		m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		})(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

		ga('create', '<?php echo $googleAnalytics; ?>', 'auto');
		ga('send', 'pageview');

		</script>
		<noscript>
		Włącz obsługę Javascript
		</noscript>
		<header>
			<!-- GÓRNE MENU NAWIGACYJNE ---------------->
			<nav class="navbar navbar-toggleable-md navbar-inverse bg-darkest topnav col-12">
				<button class="navbar-toggler navbar-toggler-right clearfix" type="button" data-toggle="collapse" data-target="#navigation">
					<span class="navbar-toggler-icon"></span>
				</button>
				<div class="container">
					<a class="navbar-brand" href="<?php echo $address; ?>">
						<img src="img/logo.png" class="d-inline-block align-top" alt="">
						<?php echo $siteDomain; ?>
					</a>
					<div class="collapse navbar-collapse" id="navigation">
						
						<ul class="navbar-nav">
						<?php 
						
						$stmt = $pdo->prepare("SELECT * FROM categories");
						$stmt->execute();
						$categories = $stmt->fetchAll();

						foreach ($categories as $row)
						{
							echo '
							<li class="nav-item '. ((isset($menu) && $menu==$row['id']) ? 'active' : '' ).'">
							<a class="nav-link" href="'.$address.'/c/'.$row['name'].'">'.$row['name'].'</a>
							</li>';
						}
						?>

							<li class="nav-item <?php if($menu==0) echo 'active'; ?>">
								<a class="nav-link" href="<?php echo $address; ?>/c/oczekujace">Oczekujące</a>
							</li>
							<li class="nav-item <?php if($menu==-1) echo 'active'; ?>">
								<a class="nav-link" href="<?php echo $address; ?>/top/">TOP</a>
							</li>


						</ul>
					</div>
				</div>
			</nav>
			<!-- GÓRNE MENU NAWIGACYJNE - KONIEC ---------------->
		</header>	
		<main>
			<div id="info" class="col-md-12">
				<div class="container"></div>
			</div>
			<div class="container">
				
			<!-- LOGOWANIE I REJESTRACJA -------------------------------->
				<div class="col-md-4 float-right">

				<!-- PANEL UŻYTKOWNIKA ---------------------------------->
					<?php if($u->isLogged()):?>

					<?php
						$stats = $u->getStats();
					?>
					<div class="right-panel">
						<div class="tab-content padding-0">	
							<div class="tab-pane active padding-0" role="tabpanel" id="userpanel">
								<div class="card card-block post border-top-0">
									<header>
										<h5 class="card-title float-left">Panel użytkownika</h5>
										<h5 class="card-title float-right"><a title="Wyloguj" class="float-right" href="wyloguj"><i class="fa fa-sign-out" aria-hidden="true"></i></a></h5>
									</header>
									<div>
										<div class="row">
											<div class="col-4">
												<img id="miniavatar" src="<?php echo $_SESSION['user']['avatar']; ?>">
											</div>
											<div class="col-8">
												<div class="row">
													<div class="col-12 padding-0">
														<i class="fa fa-user-o" aria-hidden="true"></i><strong><?php echo $_SESSION['user']['username']; ?></strong>
													</div>
													<ul class="col-6">
														<li><i class="fa fa-picture-o" aria-hidden="true"></i><?php echo $stats['images']; ?></li>
														<li><i class="fa fa-film" aria-hidden="true"></i><?php echo $stats['videos']; ?></li>
													</ul>
													<ul class="col-6">
														<li><i class="fa fa-star" aria-hidden="true"></i><?php echo ($stats['rate'] == NULL ? '0' : $stats['rate']) ?></li>
														<li><i class="fa fa-comments" aria-hidden="true"></i><?php echo ($stats['comments'] == NULL ? '0' : $stats['comments']) ?></li>
													</ul>
												</div>
											</div>
										</div>
										<div class="row">
											<a class="btn btn-grey col-6" href="ustawienia">Ustawienia konta</a>
											<a class="btn btn-grey col-6" href="moje">Twoje memy</a>
											<a class="btn btn-grey col-6" href="ulubione">Ulubione</a>
											<a id="showFollowedTags" class="btn btn-grey col-6">Obserwowane tagi</a>
											<div id="listFollowedTags" class="col-12" style="display:none;">
												<?php
												$t = $u->getFollowedTags();
												if(count($t) > 0)
												{
													foreach($t as $row) 
													{
														echo '<a href="tag/'.$row['tag'].'">#'.$row['tag'].'</a>&nbsp;';
													}
												}
												else
												{
													echo 'Jeszcze nie obserwujesz żadnych tagów';
												}
												?>
											</div>
											<?php if($u->admin > 0):?>
												<h5 class="col-12 text-center text-danger">Admin</h5>
												<a class="btn btn-grey col-6" href="uzytkownicy">Lista użytkowników</a>
												<a class="btn btn-grey col-6" href="zbanowani">Lista zbanowanych</a>
												<a class="btn btn-grey col-6" href="kategorie">Kategorie</a>
											<?php endif ?>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>


					<!-- PANEL UŻYTKOWNIKA - KONIEC ---------------------------------->

					<?php else: ?>

					<div id="loginregister">
						<ul class="nav nav-tabs" role="tablist">
							<li class="nav-item col-sm-6">
								<a class="nav-link active" data-toggle="tab" href="#login" role="tab">Logowanie</a>
							</li>
							<li class="nav-item col-sm-6">
								<a class="nav-link" data-toggle="tab" href="#register" role="tab">Rejestracja</a>
							</li>
						</ul>

					<!-- LOGOWANIE -------------------------------->

						<div class="tab-content padding-0">	
							<div class="tab-pane active padding-0" role="tabpanel" id="login">
								<div class="card card-block bg-darkest border-top-0">
									<div>
										<h5 class="card-title">Logowanie</h5>
										<form id="loginForm" method="post">
											<div class="col-12">
												<input type="text" class="col-12 bg-dark" id="username" name="username" placeholder="Nazwa użytkownika" required>
											</div>
											<div class="col-12">
												<input type="password" class="col-12 bg-dark" id="password" name="password" placeholder="Hasło" required>
											</div>
											<div class="col-12">
												<button type="submit" id="loginButton" name="login" value="1" class="col-12 btn-dark">Zaloguj</button>
											</div>
											<div class="col-12">
												<a class="mini" id="showResetForm">Resetuj hasło</a>
											</div>
										</form>
										<form id="resetPasswordForm" method="post" style="display: none;">
											<div class="col-12 margin-padding-0">
												<input type="email" class="col-12 bg-dark" name="email" placeholder="Adres email" required>
											</div>
											<div class="col-12 margin-padding-0">
												<input id="resetCode" type="text" class="col-12 bg-dark" name="code" placeholder="Kod resetowania hasła" style="display: none;" required disabled>
											</div>
											<div class="row col-12 margin-padding-0">
												<button type="submit" id="resetPasswordButton" class="col-6 btn-dark">Wyślij kod</button>
												<button type="button" id="cancelResetPasswordButton" class="col-6 btn-dark">Anuluj</button>
											</div>
										</form>
									</div>
								</div>
							</div>

					<!-- LOGOWANIE - KONIEC --------------------------->
					<!-- REJESTRACJA -------------------------------->
							
							<div class="tab-pane padding-0" role="tabpanel" id="register">
								<div class="card card-block bg-darkest border-top-0">
									<div>
										<h5 class="card-title">Rejestracja</h5>
										<form id="registerForm" method="post">
											<div class="col-12">
												<input type="text" class="col-12 bg-dark" placeholder="Nazwa użytkownika" id="registerUsername" name="username" minlength="5" required>
											</div>
											<div class="col-12">
												<input type="password" class="col-12 bg-dark" id="registerPassword1" name="password1" minlength="6" placeholder="Hasło" required>
											</div>
											<div class="col-12">
												<input type="password" class="col-12 bg-dark" id="registerPassword2" name="password2" placeholder="Potwierdź hasło" required>
											</div>
											<div class="col-12">
												<input type="email" class="col-12 bg-dark" id="registerEmail" name="email" placeholder="Adres email" required>
											</div>
											<div class="col-12">
												<button type="submit" id="registerButton" name="register" value="1" class="col-12 btn-red">Utwórz konto</button>
											</div>
											
										</form>
									</div>
								</div>
							</div>
							
						</div>
					</div>
					<?php endif ?>

					<!-- REJESTRACJA - KONIEC ---------------------------------->


					<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
					<script>
					(adsbygoogle = window.adsbygoogle || []).push({
						google_ad_client: "<?php echo $googleAd; ?>",
						enable_page_level_ads: true
					});
					</script>


					<!-- PANEL Z REKLAMAMI --------------------------------------

					

					<div class="right-panel">
						<div class="tab-content padding-0">	
							<div class="tab-pane active padding-0" role="tabpanel" id="userpanel">
								<div class="card card-block post border-top-0">
									<header>
										<h5 class="card-title float-left">Reklamy</h5>
									</header>
									<div id="a1">
										
										<a href="">
											<img src="https://via.placeholder.com/350x150">
										</a>
										<a href="">
											<img src="https://via.placeholder.com/350x150">
										</a>
										<a href="">
											<img src="https://via.placeholder.com/350x150">
										</a>

										
									</div>
								</div>
							</div>
						</div>
					</div>

					 PANEL Z REKLAMAMI - KONIEC ------------------------------------>


				</div>
			<!---- LOGOWANIE I REJESTACJA - KONIEC -------------------------------------->
			
			<?php if($u->isLogged()):?>

			<!--------------- DODAWANIE MEMÓW ------------------>

				<div class="col-md-8 float-left">
					<div class="card card-block post border-top-0">
						<header id="toggleAddPost">
							<h5 class="card-title float-left">Dodaj</h5>
							<a class="float-right">
								<i class="fa fa-plus-circle" aria-hidden="true"></i>
							</a>
						</header>

						<div style="display: none;">
							<form id="addPostForm" method="post" enctype="multipart/form-data">
								<div id="addPost">
									<div class="row col-12 margin-padding-0">
										<div class="col-12">
											<input class="bg-dark col-12" name="title" type="text" placeholder="Tytuł" required>
										</div>
									</div>
									<div class="row col-12 margin-padding-0">
										<div class="col-12">
											<textarea id="postText" name="text" class="bg-dark col-12" maxlength="10000" placeholder="Opis"></textarea>
											<span id="textLength" class="col-12">0/10000</span>
										</div>
									</div>
									<div class="row col-12 margin-padding-0">
										<div id="tdiv" class="col-10">
											<input id="addTags" class="bg-dark col-12" type="text" placeholder="Dodaj tagi (max 5)">
											<input id="addTags2" name="tags" hidden>
										</div>	
										<div id="fdiv" class="col-2" style="padding-left: 3px !important;">
											<input id="file" name="file" type="file" accept="image/x-png, image/gif, image/jpeg, video/mp4">
											<label id="filelabel" for="file" class="btn-blue2 col-12 text-center file">
												<i class="fa fa-file-image-o" aria-hidden="true"></i>
											</label>
										</div>	
									</div>
									<div class="row col-12 margin-padding-0">
										<div id="tagsLabels" class="col-12"></div>
									</div>
									<div class="row col-12 margin-padding-0">
										<div class="col-12">
											<a id="addFromYT">Dodaj film z youtube zamiast pliku</a>
											<input class="col-12 bg-dark" style="display: none;" id="yt" name="yt" type="url" placeholder="https://www.youtube.com/embed/xxxxx">
										</div>	
									</div>
									
									<div class="row col-12 margin-padding-0">
										<div class="col-12">
											<button type="submit" id="addPostButton" class="btn-red col-md-2 col-12">Dodaj <span><i class="fa fa-spinner fa-spin" aria-hidden="true"></i></span></button>
										</div>
									</div>
								</div>
							</form>
						</div>


					</div>
				</div>
				<!------------------ DODAWANIE MEMÓW - KONIEC ---------------------------------->

				<?php endif ?>

				<!------------------ USTAWIENIA UŻYTKOWNIKA ------------------------------------>



				<?php if(isset($otherPage) && $otherPage == 'settings'):?>

				<div class="col-md-8 float-left">
					<div class="card card-block post">
						<header>
							<h4 class="card-title float-left">Ustawienia</h4>
						</header>
						<div class="row col-12 margin-padding-0">
							<div class="col-md-4">
								<form id="updateAvatarForm" method="post">
									<img id="actavatar" class="col-12 margin-padding-0" src="<?php echo $_SESSION['user']['avatar']; ?>" alt="">
									<input type="file" id="avatar" name="avatar" accept="image/x-png, image/gif, image/jpeg">
									<div class="row col-12 margin-padding-0" style="margin-top: 5px !important;">
										<label title="Wybierz obraz" for="avatar" class="btn-blue2 col-10 text-center file margin-padding-0">Zdjęcie <i id="av" class="fa fa-file-image-o" aria-hidden="true"></i> <i class="fa fa-spinner fa-spin" aria-hidden="true" style="display: none;"></i></label>
										<button type="button" title="Usuń awatar" class="btn-green col-2 text-center margin-padding-0" id="removeAvatar"><i class="fa fa-trash" aria-hidden="true"></i></button>
									</div>
								</form>
							</div>
							<div class="col-md-8">
								<div class="col-12 margin-padding-0">
									<form id="changeEmailForm" method="POST">
										<div class="col-12">
											<h5>Zmiana emaila</h5>
										</div>
										<div class="col-12">
											<input type="email" class="col-12 bg-dark" placeholder="Adres email" name="email" value="<?php echo $u->email; ?>" required>
										</div>
										<div class="col-12">
											<button type="submit" class="btn-red col-md-3 col-12">Aktualizuj</button>
										</div>
									</form>
								</div>
								<div class="col-12 margin-padding-0">
									<form id="changePasswordForm" method="POST">
										<div class="col-12">
											<h5>Zmiana hasła</h5>
										</div>
										<div class="col-12">
											<input type="password" class="col-12 bg-dark" placeholder="Aktualne hasło" name="password" required>
										</div>
										<div class="col-12">
											<input type="password" class="col-12 bg-dark" placeholder="Nowe hasło" id="newpassword" name="newpassword" required>
										</div>
										<div class="col-12">
											<input type="password" class="col-12 bg-dark" placeholder="Powtórz nowe hasło" name="newpassword2" required>
										</div>
										<div class="col-12">
											<button type="submit" class="btn-red col-md-3 col-12">Aktualizuj</button>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
				</div>


				<?php endif ?>

			<!------------------ USTAWIENIA UŻYTKOWNIKA - KONIEC ------------------------------------>

			<!------------------ FORMULARZ KONTAKTOWY  ---------------------------------------------->

				<?php if(isset($otherPage) && $otherPage == 'contact'):?>


				<div class="col-md-8 float-left">
					<div class="card card-block post">
						<header>
							<h4 class="card-title float-left">Kontakt</h4>
						</header>
						<div class="row col-12 margin-padding-0">
							<div class="col-md-4">
								<p>
									Jeśli chcesz zgłosić błąd, masz sugestię lub jakieś pytania, możesz skorzystać z tego formularza.<br><br>
									Odpowiedź otrzymasz na podany adres email.
						
								</p>
							</div>
							<div class="col-md-8">
								<form id="contactForm" method="post">
									<div class="col-12">
										<input type="text" name="name" class="col-12 bg-dark" placeholder="Imię" required>
									</div>
									<div class="col-12">
										<input type="email" name="email" class="col-12 bg-dark" placeholder="Adres email" required>
									</div>
									<div class="col-12">
										<input type="text" name="topic" class="col-12 bg-dark" placeholder="Temat" required>
									</div>
									<div class="col-12">
										<textarea style="height: 150px;" class="col-12 bg-dark" name="content" placeholder="Treść wiadomości" required></textarea>
									</div>
									<div class="col-12">
										<button type="submit" class="btn-red col-md-3 col-12">Wyślij</button>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>



				<?php endif ?>


			<!------------------ FORMULARZ KONTAKTOWY - KONIEC ------------------------------------>

			<!------------------ ZASADY KORZYSTANIA Z SERWISU  ---------------------------------------------->

				<?php if(isset($otherPage) && $otherPage == 'terms'):?>

				<div class="col-md-8 float-left">
					<div class="card card-block post">
						<header>
							<h4 class="card-title float-left">Zasady korzystania z serwisu</h4>
						</header>
						<div class="row col-12 margin-padding-0">
							<div class="col-md-12">
								<p id="tou"><?php echo file_get_contents("terms_of_use.txt"); ?></p>
							</div>
						</div>
					</div>
				</div>



				<?php endif ?>

			<!------------------ ZASADY KORZYSTANIA Z SERWISU - KONIEC  -------------------------->



			
			<!-- LISTA UŻYTKOWNIKÓW  -------------------------------->

			<?php if($u->isLogged() && $u->admin && isset($users)):?>
			

			<div class="col-md-8 float-left">
				<div class="card card-block post">
					<header>
						<h4 class="card-title float-left">Lista użytkowników</h4>
					</header>
					<div class="col-12 margin-padding-0 table-responsive">
						<table class="table table-striped">
							<thead class="bg-green">
								<tr>
								<th>#</th>
								<th>Nazwa</th>
								<th>Email</th>
								<th>Data rejestracji</th>
								<th>Awatar</th>
								<th>Admin</th>
								<th>Ban</th>
								</tr>
							</thead>
							<tbody>
							<?php
							
								foreach($users as $row)
								{
									echo '<tr>
										<th scope="row">'.$row['id'].'</th>
										<td><a href="u/'.$row['username'].'">'.$row['username'].'<a/></td>
										<td>'.$row['email'].'</td>
										<td>'.$row['registration_date'].'</td>
										<td><a href="'.$row['avatar'].'">Link</a></td>									
										<td>'.($row['admin'] == 2 ? '<strong class="text-danger">TAK</strong>' : '<a class="usersControl" data-id="'.$row['id'].'" data-action="admin" title="Zmień">'.($row['admin'] == 1 ? '<strong class="text-success">TAK</strong>' : 'NIE').'</a>').'</td>
										<td>'.($row['admin'] == 2 ? '--' : '<a class="usersControl" data-id="'.$row['id'].'" data-action="ban" title="Zmień">'.($row['ban'] == 1 ? 'TAK' : 'NIE').'</a>').'</td>
									</tr>';
								}
							?>
							</tbody>
						</table>
					</div>
				</div>
			</div>


			<?php endif ?>

				
			<!-- LISTA UŻYTKOWNIKÓW - KONIEC -------------------------------->	

			<!-- LISTA KATEGORII -------------------------------->						

			<?php if($u->isLogged() && $u->admin && isset($_GET['categories'])):?>


			<div class="col-md-8 float-left">
				<div class="card card-block post">
					<header>
						<h4 class="card-title float-left">Kategorie</h4>
					</header>
					<div class="col-12 margin-padding-0 table-responsive">
						<table class="table table-striped">
							<thead class="bg-green">
								<tr>
								<th>#</th>
								<th>Nazwa</th>
								<th></th>
								</tr>
							</thead>
							<tbody>

							<?php
							
								foreach($categories as $row)
								{
									echo '<tr><td>' . $row[0] . '</td><td>' . $row[1] . '</td><td><a href="admin.php?categories&delete='.$row[0].'">Usuń</a></td></tr>';
								}
							?>
							</tbody>
						</table>
					</div>
					<form class="col-md-6" action="admin.php?categories" method="post">
						<div class="form-group">
							<label>Nazwa</label>
							<input name="name" ty[e="text" class="form-control">
						</div>
						<button name="add" type="submit" class="btn btn-primary">Dodaj</button>
					</form>
					<br>
				</div>

			</div>
					



			<?php endif ?>

			<!-- LISTA KATEGORII - KONIEC -------------------------------->						
			<!--------- POSTY ------------------>


				<?php if(isset($tag)):?>

				<div class="col-md-8 float-left">
					
						<div class="row col-12 margin-padding-0">
							<div class="col-12">
								<h5 class="float-left">Wyświetlany tag: <span class="text-muted">#<?php echo $tag ?></span></h5>
								<?php if($u->isLogged()):?>
								<h5 class="float-right"><a id="followTag" data-tag="<?php echo $tag ?>"><i class="fa <?php echo ($followed ? 'fa-star' : 'fa-star-o') ?>" aria-hidden="true"></i></a></h5>
								<?php endif ?>
							</div>
						</div>
					
				</div>
					
				

				<?php endif ?>



				<?php if(isset($_GET['favorites'])):?>

				<div class="col-md-8 float-left">
					
						<div class="row col-12 margin-padding-0">
							<div class="col-12">
								<h5 class="float-left">Przeglądasz ulubione</h5>
							</div>
						</div>	
				</div>
					
				<?php endif ?>


				<?php if(isset($userP)):?>

				<div class="col-md-8 float-left">
					
						<div class="row col-12 margin-padding-0">
							<div class="col-12">
								<h5 class="float-left">Przeglądasz posty użytkownika <span class="text-muted"><?php echo $userP; ?></span></h5>
							</div>
						</div>	
				</div>
					
				<?php endif ?>

				

				
				<?php if(isset($_GET['own'])):?>

				<div class="col-md-8 float-left">
					
						<div class="row col-12 margin-padding-0">
							<div class="col-12">
								<h5 class="float-left">Przeglądasz własne memy</h5>
							</div>
						</div>	
				</div>
					
				<?php endif ?>


				<?php
					// Wyświetla posty
					if(isset($list))
						$list->display();
				?>

			<!---------------------------------->	




				<!-------- STRONICOWANIE -------------->

				<div class="col-md-8 float-left">	
					<nav>
						<ul class="pagination justify-content-center">
							<?php
							if((!isset($_GET['id']) AND isset($list)) || isset($users))
							{
								if($page > 1)
									echo '<li class="page-item"><a class="page-link" href="'. "/$pageType/".($page-1).'">Poprzednia</a></li>';
								for ($i = ($page-4 > 0 ? $page-4 : 1); $i <= ($page+4 <= $pages ? $page+4 : $pages); $i++)
									echo '<li class="page-item '.($i == $page ? 'active' : '').'"><a class="page-link" href="' . $address ."/$pageType/".$i.'">'.$i.'</a></li>';
								if($page < $pages)
									echo '<li class="page-item"><a class="page-link" href="'."/$pageType/".($page+1).'">Następna</a></li>';

							}
							?>
						</ul>
					</nav>
					
				</div>

				<!-------------- KONIEC STRONICOWANIA ------------>


				
			</div>	
		</main>	
		<footer class="bg-darkest clearfix row col-12">
			<div class="container">
				<div class="row">
					<div class="col-sm-4">
						<h5>Kontakt</h5>
						<ul>
							<li>
								<a href="mailto:<?php echo $contactMail; ?>"><?php echo $contactMail; ?></a>
							</li>
							<li>
								<a href="kontakt">formularz kontaktowy</a>
							</li>
						</ul>
					</div>
					<div class="col-sm-4">
						<h5>Regulamin</h5>
						<ul>
							<li>
								<a href="regulamin">zasady korzystania z serwisu</a>
							</li>
						</ul>
					</div>
					<div class="col-sm-4">
						<h5>&nbsp;</h5>	
						<ul>
							<li>
								2017-2018
							</li>
							<li>
								<a href="https://github.com/PiotrX1/SimpleMemeCMS">SimpleMemeCMS</a>
							</li>
						</ul>
					</div>
					<div class="col-sm-12 text-center">
						Strona korzysta z plików cookies. Odwiedzając ją wyrażasz zgodę na ich użycie.
					</div>
				</div>
			</div>
		</footer>
	</body>

</html>