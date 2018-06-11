<?php
    require_once('../include.php');






    if($u->isLogged())
    {

        if(isset($_POST['remove']))
        {
            $u->changeAvatar();
            exit(json_encode(array('code' => '1', 'message' =>  $_SESSION['user']['avatar'])));     
        }


        if(isset($_FILES['avatar']))
        {


            $date = new DateTime();
            $realpath = realpath(dirname(getcwd()));
            $target_dir = $realpath . "/uploads/avatar" . $date->format('dmYHis') . "/";
            @mkdir($target_dir, 0777, true);
            $target_file = $target_dir . basename($_FILES["avatar"]["name"]);
            $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            if($fileType == 'png' || $fileType == 'jpg' || $fileType == 'gif')
            {
                $size = $_FILES["avatar"]["size"];
                if($size > 5000000)
                {
                    exit(json_encode(array('code' => '0', 'message' => 'Zbyt duży rozmiar pliku')));   
                }   
                
                if(move_uploaded_file($_FILES["avatar"]["tmp_name"], $target_file))
                {
                    $fileName = 'uploads/avatar' .  $date->format('dmYHis') . '/' . basename($_FILES["avatar"]["name"]);
                    $u->changeAvatar($fileName);
                    exit(json_encode(array('code' => '1', 'message' => $fileName))); 
                }
                else
                {
                    exit(json_encode(array('code' => '0', 'message' => 'Wystąpił błąd')));   
                }
            }
            else
            {
                exit(json_encode(array('code' => '0', 'message' => 'Zły format pliku')));
            }

        }
        exit(json_encode(array('code' => '0', 'message' => 'Nie otrzymano wymaganych danych')));
    }

    
?>