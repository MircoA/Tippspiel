<?php
    
  include 'admin/connect.php';

  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    session_start();

    $id = $_POST['user_id'];
    $password = $_POST['pwd'];
    $page = $_POST['page'];

    $hostname = $_SERVER['HTTP_HOST'];
    $path = dirname($_SERVER['PHP_SELF']);

    $pw = mysqli_query($con, "SELECT password FROM user WHERE id = $id");
    $_pw = mysqli_fetch_array( $pw, MYSQL_ASSOC);

    // Benutzername und Passwort werden überprüft
    if (md5($password) == $_pw[password]) {
      $_SESSION['angemeldet'] = true;
      $_SESSION['id'] = $id;

     // Weiterleitung zur geschützten Startseite
      if ($_SERVER['SERVER_PROTOCOL'] == 'HTTP/1.1') {
        if (php_sapi_name() == 'cgi') {
          header('Status: 303 See Other');
        } else {
          header('HTTP/1.1 303 See Other');
        }
      }

      header('Location: http://'.$hostname.($path == '/' ? '' : $path). '/' . $page);
      exit;
    } else {
      die('Falsches Passwort');
    }
  }
?>