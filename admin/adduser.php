<!DOCTYPE html>
<html>
  <head>
    <title>LSIII Tippspiel - Nutzerverwaltung</title>
    <link rel="stylesheet" href="../style.css" />
  </head>

  <body>
  
    <header>
      <img class="left" src="../img/m_schrift.png" alt="logo-m!" /><span style="margin-left:25%;"><font size=8 color=#85B817><b>Tippspiel</b></font></span><img class="right" src="../img/lehrstuhl3.png" alt="logo-ls3" />
    </header>

    <nav>
      <ul class="nav">
        <li class="nav"><a class="nav" href = "index.php">Startseite</a></li>
        <li class="nav"><a class="nav_current" href = "adduser.php">Nutzerverwaltung</a></li>
        <li class="nav"><a class="nav" href = "addseason.php">Saisonverwaltung</a></li>
        <li class="nav"><a class="nav" href = "addteam.php">Teamverwaltung</a></li>
        <span style="float:right;"><li class="nav"><a class="nav" href="../index.php"><b>Nutzer</b></a></li></span>
      </ul>
    </nav>
    <section class="content">
      <section class="full">
        <article class="news">

          <form action="adduser_submit.php" method="post">
            Vorname: <input type="text" name="name">
            Nachname: <input type="text" name="surname">
            K&uuml;rzel: <input type="text" name="username">
            E-Mail: <input type="mail" name="email">
            Passwort: <input type="password" name="password">
            <input type="submit" value="hinzuf&uuml;gen">
          </form><br>

          <?php

          include 'connect.php';

          $user = mysqli_query( $con,"SELECT * FROM user ORDER BY id");

          if (! $user) {
            die('Ungültige Abfrage: ' . mysqli_error());
          }

          echo '<table border="1">';
          echo "<th><b>ID</b></th>";
          echo "<th><b>Vorname</b></th>";
          echo "<th><b>Nachname</b></th>";
          echo "<th><b>K&uuml;rzel</b></th>";
          echo "<th><b>E-Mail</b></th>";
          echo "<th><b>Passwort</b></th>";
          echo "<th><b>Bearbeiten</b></th>";

          while ($zeile = mysqli_fetch_array( $user, MYSQL_ASSOC))
          {
            echo "<tr><form method='post' action='edit_user.php'>";
            echo "<td>".  $zeile['id'] . "</td>";
            echo "<td><input required name='name' type='text' value='" .  $zeile['name'] . "'></td>";
            echo "<td><input required name='surname' type='text' value='" .  $zeile['surname'] . "'></td>";
            echo "<td><input required name='username' type='text' value='" .  $zeile['username'] . "'></td>";
            echo "<td><input required name='email' type='mail' value='" .  $zeile['email'] . "'></td>";
            echo "<td><input name='pwd' type='password'></td>";
            echo '<td><input type="hidden" name="id" value="' . $zeile[id] . '"><input type="submit" value="Aktualisieren">';
            echo "</form></tr>";
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