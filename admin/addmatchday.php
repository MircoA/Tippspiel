<!DOCTYPE html>
<html>
  <head>
    <title>LSIII Tippspiel - Saisonverwaltung</title>
    <link rel="stylesheet" href="../style.css" />
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

          $season_id = mysqli_real_escape_string($con, $_GET['season_id']);

          $season = mysqli_query($con, "SELECT name FROM season WHERE id = $season_id");
          $_season = mysqli_fetch_row($season);

          echo $_season[0];

          $matchdays = mysqli_query( $con,"SELECT * FROM matchday WHERE season_id = $season_id");

          if (! $matchdays) {
            die('Ungültige Abfrage: ' . mysqli_error());
          }

          echo '<table border="1">';
          echo "<th><b>ID</b></th>";
          echo "<th><b>Bezeichnung</b></th>";
          echo "<th><b>Nummer</b></th>";
          echo "<th><b>Spiele</b></th>";
          echo "<th><b>Status</b></th>";
          echo "<th><b>Bearbeiten</b></th>";

          while ($_matchdays = mysqli_fetch_array( $matchdays, MYSQL_ASSOC))
          {

            // $test = mysqli_query($con, "SELECT * FROM game WHERE matchday_id = $_matchdays[id] AND status = 0");

            // if (mysqli_num_rows($test) == 0 && $_matchdays[games] != 0) {
            // 	mysqli_query($con, "UPDATE matchday SET status = 1 WHERE id = $_matchdays[id]");
            // 	$_matchdays[status] = 1;
            // } else {
            // 	mysqli_query($con, "UPDATE matchday SET status = 0 WHERE id = $_matchdays[id]");
            // 	$_matchdays[status] = 0;
            // }

            echo "<form method='post' action='edit_matchday.php'><tr>";
            echo "<td align='center'>" . $_matchdays['id'] . "</td>";
            echo "<td align='center'><input required name='name' type='text' value='". $_matchdays['name'] . "'></input></td>";
            echo "<td align='center'><input required name='number' type='number' value='". $_matchdays['matchday_number'] . "'></input></td>";
            echo "<td align='center'><a href='addgame.php?matchday_id=". $_matchdays['id'] . "'>". $_matchdays['games'] . "</a></td>";
            echo "<td align='center'><input required name='status' type='number' value='". $_matchdays['status'] . "'></input></td>";
            echo "<td align='center'><input type='hidden' name='id' value='" . $_matchdays['id'] . "'><input type='hidden' name='season_id' value='" . $_matchdays['season_id'] . "'><input type='submit' value='Aktualisieren'></td>";
            echo "</tr></form>";
          }
          echo "</table>";

          mysqli_close($con);
          ?>
          <br><form action="addmatchday_submit.php" method="post">
          <input type="hidden" name="season_id" value="<?php echo $season_id?>">
          <input type="submit" value="Spieltag hinzuf&uuml;gen">
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