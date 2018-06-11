<?php
    require_once('../include.php');

    if($u->isLogged() && isset($_POST['email']))
    {

        if($u->changeEmail($_POST['email']))
        {
            exit(json_encode(array('code' => '1', 'message' => 'Zapisano')));
        }

        exit(json_encode(array('code' => '0', 'message' => 'Wystąpił błąd')));
        
    }

    exit(json_encode(array('code' => '0', 'message' => 'Nie otrzymano wymaganych danych')));
?>