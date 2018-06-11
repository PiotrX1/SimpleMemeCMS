<?php
    require_once('../include.php');


    if($u->isLogged() && $u->admin > 0)
    {
        if(isset($_POST['id']) && isset($_POST['action']))
        {
            $action = $_POST['action'];
            if($action == 'move')
            {
                $category = $_POST['to'];

                $stmt = $pdo->prepare("SELECT title FROM posts WHERE id=:id");
                $stmt->execute(array('id' => $_POST['id']));

                $post = $stmt->fetch();

                $stmt = $pdo->prepare("UPDATE posts SET category=:category WHERE id=:id");
                $stmt->execute(array('category' => $category, 'id' => $_POST['id']));


                AddTolog($u->id, "PRZENIESIONO '".$post['title']."' (".$_POST['id'].") DO KATEGORII O ID" . strtoupper($category));


                exit(json_encode(array('code' => '1', 'message' => 'Przeniesiono')));
            }
            else if($action == 'delete')
            {
                $stmt = $pdo->prepare("SELECT title, object, type FROM posts WHERE id=:id");
                $stmt->execute(array('id' => $_POST['id']));

                $post = $stmt->fetch();

                if($post['type'] == 'image' || $post['type'] == 'video')
                {
                    $realpath = realpath(dirname(getcwd()));
				    unlink($realpath . '/' . $post['object']);
                }

                $stmt = $pdo->prepare("DELETE FROM posts WHERE id=:id");
                $stmt->execute(array('id' => $_POST['id']));


                AddTolog($u->id, "USUNIĘTO '".$post['title']."' (".$_POST['id'].")");

                exit(json_encode(array('code' => '1', 'message' => 'Usunięto')));
            }
            else if($action == 'ban')
            {

                $stmt = $pdo->prepare("SELECT user FROM posts WHERE id=:id");
                $stmt->execute(array('id' => $_POST['id']));

                $user = $stmt->fetch()['user'];

                

                $stmt = $pdo->prepare("SELECT username, admin FROM users WHERE id=:id");
                $stmt->execute(array('id' => $user));

                $usr = $stmt->fetch();
                $name = $usr['username'];


                if($usr['admin'] == 0 || $u->admin > 1)
                {
                    $stmt = $pdo->prepare("UPDATE users SET ban=1 WHERE id=:id");
                    $stmt->execute(array('id' => $user));
                    AddTolog($u->id, "ZBANOWANO '$name' ($user)");

                    exit(json_encode(array('code' => '1', 'message' => 'Zbanowano użytkownka')));
                }
                else
                {
                    exit(json_encode(array('code' => '0', 'message' => 'Nie możesz zbanować admina')));
                }




                
            }



        }
  
        exit(json_encode(array('code' => '0', 'message' => 'Nie otrzymano wymaganych danych')));
    }

    
?>