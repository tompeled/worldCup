<?php
include_once "header.php";
?>

<section class="main-container">
    <div class="main-wrapper">
        <h2>Calendario</h2>
        <?php
        if (isset($_SESSION["u_id"])) {
            echo "You are logged in!";
        }
        ?>
    </div>

</section>

<?php
include_once "includes/dbhworld.inc.php";
include $_SERVER["DOCUMENT_ROOT"] . '/phplib-football-data-master/FootballData.php';
date_default_timezone_set('Europe/Lisbon');


echo '<h2>Fase de Grupos</h2>';
echo '<table class="jogos">';
echo '<tr>';
echo '<th>Dia</th>';
echo '<th>Hora</th>';
echo '<th></th>';
echo '<th></th>';
echo '<th></th>';
echo '<th></th>';
echo '<th>Grupo</th>';
echo '</tr>';

$sql = 'SELECT games.id, games.dia_hora, teams.nome AS eq1, t.nome AS eq2, teams.id AS id1, t.id AS id2,games.res1, games.res2, teams.grupo AS grupo, games.fixture_id  FROM games JOIN teams ON games.equipa1=teams.id JOIN teams t ON games.equipa2=t.id;';
$result = mysqli_query($conn, $sql);

$checkdt = true;

$api = new FootballData();


while ($row = mysqli_fetch_assoc($result)) {
    echo '<tr>';
    $date = date_create($row["dia_hora"]);
    echo '<td>' . date_format($date, 'j/n') . '</td>';
    echo '<td>' . date_format($date, 'G:i') . '</td>';
    echo '<td>' . $row["eq1"] . '</td>';

    if ($checkdt && ($date < date_create("now")) && ($row["stat"] != 3 && $row["stat"] != 2)) {
        $fixture = $api->getFixtureById($row["fixture_id"]);
        if (is_null($fixture->fixture->result->goalsHomeTeam)) {
            echo '<td>-</td>';
        } else {
            echo '<td>' . $fixture->fixture->result->goalsHomeTeam . '</td>';
        }
        if (is_null($fixture->fixture->result->goalsHomeTeam)) {
            echo '<td>-</td>';
        } else {
            echo '<td>' . $fixture->fixture->result->goalsAwayTeam . '</td>';
        }

        //verificar se acabou
        if ($fixture->fixture->status == "FINISHED") {
            if ($fixture->fixture->result->goalsHomeTeam > $fixture->fixture->result->goalsAwayTeam)
                $sql = "UPDATE games SET stat=2, res1 =" . $fixture->fixture->result->goalsHomeTeam . ", res2 =" . $fixture->fixture->result->goalsAwayTeam . " ,winner=" . $row["id1"] . " ,loser=" . $row["id2"] . " WHERE id =" . $row["id"];
            elseif ($fixture->fixture->result->goalsHomeTeam < $fixture->fixture->result->goalsAwayTeam)
                $sql = "UPDATE games SET stat=2, res1 =" . $fixture->fixture->result->goalsHomeTeam . ", res2 =" . $fixture->fixture->result->goalsAwayTeam . " ,winner=" . $row["id2"] . " ,loser=" . $row["id1"] . " WHERE id =" . $row["id"];
            else
                $sql = "UPDATE games SET stat=2, res1 =" . $fixture->fixture->result->goalsHomeTeam . ", res2 =" . $fixture->fixture->result->goalsAwayTeam . " ,winner=0 ,loser=0 WHERE id =" . $row["id"];
            mysqli_query($conn, $sql);

        }

        //prolongamento e penaltis TODO


    } else {
        if ($row["stat"] != 3 && $row["stat"] != 2)
            $checkdt = false;
        if (is_null($row["res1"])) {
            echo '<td>-</td>';
        } else {
            echo '<td>' . $row["res1"] . '</td>';
        }
        if (is_null($row["res2"])) {
            echo '<td>-</td>';
        } else {
            echo '<td>' . $row["res2"] . '</td>';
        }
    }
    echo '<td>' . $row["eq2"] . '</td>';
    echo '<td>' . $row["grupo"];
    echo '</tr>';

}
echo '</table>';

?>

<?php
//Oitavos de final

echo '<h2>Oitavos de Final</h2>';
echo '<table class="oitavos jogos">';
echo '<tr>';
echo '<th>Dia</th>';
echo '<th>Hora</th>';
echo '<th></th>';
echo '<th></th>';
echo '<th></th>';
echo '<th></th>';
echo '</tr>';


$sql = "SELECT knockout.id, knockout.dia_hora, teams.nome AS eq1, t.nome AS eq2, teams.id AS id1, t.id AS id2, knockout.res1, knockout.res2, knockout.fixture_id, knockout.ph1, knockout.ph2  FROM knockout LEFT JOIN teams ON knockout.equipa1=teams.id LEFT JOIN teams t ON knockout.equipa2=t.id WHERE knockout.id<=8";
$result = mysqli_query($conn, $sql);

$checkdt = true;

$api = new FootballData();


while ($row = mysqli_fetch_assoc($result)) {
    echo '<tr>';
    $date = date_create($row["dia_hora"]);
    echo '<td>' . date_format($date, 'j/n') . '</td>';
    echo '<td>' . date_format($date, 'G:i') . '</td>';

    if (is_null($row["eq1"]))
        echo '<td>' . $row["ph1"] . '</td>';
    else
        echo '<td>' . $row["eq1"] . '</td>';

    if ($checkdt && ($date < date_create("now")) && ($row["stat"] != 3 && $row["stat"] != 2)) {
        $fixture = $api->getFixtureById($row["fixture_id"]);
        if (is_null($fixture->fixture->result->goalsHomeTeam)) {
            echo '<td>-</td>';
        } else {
            echo '<td>' . $fixture->fixture->result->goalsHomeTeam . '</td>';
        }
        if (is_null($fixture->fixture->result->goalsHomeTeam)) {
            echo '<td>-</td>';
        } else {
            echo '<td>' . $fixture->fixture->result->goalsAwayTeam . '</td>';
        }

        //verificar se acabou
        if ($fixture->fixture->status == "FINISHED") {
            if ($fixture->fixture->result->goalsHomeTeam > $fixture->fixture->result->goalsAwayTeam)
                $sql = "UPDATE knockout SET stat=2, res1 =" . $fixture->fixture->result->goalsHomeTeam . ", res2 =" . $fixture->fixture->result->goalsAwayTeam . " ,winner=" . $row["id1"] . " ,loser=" . $row["id2"] . " WHERE id =" . $row["id"];
            elseif ($fixture->fixture->result->goalsHomeTeam < $fixture->fixture->result->goalsAwayTeam)
                $sql = "UPDATE knockout SET stat=2, res1 =" . $fixture->fixture->result->goalsHomeTeam . ", res2 =" . $fixture->fixture->result->goalsAwayTeam . " ,winner=" . $row["id2"] . " ,loser=" . $row["id1"] . " WHERE id =" . $row["id"];
            else
                $sql = "UPDATE knockout SET stat=2, res1 =" . $fixture->fixture->result->goalsHomeTeam . ", res2 =" . $fixture->fixture->result->goalsAwayTeam . " ,winner=0 ,loser=0 WHERE id =" . $row["id"];
            mysqli_query($conn, $sql);

        }

        //prolongamento e penaltis TODO


    } else {
        if ($row["stat"] != 3 && $row["stat"] != 2)
            $checkdt = false;
        if (is_null($row["res1"])) {
            echo '<td>-</td>';
        } else {
            echo '<td>' . $row["res1"] . '</td>';
        }
        if (is_null($row["res2"])) {
            echo '<td>-</td>';
        } else {
            echo '<td>' . $row["res2"] . '</td>';
        }
    }
    if (is_null($row["eq2"]))
        echo '<td>' . $row["ph2"] . '</td>';
    else
        echo '<td>' . $row["eq2"] . '</td>';
    echo '</tr>';


}

echo '</table>';


?>

<?php
//Quartos de final

echo '<h2>Quartos de Final</h2>';
echo '<table class="quartos jogos">';
echo '<tr>';
echo '<th>Dia</th>';
echo '<th>Hora</th>';
echo '<th></th>';
echo '<th></th>';
echo '<th></th>';
echo '<th></th>';
echo '</tr>';


$sql = "SELECT knockout.id, knockout.dia_hora, teams.nome AS eq1, t.nome AS eq2, teams.id AS id1, t.id AS id2, knockout.res1, knockout.res2, knockout.fixture_id, knockout.ph1, knockout.ph2  FROM knockout LEFT JOIN teams ON knockout.equipa1=teams.id LEFT JOIN teams t ON knockout.equipa2=t.id WHERE 8<knockout.id AND knockout.id<=12";
$result = mysqli_query($conn, $sql);

$checkdt = true;

$api = new FootballData();


while ($row = mysqli_fetch_assoc($result)) {
    echo '<tr>';
    $date = date_create($row["dia_hora"]);
    echo '<td>' . date_format($date, 'j/n') . '</td>';
    echo '<td>' . date_format($date, 'G:i') . '</td>';

    if (is_null($row["eq1"]))
        echo '<td>' . $row["ph1"] . '</td>';
    else
        echo '<td>' . $row["eq1"] . '</td>';

    if ($checkdt && ($date < date_create("now")) && ($row["stat"] != 3 && $row["stat"] != 2)) {
        $fixture = $api->getFixtureById($row["fixture_id"]);
        if (is_null($fixture->fixture->result->goalsHomeTeam)) {
            echo '<td>-</td>';
        } else {
            echo '<td>' . $fixture->fixture->result->goalsHomeTeam . '</td>';
        }
        if (is_null($fixture->fixture->result->goalsHomeTeam)) {
            echo '<td>-</td>';
        } else {
            echo '<td>' . $fixture->fixture->result->goalsAwayTeam . '</td>';
        }

        //verificar se acabou
        if ($fixture->fixture->status == "FINISHED") {
            if ($fixture->fixture->result->goalsHomeTeam > $fixture->fixture->result->goalsAwayTeam)
                $sql = "UPDATE knockout SET stat=2, res1 =" . $fixture->fixture->result->goalsHomeTeam . ", res2 =" . $fixture->fixture->result->goalsAwayTeam . " ,winner=" . $row["id1"] . " ,loser=" . $row["id2"] . " WHERE id =" . $row["id"];
            elseif ($fixture->fixture->result->goalsHomeTeam < $fixture->fixture->result->goalsAwayTeam)
                $sql = "UPDATE knockout SET stat=2, res1 =" . $fixture->fixture->result->goalsHomeTeam . ", res2 =" . $fixture->fixture->result->goalsAwayTeam . " ,winner=" . $row["id2"] . " ,loser=" . $row["id1"] . " WHERE id =" . $row["id"];
            else
                $sql = "UPDATE knockout SET stat=2, res1 =" . $fixture->fixture->result->goalsHomeTeam . ", res2 =" . $fixture->fixture->result->goalsAwayTeam . " ,winner=0 ,loser=0 WHERE id =" . $row["id"];
            mysqli_query($conn, $sql);

        }

        //prolongamento e penaltis TODO


    } else {
        if ($row["stat"] != 3 && $row["stat"] != 2)
            $checkdt = false;
        if (is_null($row["res1"])) {
            echo '<td>-</td>';
        } else {
            echo '<td>' . $row["res1"] . '</td>';
        }
        if (is_null($row["res2"])) {
            echo '<td>-</td>';
        } else {
            echo '<td>' . $row["res2"] . '</td>';
        }
    }
    if (is_null($row["eq2"]))
        echo '<td>' . $row["ph2"] . '</td>';
    else
        echo '<td>' . $row["eq2"] . '</td>';
    echo '</tr>';


}

echo '</table>';


?>
<?php

//Semi-Finais

echo '<h2>Semi-Finais</h2>';
echo '<table class="semi jogos">';
echo '<tr>';
echo '<th>Dia</th>';
echo '<th>Hora</th>';
echo '<th></th>';
echo '<th></th>';
echo '<th></th>';
echo '<th></th>';
echo '</tr>';


$sql = "SELECT knockout.id, knockout.dia_hora, teams.nome AS eq1, t.nome AS eq2, teams.id AS id1, t.id AS id2, knockout.res1, knockout.res2, knockout.fixture_id, knockout.ph1, knockout.ph2  FROM knockout LEFT JOIN teams ON knockout.equipa1=teams.id LEFT JOIN teams t ON knockout.equipa2=t.id WHERE 12<knockout.id AND knockout.id<=14";
$result = mysqli_query($conn, $sql);

$checkdt = true;

$api = new FootballData();


while ($row = mysqli_fetch_assoc($result)) {
    echo '<tr>';
    $date = date_create($row["dia_hora"]);
    echo '<td>' . date_format($date, 'j/n') . '</td>';
    echo '<td>' . date_format($date, 'G:i') . '</td>';

    if (is_null($row["eq1"]))
        echo '<td>' . $row["ph1"] . '</td>';
    else
        echo '<td>' . $row["eq1"] . '</td>';

    if ($checkdt && ($date < date_create("now")) && ($row["stat"] != 3 && $row["stat"] != 2)) {
        $fixture = $api->getFixtureById($row["fixture_id"]);
        if (is_null($fixture->fixture->result->goalsHomeTeam)) {
            echo '<td>-</td>';
        } else {
            echo '<td>' . $fixture->fixture->result->goalsHomeTeam . '</td>';
        }
        if (is_null($fixture->fixture->result->goalsHomeTeam)) {
            echo '<td>-</td>';
        } else {
            echo '<td>' . $fixture->fixture->result->goalsAwayTeam . '</td>';
        }

        //verificar se acabou
        if ($fixture->fixture->status == "FINISHED") {
            if ($fixture->fixture->result->goalsHomeTeam > $fixture->fixture->result->goalsAwayTeam)
                $sql = "UPDATE knockout SET stat=2, res1 =" . $fixture->fixture->result->goalsHomeTeam . ", res2 =" . $fixture->fixture->result->goalsAwayTeam . " ,winner=" . $row["id1"] . " ,loser=" . $row["id2"] . " WHERE id =" . $row["id"];
            elseif ($fixture->fixture->result->goalsHomeTeam < $fixture->fixture->result->goalsAwayTeam)
                $sql = "UPDATE knockout SET stat=2, res1 =" . $fixture->fixture->result->goalsHomeTeam . ", res2 =" . $fixture->fixture->result->goalsAwayTeam . " ,winner=" . $row["id2"] . " ,loser=" . $row["id1"] . " WHERE id =" . $row["id"];
            else
                $sql = "UPDATE knockout SET stat=2, res1 =" . $fixture->fixture->result->goalsHomeTeam . ", res2 =" . $fixture->fixture->result->goalsAwayTeam . " ,winner=0 ,loser=0 WHERE id =" . $row["id"];
            mysqli_query($conn, $sql);

        }

        //prolongamento e penaltis TODO


    } else {
        if ($row["stat"] != 3 && $row["stat"] != 2)
            $checkdt = false;
        if (is_null($row["res1"])) {
            echo '<td>-</td>';
        } else {
            echo '<td>' . $row["res1"] . '</td>';
        }
        if (is_null($row["res2"])) {
            echo '<td>-</td>';
        } else {
            echo '<td>' . $row["res2"] . '</td>';
        }
    }
    if (is_null($row["eq2"]))
        echo '<td>' . $row["ph2"] . '</td>';
    else
        echo '<td>' . $row["eq2"] . '</td>';
    echo '</tr>';


}

echo '</table>';


?>

<?php
//Terceiro Lugar

echo '<h2>Terceiro Lugar</h2>';
echo '<table class="terceiro jogos">';
echo '<tr>';
echo '<th>Dia</th>';
echo '<th>Hora</th>';
echo '<th></th>';
echo '<th></th>';
echo '<th></th>';
echo '<th></th>';
echo '</tr>';


$sql = "SELECT knockout.id, knockout.dia_hora, teams.nome AS eq1, t.nome AS eq2, teams.id AS id1, t.id AS id2, knockout.res1, knockout.res2, knockout.fixture_id, knockout.ph1, knockout.ph2  FROM knockout LEFT JOIN teams ON knockout.equipa1=teams.id LEFT JOIN teams t ON knockout.equipa2=t.id WHERE knockout.id=15";
$result = mysqli_query($conn, $sql);

$checkdt = true;

$api = new FootballData();


while ($row = mysqli_fetch_assoc($result)) {
    echo '<tr>';
    $date = date_create($row["dia_hora"]);
    echo '<td>' . date_format($date, 'j/n') . '</td>';
    echo '<td>' . date_format($date, 'G:i') . '</td>';

    if (is_null($row["eq1"]))
        echo '<td>' . $row["ph1"] . '</td>';
    else
        echo '<td>' . $row["eq1"] . '</td>';

    if ($checkdt && ($date < date_create("now")) && ($row["stat"] != 3 && $row["stat"] != 2)) {
        $fixture = $api->getFixtureById($row["fixture_id"]);
        if (is_null($fixture->fixture->result->goalsHomeTeam)) {
            echo '<td>-</td>';
        } else {
            echo '<td>' . $fixture->fixture->result->goalsHomeTeam . '</td>';
        }
        if (is_null($fixture->fixture->result->goalsHomeTeam)) {
            echo '<td>-</td>';
        } else {
            echo '<td>' . $fixture->fixture->result->goalsAwayTeam . '</td>';
        }

        //verificar se acabou
        if ($fixture->fixture->status == "FINISHED") {
            if ($fixture->fixture->result->goalsHomeTeam > $fixture->fixture->result->goalsAwayTeam)
                $sql = "UPDATE knockout SET stat=2, res1 =" . $fixture->fixture->result->goalsHomeTeam . ", res2 =" . $fixture->fixture->result->goalsAwayTeam . " ,winner=" . $row["id1"] . " ,loser=" . $row["id2"] . " WHERE id =" . $row["id"];
            elseif ($fixture->fixture->result->goalsHomeTeam < $fixture->fixture->result->goalsAwayTeam)
                $sql = "UPDATE knockout SET stat=2, res1 =" . $fixture->fixture->result->goalsHomeTeam . ", res2 =" . $fixture->fixture->result->goalsAwayTeam . " ,winner=" . $row["id2"] . " ,loser=" . $row["id1"] . " WHERE id =" . $row["id"];
            else
                $sql = "UPDATE knockout SET stat=2, res1 =" . $fixture->fixture->result->goalsHomeTeam . ", res2 =" . $fixture->fixture->result->goalsAwayTeam . " ,winner=0 ,loser=0 WHERE id =" . $row["id"];
            mysqli_query($conn, $sql);

        }

        //prolongamento e penaltis TODO


    } else {
        if ($row["stat"] != 3 && $row["stat"] != 2)
            $checkdt = false;
        if (is_null($row["res1"])) {
            echo '<td>-</td>';
        } else {
            echo '<td>' . $row["res1"] . '</td>';
        }
        if (is_null($row["res2"])) {
            echo '<td>-</td>';
        } else {
            echo '<td>' . $row["res2"] . '</td>';
        }
    }
    if (is_null($row["eq2"]))
        echo '<td>' . $row["ph2"] . '</td>';
    else
        echo '<td>' . $row["eq2"] . '</td>';
    echo '</tr>';


}

echo '</table>';


?>
<?php
//Final

echo '<h2>Final</h2>';
echo '<table class="final jogos">';
echo '<tr>';
echo '<th>Dia</th>';
echo '<th>Hora</th>';
echo '<th></th>';
echo '<th></th>';
echo '<th></th>';
echo '<th></th>';
echo '</tr>';


$sql = "SELECT knockout.id, knockout.dia_hora, teams.nome AS eq1, t.nome AS eq2, teams.id AS id1, t.id AS id2, knockout.res1, knockout.res2, knockout.fixture_id, knockout.ph1, knockout.ph2  FROM knockout LEFT JOIN teams ON knockout.equipa1=teams.id LEFT JOIN teams t ON knockout.equipa2=t.id WHERE knockout.id=16";
$result = mysqli_query($conn, $sql);

$checkdt = true;

$api = new FootballData();


while ($row = mysqli_fetch_assoc($result)) {
    echo '<tr>';
    $date = date_create($row["dia_hora"]);
    echo '<td>' . date_format($date, 'j/n') . '</td>';
    echo '<td>' . date_format($date, 'G:i') . '</td>';

    if (is_null($row["eq1"]))
        echo '<td>' . $row["ph1"] . '</td>';
    else
        echo '<td>' . $row["eq1"] . '</td>';

    if ($checkdt && ($date < date_create("now")) && ($row["stat"] != 3 && $row["stat"] != 2)) {
        $fixture = $api->getFixtureById($row["fixture_id"]);
        if (is_null($fixture->fixture->result->goalsHomeTeam)) {
            echo '<td>-</td>';
        } else {
            echo '<td>' . $fixture->fixture->result->goalsHomeTeam . '</td>';
        }
        if (is_null($fixture->fixture->result->goalsHomeTeam)) {
            echo '<td>-</td>';
        } else {
            echo '<td>' . $fixture->fixture->result->goalsAwayTeam . '</td>';
        }

        //verificar se acabou
        if ($fixture->fixture->status == "FINISHED") {
            if ($fixture->fixture->result->goalsHomeTeam > $fixture->fixture->result->goalsAwayTeam)
                $sql = "UPDATE knockout SET stat=2, res1 =" . $fixture->fixture->result->goalsHomeTeam . ", res2 =" . $fixture->fixture->result->goalsAwayTeam . " ,winner=" . $row["id1"] . " ,loser=" . $row["id2"] . " WHERE id =" . $row["id"];
            elseif ($fixture->fixture->result->goalsHomeTeam < $fixture->fixture->result->goalsAwayTeam)
                $sql = "UPDATE knockout SET stat=2, res1 =" . $fixture->fixture->result->goalsHomeTeam . ", res2 =" . $fixture->fixture->result->goalsAwayTeam . " ,winner=" . $row["id2"] . " ,loser=" . $row["id1"] . " WHERE id =" . $row["id"];
            else
                $sql = "UPDATE knockout SET stat=2, res1 =" . $fixture->fixture->result->goalsHomeTeam . ", res2 =" . $fixture->fixture->result->goalsAwayTeam . " ,winner=0 ,loser=0 WHERE id =" . $row["id"];
            mysqli_query($conn, $sql);

        }

        //prolongamento e penaltis TODO


    } else {
        if ($row["stat"] != 3 && $row["stat"] != 2)
            $checkdt = false;
        if (is_null($row["res1"])) {
            echo '<td>-</td>';
        } else {
            echo '<td>' . $row["res1"] . '</td>';
        }
        if (is_null($row["res2"])) {
            echo '<td>-</td>';
        } else {
            echo '<td>' . $row["res2"] . '</td>';
        }
    }
    if (is_null($row["eq2"]))
        echo '<td>' . $row["ph2"] . '</td>';
    else
        echo '<td>' . $row["eq2"] . '</td>';
    echo '</tr>';


}

echo '</table>';


?>

<?php
include_once "footer.php";
?>

