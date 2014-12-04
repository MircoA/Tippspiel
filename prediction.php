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
    <title>LSIII Tippspiel - Tippabgabe</title>
    <link rel="stylesheet" href="style.css" />
    <style type="text/css">
      table, th, td {
         border: 1px solid black;
      }
      td, th {
        padding: 0.3rem;
      }
      form {
        display: inline;
      }
    </style>
    <script type="text/javascript" src="jquery-1.6.4.js"></script>
    <script type="text/javascript">
      var state = false;

      $(document).ready(function() {

          $('#submit_all').click(function() {
              go();

              // get all forms on the page
              $forms = $('form');
              sent = 0;

              // post the form (non-async)
              $forms.each(function() {
                  if(state) {
                  $.ajax({
                      type: "post",
                      async: false,
                      url: $(this).attr("action"), 
                      data: $(this).serialize(), 
                      success: function(data) { 
                          if(++sent == $forms.length) {
                              alert(unescape("Alle Tipps best%E4tigt"));
                          }
                      }
                  });
                  } else { return false; }
              });
          });

          function go() {
              if(!state) {
                  state = true;
                  $('input[type=button], input[type=submit]').attr("disabled", "disabled");
          }}

      });
      </script>
      <script type="text/javascript">
        function SetButtonStatusOn(target)
        {
            document.getElementById(target).removeAttribute("disabled");
            $('input[type=button]').removeAttr("disabled");
        }

        function SetButtonStatusOff(target)
        {
            document.getElementById(target).setAttribute("disabled", "disabled");
        }
    </script>
  </head>

  <body>

  <iframe name="formDestination" style="display:none"></iframe> <!--Versteckts iframe zur Uebergabe der Ergebnisparameter -->

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
        <li class="nav"><a class="nav" href = "index.php">Startseite</a></li>
        <li class="nav"><a class="nav" href = "matchday.php">Spieltags&uuml;bersicht</a></li>
        <?php if (isset($_SESSION['angemeldet']) || $_SESSION['angemeldet']) { echo '<li class="nav"><a class="nav_current" href = "prediction.php">Tippabgabe</a></li>'; } ?>
        <li class="nav"><a class="nav" href = "ranking.php">Tipp-Tabelle</a></li>
        <?php if (isset($_SESSION['angemeldet']) || $_SESSION['angemeldet']) { echo '<li class="nav"><a class="nav" href = "usercontrol.php">Kontoverwaltung</a></li>'; } ?>
        <span style="float:right;"><li class="nav"><a class="nav" href="admin/index.php"><b>Administration</b></a></li></span>
      </ul>
    </nav>

<section class="content"><section class="full"><article class="news">
    <?php

    //Zeitzone zum vergleichen waehlen
    date_default_timezone_set('Europe/Berlin');

    //Datumsformat
    setlocale (LC_ALL, 'de_de');

    $user_id = $_SESSION[id];

    $matchday_id = $_POST['matchday_id'];
    if (is_null($matchday_id) ) {
      $matchday_id = -1;
    }
    $season_id = $_POST['season_id'];
    if (is_null($season_id) ) {
      $season_id = -1;
    }

    $seasons = mysqli_query($con, "SELECT * FROM season WHERE EXISTS (SELECT * FROM user_in_season WHERE user_in_season.user_id = $_SESSION[id] AND user_in_season.season_id = season.id)");

if (mysqli_num_rows($seasons) > 0) {

    echo '<form action="prediction.php" method="post">Tippabgabe f&uuml;r ';

    echo '<select name="season_id" onchange="this.form.submit()">';

    if ($season_id == -1 && $matchday_id != -1) {
      $tmp = mysqli_query($con, "SELECT season_id FROM matchday WHERE id = $matchday_id");
      $_tmp = mysqli_fetch_array($tmp, MYSQL_ASSOC);
      $season_id = $_tmp[season_id];
    }

    while ($_seasons = mysqli_fetch_array($seasons, MYSQL_ASSOC))
    {

      if ($season_id == -1 || $season_id == $_seasons[id]) {
        $season_id = $_seasons['id'];
        echo "<option selected value='" . $_seasons['id'] . "'>" .  $_seasons[name];
      } else {
        echo "<option value='" . $_seasons['id'] . "'>" .  $_seasons[name];
      }
      echo "</option>";
    }

    echo '</select><noscript><input type="submit" value="Liga aktualisieren"></noscript></form>';

    $matchdays = mysqli_query( $con,"SELECT id, matchday_number, season_id, name FROM matchday WHERE (matchday.status = 0 OR matchday.status = 3) AND season_id = $season_id ORDER BY matchday_number");
    if (! $matchdays) {
      die('Ungültige Abfrage: ' . mysqli_error());
    }

    echo ' <form action="prediction.php" method="post"><select name="matchday_id" onchange="this.form.submit()">';
    while ($_matchdays = mysqli_fetch_array($matchdays, MYSQL_ASSOC))
    {
      if ($_matchdays[id] == $matchday_id || $matchday_id == -1) {
        $matchday_id = $_matchdays[id];
        echo "<option selected value='" . $_matchdays['id'] . "'>" .  $_matchdays[name];
      } else {
        echo "<option value='" . $_matchdays['id'] . "'>" .  $_matchdays[name];
      }
      echo "</option>";
    }
    ?>
  </select>
  <noscript><input type="submit" value="Spieltag aktualisieren"></noscript>:
</form><br>
<?php
$games = mysqli_query($con, "SELECT * FROM game WHERE matchday_id = $matchday_id ORDER BY timestamp ASC");

echo '<br><table border="1">';
echo "<th><b>Datum</b></th>";
echo "<th><b>Heimmanschaft</b></th>";
echo "<th><b>Gastmannschaft</b></th>";
echo "<th><b>Ansto&szlig;</b></th>";
echo "<th><b>Ergebnis</b></th>";
echo "<th><b>Tipp</b></th>";

$num = 0;

while ($_games = mysqli_fetch_array( $games, MYSQL_ASSOC))
{
  echo "<tr>";

  $home_name = mysqli_query($con, "SELECT name FROM team WHERE id = $_games[home_team_id]");
  $_home_name = mysqli_fetch_row($home_name);

  $away_name = mysqli_query($con, "SELECT name FROM team WHERE id = $_games[away_team_id]");
  $_away_name = mysqli_fetch_row($away_name);

  $tipp = mysqli_query($con, "SELECT * FROM prediction WHERE game_id = $_games[id] AND user_id = $user_id");
  $_tipp = mysqli_fetch_row($tipp);

  echo "<td align='right'>" . strftime("%A, %d.%m.%y", strtotime(date($_games[timestamp]))) . "</td>";

  echo "<td>". $_home_name[0] . "</td>";
  echo "<td>". $_away_name[0] . "</td>";

  echo "<td align='center'>" . strftime("%H:%M", strtotime(date($_games[timestamp]))) . "</td>";

  if( !is_null($_games['home_score']) && !is_null($_games['away_score']) ) {
  	echo "<td align='center'>". $_games['home_score'] . ":" . $_games['away_score'] . "</td>";
  } else {
  	echo "<td> &nbsp; </td>";
  }

  echo '<td><form target="formDestination" action="editprediction.php" method="post" id="form' . $num . '" onsubmit=' . '"javascript:SetButtonStatusOff(' . "'absenden" . $num . "'" . ')"' . '><input type="number" min="0" max="20"';
  if ($_games[status] != 0 || strtotime($_games[timestamp]) < time()) {
  	echo ' readonly';
  }
  echo ' required name="home_tipp" onchange="SetButtonStatusOn(' . "'absenden" . $num . "'" . ')"';
  echo ' value="' . $_tipp[1] . '">';


  echo ':<input type="number" min="0" max="20"';
  if ($_games[status] != 0 || strtotime($_games[timestamp]) < time()) {
  	echo ' readonly';
  }

  echo ' required name="away_tipp" onchange="SetButtonStatusOn(' . "'absenden" . $num . "'" . ')"';
  echo ' value="' . $_tipp[2] . '">';

  if ($_games[status] == 0 && strtotime($_games[timestamp]) > time()) { // Wieder ==0 setzen
  	echo '<input type="hidden" name="user_id" value="' . $user_id . '">' . '<input type="hidden" name="game_id" value="' . $_games[id] . '">' . '<input type="hidden" name="matchday_id" value="' . $matchday_id . '">' .'<input type="submit" id="absenden'. $num . '" disabled="disabled" value="best&auml;tigen"></form>'; //hidden noch user_id und match_id dann prediction aufrufen und erstellen oder uebermitteln
  } else {
  	echo '</form>';
  }

  echo "</tr>";
  $num +=1;
}
?>
<tr><td></td><td></td><td></td><td></td><td></td><td align="right"><input type="button" disabled="disabled" id="submit_all" value="Alle best&auml;tigen" /></td></tr>
</table>
<br><u>Hinweis:</u> Das Best&auml;tigen eines Tipps ist erst nach einer Eingabe bzw. &Auml;nderung m&ouml;glich.<br><br>
<?php 
} else {
  echo 'Sie sind bisher f&uuml;r kein Tippspiel angemeldet. Bitte treten sie in der Kontoverwaltung einem Tippspiel bei.';
}
?>
</article></section></section>
    <footer>
      <a href="mailto:mirco.altenbernd@mathematik.tu-dortmund.de">Kontakt</a>
    </footer>

  </body>

</html>