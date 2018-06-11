<?php
    require_once('../include.php');

    if(isset($_POST['email']))
    {
        if(isset($_POST['code']))
        {
            if($u->resetPassword($_POST['email'], $_POST['code']))
            {
                exit(json_encode(array('code' => '2', 'message' => 'Nowe hasło zostało wysłane na maila')));
            }
            exit(json_encode(array('code' => '0', 'message' => 'Podany kod jest nie prawidłowy')));
        }
        else
        {

            if($u->sendCode($_POST['email']))
            {
                exit(json_encode(array('code' => '1', 'message' => 'Wysłano maila z kodem')));
            }
            exit(json_encode(array('code' => '0', 'message' => 'Nie znaleziono użytkownika o takim adresie email')));  
        }
    }

    exit(json_encode(array('code' => '0', 'message' => 'Nie otrzymano wymaganych danych')));
?>