<?php
    require_once('../include.php');

    if(isset($_POST['id']) && isset($_POST['action']))
    {
        $action = $_POST['action'];
        $postID = (int)$_POST['id'];

        /************** WYŚWIETLANIE *****************************/
        if($action == 'show')
        {
            
            $query = "SELECT c.id, c.date, c.text, c.rate, c.ip, u.username, u.avatar FROM comments as c LEFT JOIN users as u ON c.user = u.id WHERE c.post=$postID ORDER BY c.id ASC";

            $comments = array();
            foreach($pdo->query($query) as $value)
            {
                $date = new DateTime($value['date']);
                $value['date'] = $date->format('d.m.Y H:i');
                $value['diff'] = timeDiff($date);
                if($value['username'] == null)
                {
                    $value['username'] = 'anonim (' . $value['ip'] . ')';
                    $value['avatar'] = 'img/default_avatar.png';
                }

                if(isset($_SESSION['user']) && $_SESSION['user']['admin'] > 0)
                {
                     $value['admin'] = true;
                }

                $comments[] = $value;
            }

            exit(json_encode($comments));
        }
        /********************************************************/
        /************** DODAWANIE *******************************/
        else if($action == 'add' && $u->isLogged())
        {
            if(isset($_POST['text']))
            {
                $text = htmlentities($_POST['text']);




                $d = new DateTime();

                $d->modify("-30 seconds"); 
        

                $stmt = $pdo->prepare("SELECT * FROM comments WHERE date > :date AND user=:id");
                $stmt->execute(
                    array(
                        'date' => $d->format('Y-m-d H:i:s'),
                        'id' => $u->id
                    )
                );

                if($u->admin == 0 && $stmt->fetch()['id'])
                {
                    exit(json_encode(array('code' => '0', 'message' => 'Musisz chwilę odczekać przed dodaniem kolejnego komentarza')));
                }




                if(strlen($text) >= 3 && strlen($text) <= 500)
                {
                    /*
                    if(isset($_SESSION['uid']))
                        $uid = (int)$_SESSION['uid'];
                    else
                        $uid = null;
                    */
                    $uid = $u->id;

                    $ip = $_SERVER['REMOTE_ADDR'];

                    $date = new DateTime();
                    $date = $date->format('Y-m-d H:i:s');
                    $query = 'INSERT INTO comments(`post`, `user`, `date`, `text`, `ip`) VALUES(:post, :user, :date, :text, :ip)';
                    $stmt = $pdo->prepare($query);
                    $stmt->execute(array('post' => $postID, 'user' => $uid, 'date' => $date, 'text' => $text, 'ip' => $ip));
                }
            }
        }
        else if($action == 'delete' && $_POST['id'] && $u->isLogged() && $u->admin > 0)
        {
            $stmt = $pdo->prepare("DELETE FROM comments WHERE id=:id");
            $stmt->execute(array('id' => $_POST['id']));
            exit(json_encode(array('code' => '1', 'message' => 'Usunięto komentarz')));

        }
        /********************************************************/
         
    }

?>