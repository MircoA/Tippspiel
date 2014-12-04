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
          
          <form action="addteamtoseason_submit.php" method="post">
            <select name="team_id">
            <?php

            include 'connect.php';

            $season_id = mysqli_real_escape_string($con, $_GET['season_id']);

            $teams = mysqli_query( $con,"SELECT team.name, team.id FROM team WHERE NOT EXISTS (SELECT * FROM season_has_team WHERE season_id = $season_id AND team_id = team.id) ORDER BY id");

            if (! $teams) {
              die('Ungültige Abfrage: ' . mysqli_error());
            }

            while ($_teams = mysqli_fetch_array( $teams, MYSQL_ASSOC))
            {
              echo "<option value='" . $_teams['id'] . "'>" .  $_teams['name'] . "</option>";
            }

            ?>
            </select>
            <input type="hidden" name="season_id" value="<?php echo $season_id?>">
            <input type="submit" value="Team hinzuf&uuml;gen">
          </form>
          <?php

          $season = mysqli_query($con, "SELECT name FROM season WHERE id = $season_id");
          $_season = mysqli_fetch_row($season);

          echo 'Teams der ' . $_season[0] .':';

          $teams = mysqli_query( $con,"SELECT * FROM season_has_team WHERE season_id = $season_id");

          if (! $teams) {
            die('Ungültige Abfrage: ' . mysqli_error());
          }

          echo '<table border="1">';
          echo '<th>ID</th>';
          echo '<th>Name</th>';
          while ($_teams = mysqli_fetch_array( $teams, MYSQL_ASSOC))
          {
            
            $name = mysqli_query($con, "SELECT id, name FROM team WHERE id = $_teams[team_id]");
            $_name = mysqli_fetch_array($name);

            echo "<tr>";
            echo "<td>" . $_name[id] . "</td>";
            echo "<td>" . $_name[name] . "</td>";
            echo "</tr>";
          }
          echo "</table>";

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