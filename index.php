<?php
    session_start();

    $hostname = $_SERVER['HTTP_HOST'];
    $path = dirname($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html>

	<head>
		<title>LSIII Tippspiel - Startseite</title>
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
  	<script language="JavaScript">
		  
		  /*
		  	Author:		Robert Hashemian (http://www.hashemian.com/)
		  	Modified by:	Munsifali Rashid (http://www.munit.co.uk/)
		  	Modified by:	Tilesh Khatri
		  */
		  
		  function StartCountDown(myDiv,myTargetDate)
		  {
		    var dthen	= new Date(myTargetDate);
		    var dnow	= new Date();
		    ddiff		= new Date(dthen-dnow);
		    gsecs		= Math.floor(ddiff.valueOf()/1000);
		    CountBack(myDiv,gsecs);
		  }
		  
		  function Calcage(secs, num1, num2)
		  {
		    s = ((Math.floor(secs/num1))%num2).toString();
		    if (s.length < 2) 
		    {	
		      s = "0" + s;
		    }
		    return (s);
		  }
		  
		  function CountBack(myDiv, secs)
		  {
		    var DisplayStr;
		    var DisplayFormat = "%%D%% Tage, %%H%% Stunden, %%M%% Minuten, %%S%% Sekunden";
		    DisplayStr = DisplayFormat.replace(/%%D%%/g,	Calcage(secs,86400,100000));
		    DisplayStr = DisplayStr.replace(/%%H%%/g,		Calcage(secs,3600,24));
		    DisplayStr = DisplayStr.replace(/%%M%%/g,		Calcage(secs,60,60));
		    DisplayStr = DisplayStr.replace(/%%S%%/g,		Calcage(secs,1,60));
		    if(secs > 0)
		    {	
		      document.getElementById(myDiv).innerHTML = DisplayStr;
		      setTimeout("CountBack('" + myDiv + "'," + (secs-1) + ");", 990);
		    }
		    else
		    {
		      document.getElementById(myDiv).innerHTML = "Spiel hat begonnen.";
		    }
		  }	

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
				<li class="nav"><a class="nav_current" href = "index.php">Startseite</a></li>
				<li class="nav"><a class="nav" href = "matchday.php">Spieltags&uuml;bersicht</a></li>
				<?php if (isset($_SESSION['angemeldet']) || $_SESSION['angemeldet']) { echo '<li class="nav"><a class="nav" href = "prediction.php">Tippabgabe</a></li>'; } ?>
				<li class="nav"><a class="nav" href = "ranking.php">Tipp-Tabelle</a></li>
				<?php if (isset($_SESSION['angemeldet']) || $_SESSION['angemeldet']) { echo '<li class="nav"><a class="nav" href = "usercontrol.php">Kontoverwaltung</a></li>'; } ?>
				<span style="float:right;"><li class="nav"><a class="nav" href="admin/index.php"><b>Administration</b></a></li></span>
			</ul>
		</nav>

		<section class="content">
			<section class="left">
				<article class="news">
					<H3>Automatische Punkteberechnung und DFB-Pokal</H3><span style="float:right">22.9.2014</span>
					<p>
						Spieltagsergebnisse und Punkte werden nun innerhalb von 5 Minuten nach Abpfiff automatisch eingetragen und berechnet.
						<br>Ein Dank daf&uuml;r geht an Dirk.
						<br><br>
						Au&szlig;erdem ist es nun m&ouml;glich den aktuellen DFB-Pokal zu tippen. Daf&uuml;r muss in der Kontoverwaltung unter Tippspielverwaltung einfach nur dem entsprechenden Tippspiel beigetreten werden.
						<br>Der DFB-Pokal soll vorallem ein Test f&uuml;r Turniere, wie die Europameisterschaft, sein. Daher w&auml;re eine rege Teilnahme w&uuml;nschenswert.
					</p>
					<hr>
				</article>
				<article class="news">
					<H3>E-Mail-Benachrichtigung</H3><span style="float:right">18.9.2014</span>
					<p>
						In der Kontoverwaltung kann nun eine E-Mail-Benachrichtigung aktiviert werden. Diese informiert einen t&auml;glich um 10 Uhr, ob innerhalb der n&auml;chsten zwei Tage noch Spiele existieren f&uuml;r die bisher kein Tipp abgegeben wurde.
					</p>
					<hr>
				</article>
				<article class="news">
					<H3>Session-Login und weitere Verbesserungen</H3><span style="float:right">17.9.2014</span>
					<p>
						Bei der Tippabgabe muss nun nicht immer das Passwort eingegeben werden. Stattdessen meldet man sich nun einmal an (oben rechts) und kann dann nach belieben seine Tipps eingeben und &auml;ndern. Au&szlig;erdem sind nun Spieltags&uuml;bersicht und Tippabgabe getrennt.
						<br><br>Zus&auml;tzlich gibt es nach dem Login auf der Startseite eine Anzeige der Spiele innerhalb der n&auml;chsten Woche f&uuml;r die noch kein Tipp abgegeben wurde.
						<br><br>Lesezeichen sollten nun m&ouml;glichst auf die "index.php" statt "index.html" verweisen.
					</p>
					<hr>
				</article>
				<article class="news">
					<H3>Neues Design</H3><span style="float:right">14.9.2014</span>
					<p>
						Nachdem die grundlegenden Funktionen vorhanden sind gibt es nun auch ein hoffentlich ansprechenderes Design. Wie zu erkennen angelehnt an das &uuml;bliche Design der Universit&auml;tsseiten.
					</p>
					<hr>
				</article>
				<br><br>
<!-- 				<article class="news">
					<H3>Lorem ipsum</H3><span style="float:right">14.9.2014</span>
					<p>Dort auf dem Platze banden mitunter die kecksten Knaben ihre Schlitten an die Bauernwagen und fuhren dann eine t&uuml;chtige Strecke mit. Das ging gerade recht lustig. Als das Spiel im vollen Gange war, kam ein gro&szlig;er, wei&szlig; angestrichener Schlitten. Eine Person sa&szlig; in demselben, die in einen wei&szlig;en, rauen Pelz eingeh&uuml;llt und mit einer wei&szlig;en Pelzm&uuml;tze bedeckt war. Der Schlitten fuhr zweimal um den Platz herum und Kay gelang es, seinen kleinen Schlitten an denselben festzubinden und nun fuhr er mit. Rascher und immer rascher ging es gerade in die n&auml;chste Stra&szlig;e hinein. Der F&uuml;hrer des Schlittens wandte den Kopf und nickte ihm so freundlich zu, als ob sie mit einander bekannt w&auml;ren. So oft Kay seinen kleinen Schlitten abbinden wollte, nickte die Person abermals und dann blieb Kay sitzen; sie fuhren gerade zum Stadttore hinaus. Da wurde das Schneegest&ouml;ber so heftig, dass der kleine Knabe nicht die Hand vor den Augen mehr erkennen konnte, w&auml;hrend er gleichwohl weiter fuhr. Endlich lie&szlig; er den Strick fallen, um sich von dem gro&szlig;en Schlitten los zu machen, aber es half nichts, sein kleines Fuhrwerk hing fest und es ging mit Windeseile. Da rief er ganz laut, aber niemand h&ouml;rte ihn, und der Schnee wirbelte und der Schlitten flog vorw&auml;rts. Mitunter gab es einen Sto&szlig;, als ob man &uuml;ber Gr&auml;ben und Hecken f&uuml;hre. Er war ganz entsetzt, wollte sein Vaterunser beten, konnte sich aber nur noch auf das gro&szlig;e Einmaleins besinnen.</p>
				</article> -->
			</section>
			<div style="border-left:2px #D3D3D3 solid; position:absolute; left:71.9%; width:4px; height:100%;"></div>
			<section class="right" >
				<!-- <article class="side">
	  			<H3>Hinweis</H3><br>
	  			Bitte nach Abgabe der Tipps &uuml;berpr&uuml;fen, ob diese angenommen wurden. Dazu einfach die Spieltags&uuml;bersicht f&uuml;r den Spieltag aufrufen. Sollte ein Tipp angenommen worden sein, so steht dieser bei dem entsprechenden Spiel.
				</article> -->
				<article class="side">
					<H3>N&auml;chste Spiele</H3><br>
					<?php
						//Zeitzone zum vergleichen waehlen
						date_default_timezone_set('Europe/Berlin');

						//Datumsformat
						setlocale (LC_ALL, 'de_de');

						$count = 1;

						$season = mysqli_query($con, "SELECT id FROM season WHERE status = 0");

						while ($_season = mysqli_fetch_array($season, MYSQL_ASSOC)) {

							$time = mysqli_query($con, "SELECT timestamp, season.name as name, matchday.name as m_name FROM game LEFT JOIN matchday on game.matchday_id = matchday.id LEFT JOIN season ON matchday.season_id = season.id WHERE game.status = 0 AND matchday.season_id = $_season[id] AND game.timestamp > NOW() ORDER BY timestamp LIMIT 1");
							
							if (mysqli_num_rows($time) > 0) {
								$_time = mysqli_fetch_array($time, MYSQL_ASSOC);
					?>
								<br><u><?php echo $_time[name];?>:</u> <?php echo $_time[m_name]; ?><br>
								<div id="clock<?php echo $count;?>">[clock1]</div>
								<script>
									StartCountDown(<?php echo '"clock' . $count . '"';?>,<?php echo '"' . date('m/d/Y H:i', strtotime($_time[timestamp])) . '"';?>)
  							</script>
  				<?php
  							$count++;
  						}
  					}
  				?>
				</article>
				<?php
					if (isset($_SESSION['angemeldet']) || $_SESSION['angemeldet']) {
						echo '<article class="side">';

						echo 'Hallo ' . $_info[name] . ' ' . $_info[surname] . '.<br>';

						// $notipps = mysqli_query($con, "SELECT matchday.matchday_number as number, h.name as home_name, a.name as away_name FROM game LEFT JOIN team h ON (game.home_team_id = h.id) LEFT JOIN team a ON (game.away_team_id = a.id) LEFT JOIN matchday ON (matchday.id = game.matchday_id) WHERE NOT EXISTS (SELECT id FROM prediction WHERE prediction.game_id = game.id AND prediction.user_id = $_SESSION[id]) AND game.status = 0 ORDER BY timestamp ASC LIMIT 9");
						$notipps = mysqli_query($con, "SELECT matchday.season_id as season_id, matchday.id as number, matchday.name as name, h.name as home_name, a.name as away_name FROM game LEFT JOIN team h ON (game.home_team_id = h.id) LEFT JOIN team a ON (game.away_team_id = a.id) LEFT JOIN matchday ON (matchday.id = game.matchday_id) WHERE NOT EXISTS (SELECT id FROM prediction WHERE prediction.game_id = game.id AND prediction.user_id = $_SESSION[id]) AND game.status = 0 AND DATE(timestamp) < DATE_SUB(DATE(NOW()), Interval -7 day) AND EXISTS (SELECT * FROM user_in_season WHERE user_in_season.user_id = $_SESSION[id] AND user_in_season.season_id = matchday.season_id) ORDER BY timestamp ASC");

						$number = '';

						while ($_notipps = mysqli_fetch_array($notipps, MYSQL_ASSOC)) {

							if ($number == '') {

								$season_name = mysqli_query($con, "SELECT name FROM season WHERE id = $_notipps[season_id]");
								$_season_name = mysqli_fetch_array($season_name, MYSQL_ASSOC);

								echo '<br>Spiele ohne Tipp innerhalb der n&auml;chsten Woche:<br>';
								$number = $_notipps[number];
								echo '<br><u>' . $_notipps['name'] . ' ('. $_season_name[name] .'):</u><br><table class="user">';	
							}
							if ($number != $_notipps[number]) {

								$season_name = mysqli_query($con, "SELECT name FROM season WHERE id = $_notipps[season_id]");
								$_season_name = mysqli_fetch_array($season_name, MYSQL_ASSOC);

								echo '</table><br><u>' . $_notipps['name'] . ' ('. $_season_name[name] .'):</u><br><table class="user">';	
							}
							echo '<tr><td class="user">' . $_notipps[home_name] . '</td><td class="user"> - </td><td class="user">' . $_notipps[away_name] . '</td></tr>';

							$number = $_notipps[number];
						}
					 	echo '</table>';
						if ($number == '') {
							echo '<br>Du hast f&uuml;r alle Spiele innerhalb der n&auml;chsten Woche Tipps abgegeben.';
						}
						echo '</article><br><br>';
					}
				?>
			</section>

		</section>

		<footer>
			<a href="mailto:mirco.altenbernd@mathematik.tu-dortmund.de">Kontakt</a>
		</footer>
	</body>
</html>