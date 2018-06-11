<?php
    require_once('../include.php');
    if(isset($_POST['id']) && isset($_POST['mark']) && isset($_POST['type']))
    {
        $itemID = (int)$_POST['id'];
        $mark = $_POST['mark'];
        $type = $_POST['type'];

       switch($type)
       {
            case 'post':
                $table = 'posts';
                break;
            case 'comment':
                $table = 'comments';
                break;
            default:
                 $table = null;
       }

        if(isset($_SESSION['uid']))
            $uid = (int)$_SESSION['uid'];
        else
            $uid = null;
        
        $ip = $_SERVER['REMOTE_ADDR'];
        $query = 'SELECT id FROM rating WHERE (user=:uid OR ip=:ip) AND rated=:rated AND type=:type';

        $stmt = $pdo->prepare($query);
		$stmt->execute(array('uid' => $uid, 'ip' => $ip, 'rated' => $itemID, 'type' => $type));

        if($mark == '+' OR $mark == '-')
        {
            if(!$stmt->fetch()['id'])
            {
                $stmt = $pdo->prepare('INSERT INTO rating(`user`, `rated`, `mark`, `ip`, `type`) VALUES(:user, :rated, :mark, :ip, :type)');
                $stmt->execute(array('user' => $uid, 'rated' => $itemID, 'mark' => $mark, 'ip' => $ip, 'type' => $type));

                $queryU = 'UPDATE '.$table.' SET rate = rate'. ($mark == '+' ? '+' : '-') . '1 WHERE id=:id';
                $stmt = $pdo->prepare($queryU);
                $stmt->execute(array('id' => $itemID));

                $stmt = $pdo->prepare('SELECT rate FROM '.$table.' WHERE id=:id');
                $stmt->execute(array('id' => $itemID));

                $response['rate'] = $stmt->fetch()['rate'];
                $response['text'] = 'Dziękujemy za oddanie głosu';
            }
            else
            {
                $response['text'] = 'Już głosowałeś';
            }
        }
    
        exit(json_encode($response));
    }

?>