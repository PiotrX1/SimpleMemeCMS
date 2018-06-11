<?php
    require_once('../include.php');

    if($u->isLogged() && isset($_POST['password']) && isset($_POST['newpassword']) && isset($_POST['newpassword2']))
    {

        if($_POST['newpassword'] == $_POST['newpassword2'])
        {
            $password = $_POST['password'];
            $newpassword = $_POST['newpassword'];

            if($u->changePassword($password, $newpassword))
            {
                exit(json_encode(array('code' => '1', 'message' => 'Hasło zmienione')));
            }
            exit(json_encode(array('code' => '0', 'message' => 'Podałeś nie prawidłowe hasło')));
        }
        


        exit(json_encode(array('code' => '0', 'message' => 'Hasła się nie zgadzają')));
        
    }

    exit(json_encode(array('code' => '0', 'message' => 'Nie otrzymano wymaganych danych')));
?>