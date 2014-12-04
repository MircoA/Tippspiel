<?php
    session_start();

    $hostname = $_SERVER['HTTP_HOST'];
    $path = dirname($_SERVER['PHP_SELF']);
    if (!isset($_SESSION['angemeldet']) || !$_SESSION['angemeldet']) {
      header('Location: http://'.$hostname.($path == '/' ? '' : $path). '/index.php');
      exit();
    }
?>
<!DOCTYPE html>
<html>
  <head>
  	<title>LSIII Tippspiel - Kontoverwaltung</title>
    <link rel="stylesheet" href="style.css" />
    <!-- Cookies fuer Nutzerauswahl setzen/lesen/aendern -->
  	<script type="text/javascript">
  		var saveclass = null;

  		function saveUser(cookieValue)
  		{
  		    var sel = document.getElementById('user_select');

  		    saveclass = saveclass ? saveclass : document.body.className;
  		    document.body.className = saveclass + ' ' + sel.value;

  		    setCookie('user', cookieValue, 365);
  		}

  		function setCookie(cookieName, cookieValue, nDays) {
  		    var today = new Date();
  		    var expire = new Date();

  		    if (nDays==null || nDays==0)
  		        nDays=1;

  		    expire.setTime(today.getTime() + 3600000*24*nDays);
  		    document.cookie = cookieName+"="+escape(cookieValue) + ";expires="+expire.toGMTString();
  		}

  		function readCookie(name) {
  		  var nameEQ = name + "=";
  		  var ca = document.cookie.split(';');
  		  for(var i = 0; i < ca.length; i++) {
  		    var c = ca[i];
  		    while (c.charAt(0) == ' ') c = c.substring(1, c.length);
  		    if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
  		  }
  		  return null;
  		}

  		document.addEventListener('DOMContentLoaded', function() {
  		    var themeSelect = document.getElementById('user_select');
  		    var selectedTheme = readCookie('user');

  		    themeSelect.value = selectedTheme;
  		    saveclass = saveclass ? saveclass : document.body.className;
  		    document.body.className = saveclass + ' ' + selectedTheme;
  		});
  	</script>
  </head>

  <body>

    <div style="height: 20px;" align="right"><?php

      include 'admin/connect.php';

       $hostname = $_SERVER['HTTP_HOST'];
       $path = dirname($_SERVER['PHP_SELF']);

       if (!isset($_SESSION['angemeldet']) || !$_SESSION['angemeldet']) {
        echo '<form method="post" action="login.php">';
        echo '<select name="user_id" id="user_select" onchange="saveUser(this.value);">';

        $users = mysqli_query( $con,"SELECT * FROM user ORDER BY name");

        if (! $users) {
          die('Ungültige Abfrage: ' . mysqli_error());
        }

        while ($_users = mysqli_fetch_array( $users, MYSQL_ASSOC))
        {
          echo "<option value='" . $_users['id'] . "'>" .  $_users['name'] . " " . $_users['surname'] . " (" . $_users[username] .  ")</option>";
        }

        echo '</select>';
        echo 'Passwort: <input type="password" name="pwd">';
        echo '<input type="submit" value="anmelden">';
        echo '<input type="hidden" name="page" value="index.php">';
        echo '</form>';

       } else {

          $info = mysqli_query($con, "SELECT name, surname, email, notification FROM user WHERE id = $_SESSION[id]");
          $_info = mysqli_fetch_array($info, MYSQL_ASSOC);

          echo '<font size=+1>' . $_info[name] . ' ' . $_info[surname] . ' ' . '<a href="logout.php">abmelden</a></font>';
       }
    ?></div>
    <header>
      <img class="left" src="img/m_schrift.png" alt="logo-m!" /><span style="margin-left:25%;"><font size=8 color=#85B817><b>Tippspiel</b></font></span><img class="right" src="img/lehrstuhl3.png" alt="logo-ls3" />
    </header>

    <nav>
      <ul class="nav">
        <li class="nav"><a class="nav" href = "index.php">Startseite</a></li>
        <li class="nav"><a class="nav" href = "matchday.php">Spieltags&uuml;bersicht</a></li>
        <?php if (isset($_SESSION['angemeldet']) || $_SESSION['angemeldet']) { echo '<li class="nav"><a class="nav" href = "prediction.php">Tippabgabe</a></li>'; } ?>
        <li class="nav"><a class="nav" href = "ranking.php">Tipp-Tabelle</a></li>
        <?php if (isset($_SESSION['angemeldet']) || $_SESSION['angemeldet']) { echo '<li class="nav"><a class="nav_current" href = "usercontrol.php">Kontoverwaltung</a></li>'; } ?>
        <span style="float:right;"><li class="nav"><a class="nav" href="admin/index.php"><b>Administration</b></a></li></span>
      </ul>
    </nav>

    <section class="content">
      <section class="full">
        <article class="news">
          <h3>E-Mail Benachrichtigung</h3>
          <form action="edit_notification.php" method="post"><br>
            Ich m&ouml;chte eine E-Mail-Benachrichtigung erhalten: <input type="checkbox" name="notification" onchange="this.form.submit()" value="1" <?php if ($_info[notification] == 1) {echo 'checked';}?>><input type="hidden" name ="user_id" value="<?php echo $_SESSION[id]; ?>"><noscript><input required type="submit" value="best&auml;tigen"></noscript><br><br>
            <u>Hinweis</u>: Bei Auswahl erh&auml;lst du jeden Tag um 10 Uhr eine Benachrichtigung, falls innerhalb der n&auml;chsten zwei Tage Spiele vorliegen f&uuml;r die du noch keine Tipps abgegeben hast.
          </form>
          <hr>
        </article>        
        <article class="news">
          <h3>E-Mail Adresse</h3>
          <form action="edit_mail.php" method="post">
            <table class="user">
              <tr><td class="user"> Aktuelle E-Mail: </td><td class="user"><?php echo $_info[email];?></td></tr>
              <tr><td class="user"> Neue E-Mail: </td><td class="user"><input type="mail" name="mail"></td><td class="user"><input type="hidden" name ="user_id" value="<?php echo $_SESSION[id]; ?>"><input required type="submit" value="E-Mail &auml;ndern"></td></tr>
            </table>
          </form>
          <hr>
        </article>
        <article class="news">
          <h3>Passwort</h3>
          <form action="edit_password.php" method="post">
            <table class="user">
            	<tr>
            		<td class="user">
            			Altes Passwort:
            		</td>
            		<td class="user" align="right">
            			<input required type="password" name="old_pwd">
            		</td>
            	</tr>
            	<tr>
            		<td class="user">
            			Neues Passwort:
            		</td>
            		<td class="user" align="right">
            			<input required type="password" name="new_pwd1">
            		</td>
            	</tr>
            	<tr>
            		<td class="user">
            			Neues Passwort wiederholen:
            		</td>
            		<td class="user" align="right">
            			<input required type="password" name="new_pwd2">
            		</td><td class="user"><input type="hidden" name ="user_id" value="<?php echo $_SESSION[id]; ?>"><input required type="submit" value="Passwort &auml;ndern"></td>
            	</tr>
            </table>
          </form>
          <hr>
        </article>
        <article class="news">
          <h3>Tippspielverwaltung</h3>
          <?php
          include 'admin/connect.php';

          $season = mysqli_query($con, "SELECT * FROM user_in_season LEFT JOIN season ON user_in_season.season_id = season.id WHERE $_SESSION[id] = user_in_season.user_id");

          echo '<br><br>Du nimmst an folgenden Tippspielen teil:<table class="user">'; 
          while ($_season = mysqli_fetch_array($season, MYSQL_ASSOC)) {
            echo '<tr><td class="user">' . $_season[name] . '</td></tr>';
          }
          echo '</table>';

          $season = mysqli_query($con, "SELECT * FROM season WHERE NOT EXISTS (SELECT * FROM user_in_season WHERE user_in_season.user_id = $_SESSION[id] AND user_in_season.season_id = season.id) AND season.status = 0");
          
          if (mysqli_num_rows($season) > 0) {
          
            echo '<form method="post" action="join_season.php">An <select name="season_id">';

            while ($_season = mysqli_fetch_array($season, MYSQL_ASSOC)) {
              echo '<option value="'. $_season[id] . '">' . $_season['name'] . '</option>';
            }
            echo '</select><input type="hidden" name="user_id" value="' . $_SESSION[id] . '"> <input type="submit" value="teilnehmen">.</form>';
          }
          ?>
          <hr>
        </article>
      </section>
    </section>

    <footer>
      <a href="mailto:mirco.altenbernd@mathematik.tu-dortmund.de">Kontakt</a>
    </footer>
</body>
</html>