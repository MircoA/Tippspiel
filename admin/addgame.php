<!DOCTYPE html>
<html>
  <head>
    <title>LSIII Tippspiel - Saisonverwaltung</title>
    <link rel="stylesheet" href="../style.css" />
    <link rel="stylesheet" type="text/css" href="../jquery.datetimepicker.css"/ >
    <script src="../jquery.js"></script>
    <script src="../jquery.datetimepicker.js"></script>
    <script type="text/javascript">
      $(document).ready(function() {
        jQuery('#timestamp').datetimepicker({
          format:'Y-m-d H:i',
          inline:false,
          lang:'de',
          defaultDate:new Date(),
          formatTime: 'H:i',
          defaultTime:'15:30',
          allowTimes:['15:30','17:30','18:30','19:00','20:00','20:30'],
          step:30
        });
      });
    </script>
  </head>

  <body>
  
    <header>
      <img class="left" src="../img/m_schrift.png" alt="logo-m!" /><span style="margin-left:25%;"><font size=8 color=#85B817><b>Tippspiel</b></font></span><img class="right" src="../img/lehrstuhl3.png" alt="logo-ls3" />
    </header>

    <nav>
      <ul class="nav">
        <li class="nav"><a class="nav" href = "index.php">Startseite</a></li>
        <li class="nav"><a class="nav" href = "adduser.php">Nutzerverwaltung</a></li>
        <li class="nav"><a class="nav_current" href = "addseason.php">Saisonverwaltung</a></li>
        <li class="nav"><a class="nav" href = "addteam.php">Teamverwaltung</a></li>
        <span style="float:right;"><li class="nav"><a class="nav" href="../index.php"><b>Nutzer</b></a></li></span>
      </ul>
    </nav>
    <section class="content">
      <section class="full">
        <article class="news">

          <?php

          include 'connect.php';

          //Zeitzone zum vergleichen waehlen
          date_default_timezone_set('Europe/Berlin');

          //Datumsformat
          setlocale (LC_ALL, 'de_de');

          $matchday_id = mysqli_real_escape_string($con, $_GET['matchday_id']);
          $matchday_info = mysqli_query($con, "SELECT matchday.matchday_number as matchday_number, matchday.season_id as season_id, season.name as name FROM matchday INNER JOIN season ON season.id = matchday.season_id WHERE matchday.id = $matchday_id");

          $_matchday_info = mysqli_fetch_array($matchday_info);

          echo $_matchday_info[name] . ', Spieltag ' . $_matchday_info[matchday_number];

          $game = mysqli_query($con, "SELECT game.*, h.name as home, a.name as away FROM game INNER JOIN team h ON h.id = game.home_team_id INNER JOIN team a ON a.id = game.away_team_id WHERE game.matchday_id = $matchday_id ORDER BY timestamp");

          echo '<table border="1">';
          echo "<th><b>ID</b></th>";
          echo "<th><b>Datum</b></th>";
          echo "<th><b>Heimmanschaft</b></th>";
          echo "<th><b>Gastmannschaft</b></th>";
          echo "<th><b>Ansto&szlig;</b></th>";
          echo "<th><b>Ergebnis</b></th>";
          echo "<th><b>Status</b></th>";

          while ($_game = mysqli_fetch_array( $game, MYSQL_ASSOC))
          {
            echo "<tr>";

            echo "<td align='right'>". $_game[id] . "</td>";

            echo "<td align='right'>" . strftime("%A, %d.%m.%y", strtotime(date($_game[timestamp]))) . "</td>";

            echo "<td>". $_game[home] . "</td>";
            echo "<td>". $_game[away] . "</td>";

            echo "<td align='center'>" . strftime("%H:%M", strtotime(date($_game[timestamp]))) . "</td>";

            echo '<td><form action="editscore.php" method="post"><input type="number" name="home_score" value="'. $_game['home_score'] . '">:<input type="number" name="away_score" value="' . $_game['away_score'] . '"><input type="hidden" name="game_id" value="'. $_game[id] .'"><input type="submit" value="Aktualisieren"></form></td>';
            echo "<td align='center'>". $_game[status] . "</td>";
            echo "</tr>";
          }
          echo "</table>";

          // mysqli_close($con);
          ?>
          <br>
          <form action="addgame_submit.php" method="post">
          Heimmanschaft:
          <select name="home_team_id">
          <?php

          $teams = mysqli_query( $con,"SELECT * FROM team WHERE NOT EXISTS (SELECT * FROM game WHERE matchday_id = $matchday_id AND (team.id = home_team_id OR team.id = away_team_id)) AND EXISTS (SELECT * FROM season_has_team WHERE team.id = team_id AND season_id = $_matchday_info[season_id])");

          if (! $teams) {
            die('Ungültige Abfrage: ' . mysqli_error());
          }

          while ($_teams = mysqli_fetch_array( $teams, MYSQL_ASSOC))
          {
          	echo "<option value='" . $_teams['id'] . "'>" .  $_teams['name'] . "</option>";
          }
          ?>
          </select>
          Gastmannschaft:
          <select name="away_team_id">
          <?php

          $teams = mysqli_query( $con,"SELECT * FROM team WHERE NOT EXISTS (SELECT * FROM game WHERE matchday_id = $matchday_id AND (team.id = home_team_id OR team.id = away_team_id)) AND EXISTS (SELECT * FROM season_has_team WHERE team.id = team_id AND season_id = $_matchday_info[season_id])");

          if (! $teams) {
            die('Ungültige Abfrage: ' . mysqli_error());
          }

          while ($_teams = mysqli_fetch_array( $teams, MYSQL_ASSOC))
          {
          	echo "<option value='" . $_teams['id'] . "'>" .  $_teams['name'] . "</option>";
          }

          mysqli_close($con);
          ?>
          </select>
          <!-- Termin: <input type="datetime-local" name="timestamp" value="2014-09-05T15:30:00">  -->
          Termin: <input type="text" name="timestamp" id="timestamp"> 
          <input type="hidden" name="matchday_id" value="<?php echo $matchday_id; ?>">
          <input type="submit" value="Spiel hinzuf&uuml;gen">
          </form><br>

          <form action="calculate.php" method="post">
          <input type="hidden" name="matchday_id" value="<?php echo $matchday_id; ?>">
          <input type="submit" value="Punkte berechnen">
          </form>
          <br><br>
        </article>

      </section>

    </section>

    <footer>
      <a href="mailto:mirco.altenbernd@mathematik.tu-dortmund.de">Kontakt</a>
    </footer>
  </body>
</html>