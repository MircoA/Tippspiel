<!-- Anmeldund ueberpruefen und eine SESSION beginnen -->
<?php
    
  //Verbindungsaufbau
  include 'admin/connect.php';

  if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    //Session beginnen
    session_start();

    $id = $_POST['user_id']; //User_id
    $password = $_POST['pwd']; //Eingegebenes Passwort
    $page = $_POST['page']; //Seite von der Anmeldung erfolgte

    $hostname = $_SERVER['HTTP_HOST'];
    $path = dirname($_SERVER['PHP_SELF']);

    //Passwort aus Datenbank zum vergleichen
    $pw = mysqli_query($con, "SELECT password FROM user WHERE id = $id");
    $_pw = mysqli_fetch_array( $pw, MYSQL_ASSOC);

    // Benutzername und Passwort werden überprüft
    if (md5($password) == $_pw[password]) {

      //Setzen von Session-Variablen
      $_SESSION['angemeldet'] = true; //ist angemeldet
      $_SESSION['id'] = $id; //User_id

     // Weiterleitung zur geschützten Startseite
      if ($_SERVER['SERVER_PROTOCOL'] == 'HTTP/1.1') {
        if (php_sapi_name() == 'cgi') {
          header('Status: 303 See Other');
        } else {
          header('HTTP/1.1 303 See Other');
        }
      }

      //Weiterleitung
      header('Location: http://'.$hostname.($path == '/' ? '' : $path). '/' . $page);
      exit;
    } else {
      die('Falsches Passwort');
    }
  }
?>