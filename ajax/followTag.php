<?php
    require_once('../include.php');


    if($u->isLogged())
    {
        if(isset($_POST['tag']))
        {
            $stmt = $pdo->prepare("SELECT * FROM followedtags WHERE user=:user AND tag=:tag");
            $stmt->execute(array('user' => $u->id, 'tag' => $_POST['tag']));

            $idt = $stmt->fetch()['id'];

            if($idt)
                $status = true;
            else
                $status = false;

            if(isset($_POST['set']))
            {
                if($status)
                {
                    $stmt = $pdo->prepare("DELETE FROM followedtags WHERE id=:id");
                    $stmt->execute(array('id' => $idt));
                    exit(json_encode(array('status' => false)));
                }
                else
                {
                    $stmt = $pdo->prepare("INSERT INTO followedtags VALUES(NULL, :user, :tag)");
                    $stmt->execute(array('user' => $u->id, 'tag' => htmlentities($_POST['tag'])));
                    exit(json_encode(array('status' => true)));
                }
            }

            exit(json_encode(array('status' => $status)));
        }
  
        exit(json_encode(array('code' => '0', 'message' => 'Nie otrzymano wymaganych danych')));
    }

    
?>