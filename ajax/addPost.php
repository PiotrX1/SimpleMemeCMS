<?php
    require_once('../include.php');
    $date = new DateTime();

    function check($id)
    {
        // Funkcja sprawdza czy użytkownik odczekał odpowiedni czas po dodaniu ostatniego posta
        // Zabezpieczenie przed botami i spamem

        global $pdo;
        global $date;

        $d = clone $date;
        $d->modify("-2 minutes"); 
        

        $stmt = $pdo->prepare("SELECT * FROM posts WHERE date > :date AND user=:id");
        $stmt->execute(
            array(
                'date' => $d->format('Y-m-d H:i:s'),
                'id' => $id
            )
        );
        global $u;
        if($stmt->fetch()['id'] && $u->admin == 0)
        {
            return false;
        }
        return true;
    }


    if(isset($_GET['check']))
    {
        if(check($u->id))
            exit('1');
        else
            exit('0');
        
    };



    $fileName = '';

    if(isset($_POST["title"]) && $u->isLogged())
    {
        
        if(!check($u->id))
        {
            exit(json_encode(array('code' => '0', 'message' => 'Musisz chwilę odczekać przed dodaniem kolejnego postu.')));
        }


        if(isset($_FILES["file"]))
        {
            

            $realpath = realpath(dirname(getcwd()));
            $target_dir = $realpath . "/uploads/" . $date->format('dmYHis') . "/";
            @mkdir($target_dir, 0777, true);
            $target_file = $target_dir . basename($_FILES["file"]["name"]);
            $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            if($fileType == 'png' || $fileType == 'jpg' || $fileType == 'gif' || $fileType == 'mp4')
            {
                $size = $_FILES["file"]["size"];
                if($size > 10000000)
                {
                    exit(json_encode(array('code' => '0', 'message' => 'Zbyt duży rozmiar pliku')));   
                }   
                
                if(move_uploaded_file($_FILES["file"]["tmp_name"], $target_file))
                {
                    $fileName = 'uploads/' .  $date->format('dmYHis') . '/' . basename($_FILES["file"]["name"]);

                    if($fileType == 'png' || $fileType == 'jpg')
                    {
                        $stamp = imagecreatefrompng('../img/watermark.png');
                        if($fileType == 'png')
                        {
                            
                            $im = imagecreatefrompng('../'.$fileName);

                        }
                        else
                        {
                            $im = imagecreatefromjpeg('../'.$fileName);
                        }
                        $marge_right = 10;
                        $marge_bottom = 10;
                        $sx = imagesx($stamp);
                        $sy = imagesy($stamp);
                        imagecopy($im, $stamp, imagesx($im) - $sx - $marge_right, imagesy($im) - $sy - $marge_bottom, 0, 0, imagesx($stamp), imagesy($stamp));
                        imagepng($im, '../'.$fileName);
                        imagedestroy($im);
                    }


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
        else if(isset($_POST['yt']))
        {
            $fileName = YTEmbed($_POST['yt']);
            if($_POST['yt'] == '')
            {
                exit(json_encode(array('code' => '0', 'message' => 'Wybierz plik lub podaj link do yt')));
            }
        }


        $stmt = $pdo->prepare('INSERT INTO posts(`user`, `title`, `text`, `object`, `type`, `date`, `tags`) VALUES(:user, :title, :text, :object, :type, :date, :tags)');

                
        $stmt->execute(
            array(
                'user' => $u->id,
                'title' => htmlentities($_POST['title']),
                'text' => htmlentities($_POST['text']),
                'object' => $fileName,
                'type' => (isset($fileType) ? ($fileType == 'mp4' ? 'video' : 'image') : 'yt'),
                'date' => $date->format('Y-m-d H:i:s'),
                'tags' => str_replace(' ', '', htmlentities($_POST['tags']))
            )
        );
        exit(json_encode(array('code' => '1', 'message' => $address . '/post/'.$pdo->lastInsertId())));


    
        
    }

    exit(json_encode(array('code' => '0', 'message' => 'Nie otrzymano wymaganych danych')));
?>