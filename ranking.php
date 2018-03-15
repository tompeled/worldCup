<?php
include_once "header.php";
?>

<section class="main-container">
    <div class="main-wrapper">
        <h2>Ranking(TO DO)</h2>
        <?php
        if (isset($_SESSION["u_id"])) {
            echo "You are logged in!";
        }
        ?>
    </div>

</section>

<?php
//calcular pontos

include "includes/dbhworld.inc.php";
include_once "phplib-football-data-master/FootballData.php";

//1.update games
//1.1. Fase de grupos
$sql = 'SELECT games.id, games.dia_hora, teams.id AS id1, t.id AS id2, games.res1, games.res2, teams.grupo AS grupo, games.fixture_id  FROM games JOIN teams ON games.equipa1=teams.id JOIN teams t ON games.equipa2=t.id;';
$result = mysqli_query($conn, $sql);

$checkdt = true;

$api = new FootballData();


while ($row = mysqli_fetch_assoc($result)) {

    $date = date_create($row["dia_hora"]);


    if ($checkdt && ($date < date_create("now")) && ($row["stat"] != 3 && $row["stat"] != 2)) {
        $fixture = $api->getFixtureById($row["fixture_id"]);

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

    } else {
        if ($row["stat"] != 3 && $row["stat"] != 2)
            $checkdt = false;
    }
}


//1.2.Eliminatorias
$sql = "SELECT knockout.id, knockout.dia_hora, teams.nome AS eq1, t.nome AS eq2, teams.id AS id1, t.id AS id2, knockout.res1, knockout.res2, knockout.fixture_id, knockout.ph1, knockout.ph2  FROM knockout LEFT JOIN teams ON knockout.equipa1=teams.id LEFT JOIN teams t ON knockout.equipa2=t.id WHERE knockout.id<=8";
$result = mysqli_query($conn, $sql);

$checkdt = true;

$api = new FootballData();


while ($row = mysqli_fetch_assoc($result)) {

    $date = date_create($row["dia_hora"]);


    if ($checkdt && ($date < date_create("now")) && ($row["stat"] != 3 && $row["stat"] != 2)) {
        $fixture = $api->getFixtureById($row["fixture_id"]);

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

    } else {
        if ($row["stat"] != 3 && $row["stat"] != 2)
            $checkdt = false;
    }
}


//2.updateRank
//2.1. fase de grupos
$sql = "SELECT games.id, games.res1, games.res2 FROM games WHERE stat=2";
$result = mysqli_query($conn, $sql);
$resultCheck = mysqli_num_rows($result);
$sql = "UPDATE games SET stat=3 WHERE stat=2";
mysqli_query($conn, $sql);
if ($resultCheck > 0) {
    $sql = "SELECT * FROM players";
    $result_players = mysqli_query($conn, $sql);
    while ($player = mysqli_fetch_assoc($result_players)) {
        $pontos = $player["pontos"];
        mysqli_data_seek($result, 0);
        while ($row = mysqli_fetch_assoc($result)) {
            $prev1 = $player["J" . $row["id"] . "_1"];
            $prev2 = $player["J" . $row["id"] . "_2"];
            $pontos += abs($prev1 - $row["res1"]) + abs($prev2 - $row["res2"]);
            if ($prev1 > $prev2 && $row["res1"] <= $row["res2"] || $prev1 < $prev2 && $row["res1"] >= $row["res2"] || $prev1 == $prev2 && $row["res1"] != $row["res2"])
                $pontos += 3;
        }
        $sql = "UPDATE players SET pontos = '$pontos' WHERE id=" . $player["id"];
        mysqli_query($conn, $sql);
    }
}

//2.2. Eliminatorias
$sql = "SELECT knockout.id, knockout.res1, knockout.res2 FROM knockout WHERE stat=2";
$result = mysqli_query($conn, $sql);
$resultCheck = mysqli_num_rows($result);
$sql = "UPDATE knockout SET stat=3 WHERE stat=2";
mysqli_query($conn, $sql);
if ($resultCheck > 0) {
    $sql = "SELECT * FROM players";
    $result_players = mysqli_query($conn, $sql);
    while ($player = mysqli_fetch_assoc($result_players)) {
        $pontos = $player["pontos"];
        mysqli_data_seek($result, 0);
        while ($row = mysqli_fetch_assoc($result)) {
            $prev1 = $player["J" . ($row["id"] + 48) . "_1"];
            $prev2 = $player["J" . ($row["id"] + 48) . "_2"];
            $pontos += abs($prev1 - $row["res1"]) + abs($prev2 - $row["res2"]);
            if ($prev1 > $prev2 && $row["res1"] <= $row["res2"] || $prev1 < $prev2 && $row["res1"] >= $row["res2"] || $prev1 == $prev2 && $row["res1"] != $row["res2"])
                $pontos += 3;
        }
        $sql = "UPDATE players SET pontos = '$pontos' WHERE id=" . $player["id"];
        mysqli_query($conn, $sql);
    }
}


//mostrar classificacao dos jogadores

$sql = "SELECT * FROM players ORDER BY pontos";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    echo '<table class="ranking">';
    echo '<tr><td>Jogador</td><td>Pontos</td></tr>';
    while ($row = mysqli_fetch_assoc($result)) {
        echo '<tr><td>' . $row["nome"] . '</td>' . '<td>' . $row["pontos"] . '</td></tr>';
    }
    echo '</table>';
} else {
    echo "error connecting to database";
}

echo '<div class="flex-ranking">';
echo '<table class="todas-apostas"><tr>';
//mostrar apostas

$sql = "SELECT * FROM players";
$result = mysqli_query($conn, $sql);
while ($row = mysqli_fetch_assoc($result)) {
    $i = 1;
    echo '<td>';
    echo '<table class="apostas">';
    echo '<tr>';
    echo '<th colspan="2">' . $row["nome"] . '</th>';
    echo '</tr>';
    while ($i < 65) {
        echo '<tr>';
        echo '<td>' . $row["J" . $i . "_1"] . '</td>';
        echo '<td>' . $row["J" . $i . "_2"] . '</td>';
        echo '</tr>';
        $i++;
    }
    echo '</table>';
    echo '</td>';
}
echo '</tr></table>';
echo '</div>';
?>


<?php
include_once "footer.php";
?>

