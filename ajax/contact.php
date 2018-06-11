<?php
    require_once('../include.php');

    if(isset($_POST['name']) && isset($_POST['email']) && isset($_POST['topic']) && isset($_POST['content']))
    {
        $name = htmlspecialchars($_POST['name']);
        $email = htmlspecialchars($_POST['email']);
        $topic = htmlspecialchars($_POST['topic']);
        $content = htmlspecialchars($_POST['content']);

        $headers[] = 'MIME-Version: 1.0';
        $headers[] = 'Content-type: text/html; charset=utf-8';

        
        $headers[] = 'To: '. $contactMail . ' <'.$contactMail.'>';
        $headers[] = 'From: '.$name.' <'.$email.'>';
        
        if(mail($contactMail, $topic, $content, implode("\r\n", $headers)))
        {
            exit(json_encode(array('code' => '1', 'message' => 'Dziękujemy za wiadomość')));
        }
        exit(json_encode(array('code' => '0', 'message' => 'Nie udało się wysłać wiadomości')));
    }

    exit(json_encode(array('code' => '0', 'message' => 'Nie podano wystarczającej ilości danych')));
?>