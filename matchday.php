<?php
    session_start();

    $hostname = $_SERVER['HTTP_HOST'];
    $path = dirname($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html>
	<head>
		<title>LSIII Tippspiel - Spieltags&uuml;bersicht und Tippabgabe</title>
  	<link rel="stylesheet" href="style.css" />
  	<link rel="stylesheet" href="styled_table.css" />
  	<script src="sorttable.js"></script>
    <style type="text/css">
      table.sortable tbody {
        counter-reset: sortabletablescope;
      }
      table, td, th {
      	font-size: 13px;
      }
      form {
  			display: inline;
			}
    </style>
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
		    echo '<input type="hidden" name="page" value="matchday.php">';
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
					<li class="nav"><a class="nav_current" href = "matchday.php">Spieltags&uuml;bersicht</a></li>
					<?php if (isset($_SESSION['angemeldet']) || $_SESSION['angemeldet']) { echo '<li class="nav"><a class="nav" href = "prediction.php">Tippabgabe</a></li>'; } ?>
					<li class="nav"><a class="nav" href = "ranking.php">Tipp-Tabelle</a></li>
					<?php if (isset($_SESSION['angemeldet']) || $_SESSION['angemeldet']) { echo '<li class="nav"><a class="nav" href = "usercontrol.php">Kontoverwaltung</a></li>'; } ?>
					<span style="float:right;"><li class="nav"><a class="nav" href="admin/index.php"><b>Administration</b></a></li></span>
	      </ul>
	    </nav>

<section class="content"><section class="full"><article class="news">

<?php
$matchday_id = $_GET['matchday_id'];
if (is_null($matchday_id) || $matchday_id == '') {
	$matchday_id = -1; //Startwert sollte letzter berechneter Spieltag sein
}

$season_id = $_GET['season_id'];
if (is_null($season_id) || $season_id == '') {
	$season_id = -1; //Startwert sollte letzter berechneter Spieltag sein
}

//Zeitzone zum vergleichen waehlen
date_default_timezone_set('Europe/Berlin');

//Datumsformat
setlocale (LC_ALL, 'de_de');

$seasons = mysqli_query($con, "SELECT * FROM season");

echo '<form action="refresh_matchday.php" method="post">';
echo 'Spieltags&uuml;bersicht f&uuml;r ';

echo '<select name="season_id" onchange="this.form.submit()">';
while ($_seasons = mysqli_fetch_array( $seasons, MYSQL_ASSOC))
{
	if ($matchday_id != -1) {
		$tmp = mysqli_query($con, "SELECT season_id FROM matchday WHERE id = $matchday_id");
		$_tmp = mysqli_fetch_array($tmp, MYSQL_ASSOC);
		$season_id = $_tmp['season_id'];
	} else if ($season_id == -1) {
		$season_id = $_seasons[id];
	}

	echo "<option value='" . $_seasons['id'];
	if ($_seasons['id'] == $season_id) {
		echo "' selected >";
	} else {
		echo "'>";  
	}

	echo $_seasons[name];

	echo "</option>";
}
echo '</select><noscript><input type="submit" value="Liga aktualisieren"></noscript></form> ';
echo '<form action="refresh_matchday.php" method="post">';
//$matchdays = mysqli_query($con,"SELECT matchday.id as id, matchday.matchday_number as matchday_number, matchday.season_id as season_id, matchday.status as status, matchday.name as matchday_name FROM matchday WHERE matchday.season_id = $season_id ORDER BY status DESC, season_id, matchday_number DESC");
$matchdays = mysqli_query($con,"SELECT matchday.id as id, matchday.matchday_number as matchday_number, matchday.season_id as season_id, matchday.status as status, matchday.name as matchday_name FROM matchday WHERE matchday.season_id = $season_id ORDER BY matchday_number DESC");
$num = 1;
$last = mysqli_num_rows($matchdays);

echo '<select name="matchday_id" onchange="this.form.submit()">';
while ($_matchdays = mysqli_fetch_array( $matchdays, MYSQL_ASSOC))
{

	if ($matchday_id == -1 && $_matchdays[status] != 0) {
		$matchday_id = $_matchdays[id];
	} else if ($num == $last && $matchday_id == -1) {
		$matchday_id = $_matchdays[id];
	}

	echo "<option value='" . $_matchdays['id'];
	if ($_matchdays['id'] == $matchday_id) {
		echo "' selected >";
	} else {
		echo "'>";  
	}
	echo $_matchdays['matchday_name'];
	if ($_matchdays[status] == 1) {
		echo ' (beendet)';
	} else if ($_matchdays[status] == 2) {
		echo ' (berechnet)';
	} else if ($_matchdays[status] == 3) {
			echo ' (aktuell)';
	}
	echo "</option>";
	$num++;
}

?>
</select>
<noscript><input type="submit" value="Liga aktualisieren"></noscript>:
</form><br>
<?php

$users = mysqli_query($con, "SELECT name, surname, id, b.sum AS points FROM user INNER JOIN (SELECT user_id, SUM(points) AS sum FROM prediction, 
	game WHERE prediction.game_id = game.id AND game.matchday_id = $matchday_id  
	GROUP BY user_id) b ON user.id = b.user_id ORDER BY points DESC, id");

echo '<table class="sortable"><thead>';

echo '<tr>';

//Generiere Spielbezeichnung
$games = mysqli_query($con, "SELECT game.*, h.alias as home, h.name as home_name, a.alias as away, a.name as away_name FROM game INNER JOIN team h ON h.id = game.home_team_id INNER JOIN team a ON a.id = game.away_team_id WHERE game.matchday_id = $matchday_id ORDER BY timestamp");

echo '<th class="sorttable_nosort" style="vertical-align:bottom"><b>Platz</b></th>';
echo '<th class="sorttable_nosort" style="vertical-align:bottom"><b>Name</b></th>';
while ($_games = mysqli_fetch_array($games, MYSQL_ASSOC)) {

	echo '<th class="';

	if ($_games[status] != 2) {
		echo 'sorttable_nosort ';
	}

	echo 'rotate" title="'. $_games[home_name] . " - " . $_games[away_name] .'"><div><span class="line">';

	echo $_games[home] . ' - ' . $_games[away] . '';

	if ($_games[home_score] != '') {
		echo ' <span style="font-weight: normal;">' . $_games[home_score] . ':' . $_games[away_score] . '</span>';
	}

	echo '</div></span></th>';
}

echo '<th style="vertical-align:bottom"><b>Punkte</b></th></tr></thead>';

$pos = 1;

echo "<tbody>";

//Trage Nutzer, Tipps und Punkte ein
while ($_users = mysqli_fetch_array($users, MYSQL_ASSOC)) {
	
	if ($_users[id]==$_SESSION['id']) {
		echo '<tr style="color: red;"><td>' . $pos . '</td>';
	} else {
		echo '<tr><td>' . $pos . '</td>';
	}

	echo '<td class="right_line">' . $_users[name] . ' ' . $_users[surname] . '</td>';

	$games = mysqli_query($con, "SELECT id, status, timestamp FROM game WHERE matchday_id = $matchday_id ORDER BY timestamp");
	while ($_games = mysqli_fetch_array($games, MYSQL_ASSOC)) {

		echo '<td width="50px" style="background-image:url(img/diagonal.png); background-size: 100% 100%"';

		if ($_games[status] == 2) {
			$result = mysqli_query($con, "SELECT prediction.home_prediction as home, prediction.away_prediction as away, prediction.points as points FROM prediction, user, game WHERE prediction.game_id = game.id AND prediction.user_id = user.id AND game.id = $_games[id] AND user.id = $_users[id]");

			while ($_result = mysqli_fetch_array($result, MYSQL_ASSOC)) {
				echo ' sorttable_customkey="' . $_result[points] .'">';
				echo  $_result[home] . ':' . $_result[away] . ' <div style="text-align:right"> ' . round($_result[points],2) . '</div>';
				$sum = $sum + $_result[points];
			}
		} else if (strtotime($_games[timestamp]) + 300 < time()) {

			echo '>';

			$result = mysqli_query($con, "SELECT prediction.home_prediction as home, prediction.away_prediction as away, prediction.points as points FROM prediction, user, game WHERE prediction.game_id = game.id AND prediction.user_id = user.id AND game.id = $_games[id] AND user.id = $_users[id]");

			while ($_result = mysqli_fetch_array($result, MYSQL_ASSOC)) {
				echo  $_result[home] . ':' . $_result[away] . ' <div  style="text-align:right">-</div>';
			}
		} else if ($_users[id] == $_SESSION['id']) {

			echo '>';

			$result = mysqli_query($con, "SELECT prediction.home_prediction as home, prediction.away_prediction as away, prediction.points as points FROM prediction, user, game WHERE prediction.game_id = game.id AND prediction.user_id = user.id AND game.id = $_games[id] AND user.id = $_users[id]");

			while ($_result = mysqli_fetch_array($result, MYSQL_ASSOC)) {
				echo  $_result[home] . ':' . $_result[away] . ' <div  style="text-align:right">-</div>';
			}
		} else {

			echo '>';

			$result = mysqli_query($con, "SELECT prediction.points as points FROM prediction, user, game WHERE prediction.game_id = game.id AND prediction.user_id = user.id AND game.id = $_games[id] AND user.id = $_users[id]");

			while ($_result = mysqli_fetch_array($result, MYSQL_ASSOC)) {
				echo  '-:-' . ' <div  style="text-align:right">-</div>';
			}
		}

		echo '</td>';
	}
	echo '<td align="right">' . number_format((float)$_users[points], 6, '.', '') . '</td></tr>';
	$pos += 1;
}
echo "</tbody>";

echo '<tfoot><tr>';

//Generiere Spielbezeichnung
$games = mysqli_query($con, "SELECT game.*, h.alias as home, h.name as home_name, a.alias as away, a.name as away_name FROM game INNER JOIN team h ON h.id = game.home_team_id INNER JOIN team a ON a.id = game.away_team_id WHERE game.matchday_id = $matchday_id ORDER BY timestamp");

echo '<th style="vertical-align:top"><b>Platz</b></th>';
echo '<th style="vertical-align:top"><b>Name</b></th>';
while ($_games = mysqli_fetch_array($games, MYSQL_ASSOC)) {

	echo '<th class="';

	echo 'sorttable_nosort ';

	echo 'rotate_bot" title="'. $_games[home_name] . " - " . $_games[away_name] .'"><div><span class="line"><b>';

	echo $_games[home] . ' - ' . $_games[away] . '';

	if ($_games[home_score] != '') {
		echo ' <span style="font-weight: normal;">' . $_games[home_score] . ':' . $_games[away_score] . '</span>';
	}
	echo '</div></span></th>';
}

echo '<th style="vertical-align:top"><b>Punkte<b></th></tr></tfoot>';

echo '</table><br><br>';

?>
</article></section></section>

    <footer>
      <a href="mailto:mirco.altenbernd@mathematik.tu-dortmund.de">Kontakt</a>
    </footer>
	</body>

</html>