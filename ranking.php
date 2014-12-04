<?php
    session_start();

    $hostname = $_SERVER['HTTP_HOST'];
    $path = dirname($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html>

  <head>
    <title>LSIII Tippspiel - Tipp-Tabelle</title>
    <script src="sorttable.js"></script>
    <link rel="stylesheet" href="style.css" />
    <style type="text/css">
      table.sortable tbody {
          counter-reset: sortabletablescope;
      }
    </style>
    <link rel="stylesheet" href="styled_table.css" />
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
        echo '<input type="hidden" name="page" value="ranking.php">';
        echo '</form>';

       } else {

          $info = mysqli_query($con, "SELECT name, surname FROM user WHERE id = $_SESSION[id]");
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
        <li class="nav"><a class="nav_current" href = "ranking.php">Tipp-Tabelle</a></li>
        <?php if (isset($_SESSION['angemeldet']) || $_SESSION['angemeldet']) { echo '<li class="nav"><a class="nav" href = "usercontrol.php">Kontoverwaltung</a></li>'; } ?>
        <span style="float:right;"><li class="nav"><a class="nav" href="admin/index.php"><b>Administration</b></a></li></span>
      </ul>
    </nav>

<section class="content"><section class="full"><article class="news">
<?php

// include 'admin/connect.php';

$start = $_GET['start'];
$end = $_GET['end'];
$season = $_GET['season'];

if (is_null($start) ) {
	$start = 1;
}
if (is_null($end) ) {
	$end = 34; //Startwert sollte letzter berechneter Spieltag sein
}
if (is_null($season) ) {
	$season = 1; //Startwert sollte aktuellste Saison werden
}

?>

<form action="refresh_ranking.php" method="post">
Tipp-Tabelle f&uuml;r 
<select name="season" onchange="this.form.submit()">
<?php

$seasons = mysqli_query( $con,"SELECT * from season ORDER BY status ASC");

if (! $seasons) {
  die('Ungültige Abfrage: ' . mysqli_error());
}

while ($_seasons = mysqli_fetch_array( $seasons, MYSQL_ASSOC))
{
	if ($_seasons['id'] == $season) {
		echo "<option value='" . $_seasons['id'] . "' selected>" .  $_seasons['name'] . "</option>";
	} else {
		echo "<option value='" . $_seasons['id'] . "'>" .  $_seasons['name'] . "</option>";	
	}
	
}

?>
</select>
mit Spieltagen
von <input type="number" min=1 max=34 name="start" value="<?php echo $start;?>"> <!--TODO: Maximum sollte Spieltagszahl der Saison sein -->
bis <input type="number" min=1 max=34 name="end" value="<?php echo $end;?>"> <!--TODO: Maximum sollte Spieltagszahl der Saison sein -->
anzeigen.
<input type="submit" value="Aktualisieren">
</form>

<?php

// $user_points = mysqli_query($con, "SELECT user.name, user.surname, user.id, SUM(prediction.points >= 5) as big_points, SUM(prediction.points) as points, SUM(prediction.tendency) as tendency, SUM(prediction.exact) as exact, SUM(prediction.difference) as difference, SUM(prediction.goal_number) as goal_number FROM prediction LEFT JOIN game ON prediction.game_id = game.id LEFT JOIN matchday ON matchday.id = game.matchday_id JOIN user ON prediction.user_id = user.id WHERE matchday.season_id = $season GROUP BY user.id ORDER BY points DESC");
$user_points = mysqli_query($con, "SELECT user.name, user.surname, user.id, SUM(prediction.points >= 5) as big_points, SUM(prediction.points) as points, SUM(prediction.tendency) as tendency, SUM(prediction.exact) as exact, SUM(prediction.difference) as difference, SUM(prediction.goal_number) as goal_number FROM prediction LEFT JOIN game ON prediction.game_id = game.id LEFT JOIN matchday ON matchday.id = game.matchday_id JOIN user ON prediction.user_id = user.id WHERE matchday.season_id = $season AND matchday.matchday_number >= $start AND matchday.matchday_number <= $end GROUP BY user.id ORDER BY points DESC");

echo '<br><table class="sortable">';
echo '<th class="sorttable_nosort"><b>Platz</b></th>';
echo '<th class="sorttable_nosort"><b>Name</b></th>';
echo '<th><b>Punkte</b></th>';
echo '<th><b>Tendenz</b></th>';
echo '<th><b>Differenz</b></th>';
echo '<th><b>Torzahl</b></th>';
echo '<th><b>Exakt</b></th>';
echo '<th><b>Lucky Strike*</b></th>';
echo '<th><b>Klassisch*</b></th>';

$count = 1;
while ($_user_points = mysqli_fetch_array($user_points, MYSQL_ASSOC)) {
  
  if ($_user_points[id] == $_SESSION[id]) {
    echo "<tr style='color: red;'>";
  } else {
    echo "<tr>";
  }
  echo "<td>" . $count . "</td>";
  echo "<td>" . $_user_points[name] . ' ' . $_user_points[surname] . "</td>";
  echo '<td align="right">' . number_format((float)$_user_points[points], 6, '.', '') . "</td>";
  echo '<td align="right">' . $_user_points[tendency] . '</td>';
  echo '<td align="right">' . $_user_points[difference] . '</td>';
  echo '<td align="right">' . $_user_points[goal_number] . '</td>';
  echo '<td align="right">' . $_user_points[exact] . '</td>';
  echo '<td align="right">' . $_user_points[big_points] . '</td>';
  echo '<td align="right">' . ($_user_points[tendency] + $_user_points[difference] + $_user_points[exact]) . '</td>';
  echo "</tr>";
  $count += 1;

}
echo "</table>";

echo "<br>*<i>Lucky Strike</i>: Tipps die mehr als 5 Punkte gegeben haben.";
echo "<br>*<i>Klassisch</i>: Punkte bei einem klassischen 1-2-3 Tippsystem.";

echo "<br><br>";

?>
</article></section></section>
    <footer>
      <a href="mailto:mirco.altenbernd@mathematik.tu-dortmund.de">Kontakt</a>
    </footer>
</body>
</html>