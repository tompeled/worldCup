<?php
session_start();
date_default_timezone_set("Europe/Lisbon");
?>

<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
        <link rel="stylesheet" type="text/css" href="style.css">
    </head>
    <body>

        <header>
            <nav>
                <div class="main-wrapper">
                    <ul>
                        <li><a href="index.php">Home</a></li>
                        <li><a href="tabelas.php">Classificações</a></li>
                        <li><a href="ranking.php">Ranking</a></li>
                        <li><a href="calendario.php">Jogos</a></li>
                        <li><a href="history.php">Histórico</a></li>
                        <?php
                        if (isset($_SESSION["u_id"]))
                            echo '<li><a href="bet.php">Apostas</a></li>';
                        ?>
                    </ul>
                    <div class="nav-login">
                        <?php
                        if (isset($_SESSION["u_id"])) {
                            echo '<form action="includes/logout.inc.php" method="POST">';
                            echo '<button type="submit" name="submit">Logout</button>';
                            echo '</form>';
                        } else {
                            echo '<form action="includes/login.inc.php" method="POST">';
                            echo '<input type="text" name="uid" placeholder="Username/email">';
                            echo '<input type="password" name="pwd" placeholder="Password">';
                            echo '<button type="submit" name="submit">Login</button>';
                            echo '</form>';
                            echo '<a href="signup.php">Sign up</a>';
                        }
                        ?>

                    </div>
                </div>
            </nav>
        </header>