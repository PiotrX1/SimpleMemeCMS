<?php
    require_once('../include.php');


    if($u->isLogged())
    {
        if(isset($_POST['id']))
        {
            $stmt = $pdo->prepare("SELECT * FROM favorites WHERE user=:user AND post=:post");
            $stmt->execute(array('user' => $u->id, 'post' => $_POST['id']));

            $idf = $stmt->fetch()['id'];
            if($idf)
                $status = true;
            else
                $status = false;

            if(isset($_POST['set']))
            {
                if($status)
                {
                    $stmt = $pdo->prepare("DELETE FROM favorites WHERE id=:id");
                    $stmt->execute(array('id' => $idf));
                    exit(json_encode(array('status' => false)));
                }
                else
                {
                    $stmt = $pdo->prepare("INSERT INTO favorites VALUES(NULL, :user, :post)");
                    $stmt->execute(array('user' => $u->id, 'post' => $_POST['id']));
                    exit(json_encode(array('status' => true)));
                }
            }

            exit(json_encode(array('status' => $status)));
        }
  
        exit(json_encode(array('code' => '0', 'message' => 'Nie otrzymano wymaganych danych')));
    }

    
?>