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

          <form action="addseason_submit.php" method="post">
            Name: <input type="text" name="name">
            <input type="submit" value="hinzuf&uuml;gen">
          </form><br>

          <?php

          include 'connect.php';

          $season = mysqli_query( $con,"SELECT * FROM season");

          if (! $season) {
            die('Ungültige Abfrage: ' . mysqli_error());
          }

          echo '<table border="1">';
          echo "<th><b>ID</b></th>";
          echo "<th><b>Name</b></th>";
          echo "<th><b>Teams</b></th>";
          echo "<th><b>Spieltage</b></th>";
          echo "<th><b>Punkte f&uuml;r Tendenz</b></th>";
          echo "<th><b>Bonuspunkte</b></th>";
          echo "<th><b>Bearbeiten</b></th>";
          echo "<th><b>Neu berechnen</b></th>";

          while ($zeile = mysqli_fetch_array( $season, MYSQL_ASSOC))
          {
            echo "<tr><form method='post' action='edit_season.php'>";
            echo "<td>". $zeile[id] . "</td>";
            echo "<td><input required name='name' type='text' value='" . $zeile['name'] . "'></input></td>";
            echo "<td><a href='addteamtoseason.php?season_id=" . $zeile['id'] . "'>". $zeile['teams'] . "</a></td>";
            echo "<td><a href='addmatchday.php?season_id=" . $zeile['id'] . "'>" . $zeile['matchdays'] . "</a></td>";
            echo "<td><input required name='tendency' type='text' value='". $zeile['right_tendency'] . "'></input></td>";
            echo "<td><input required name='points' type='text' value='". $zeile['bonus_points'] . "'></input></td>";
            echo "<td><input required type='hidden' name='id' value='". $zeile[id] . "'><input type='submit' value='Aktualisieren'></td></form>";
            echo '<form action="recalculate_season.php" method="post"><td align="center"><input type="hidden" name="season_id" value="' . $zeile[id] . '"><input type="submit" value="Berechnen"></td></form></tr>';
          }
          echo "</table>";
          mysqli_close($con);
          ?>

          <br><br>
        </article>

      </section>

    </section>

    <footer>
      <a href="mailto:mirco.altenbernd@mathematik.tu-dortmund.de">Kontakt</a>
    </footer>
  </body>
</html>