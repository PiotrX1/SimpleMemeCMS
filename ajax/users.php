<?php
    require_once('../include.php');


    if($u->isLogged() && $u->admin > 0)
    {
        if(isset($_POST['id']) && isset($_POST['action']))
        {
            $action = $_POST['action'];
           
            $stmt = $pdo->prepare("SELECT id, username, admin, ban FROM users WHERE id=:id");
            $stmt->execute(array('id' => $_POST['id']));

            $user = $stmt->fetch();

            if($action == 'ban')
            {
                if($user['admin'] == 0 || $u->admin > 1)
                {
                    $stmt = $pdo->prepare("UPDATE users SET ban=:ban WHERE id=:id");
                    $stmt->execute(array('id' => $user['id'], 'ban' => ($user['ban'] == 1 ? 0 : 1)));

                
                    if($user['ban'])
                    {
                        AddTolog($u->id, "ODBANOWANO '".$user['username']."' (".$user['id'].")");
                        exit(json_encode(array('code' => '1', 'message' => 'Odbanowano użytkownka')));
                    }
                    else
                    {
                        AddTolog($u->id, "ZBANOWANO '".$user['username']."' (".$user['id'].")");
                        exit(json_encode(array('code' => '1', 'message' => 'Zbanowano użytkownka')));
                    }
                }
                exit(json_encode(array('code' => '0', 'message' => 'Nie można zbanować administratora')));
            }
            else if($action == 'admin' && $u->admin > 1)
            {

                $stmt = $pdo->prepare("UPDATE users SET admin=:admin WHERE id=:id");
                $stmt->execute(array('id' => $user['id'], 'admin' => ($user['admin'] == 1 ? 0 : 1)));

                if($user['admin'])
                {
                    AddTolog($u->id, "ODEBRANO PRAWA ADMINA => '".$user['username']."' (".$user['id'].")");
                    exit(json_encode(array('code' => '1', 'message' => 'Odebrano uprawniania')));
                }
                else
                {
                    AddTolog($u->id, "PRZYZNANO PRAWA ADMINA => '".$user['username']."' (".$user['id'].")");
                    exit(json_encode(array('code' => '1', 'message' => 'Przyznano uprawnienia')));
                }
            }



        }
  
    }

    
?>