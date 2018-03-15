<?php
include_once "header.php";
?>

<section class="main-container">
    <div class="main-wrapper">
        <h2>Classificações</h2>
        <?php
        if (isset($_SESSION["u_id"])) {
            echo "You are logged in!";
        }
        ?>
    </div>

</section>

<?php
include "includes/dbhworld.inc.php";
include_once "phplib-football-data-master/FootballData.php";


//atualizar resultados

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


//gerar classificacoes

$sql = "SELECT teams.id, teams.nome, g1.id AS id1, g1.winner AS winner1, g1.loser AS loser1, g1.res1 AS gc_1, g1.res2 AS gf_1, g1.equipa1 AS eqCasa1,g2.id AS id2, g2.winner AS winner2, g2.loser AS loser2,  g2.res1 AS gc_2, g2.res2 AS gf_2, g2.equipa1 AS eqCasa2, g3.id AS id3, g3.winner AS winner3, g3.loser AS loser3, g3.res1 AS gc_3, g3.res2 AS gf_3, g3.equipa1 AS eqCasa3 FROM teams JOIN games g1 ON (teams.id = g1.equipa1 OR teams.id = g1.equipa2) AND teams.game1=g1.id JOIN games g2 ON (teams.id = g2.equipa1 OR teams.id = g2.equipa2) AND teams.game2=g2.id JOIN games g3 ON (teams.id = g3.equipa1 OR teams.id = g3.equipa2) AND teams.game3=g3.id";
$result = mysqli_query($conn, $sql);
while ($row = mysqli_fetch_assoc($result)) {
    $win = 0;
    $tie = 0;
    $lost = 0;
    foreach ([$row["winner1"], $row["winner2"], $row["winner3"]] as $winner) {
        switch ($winner) {
            case null:
                break;
            case 0:
                $tie++;
                break;
            case $row["id"]:
                $win++;
                break;
        }
    }
    foreach ([$row["loser1"], $row["loser2"], $row["loser3"]] as $loser) {
        if ($loser == $row["id"]) {
            $lost++;
        }
    }
    $marcados = 0;
    $sofridos = 0;
    foreach ([1, 2, 3] as $i) {
        if ($row["id"] == $row["eqCasa" . $i]) {
            $marcados += $row["gc_" . $i];
            $sofridos += $row["gf_" . $i];
        } elseif (!is_null($row["eqCasa" . $i])) {
            $marcados += $row["gf_" . $i];
            $sofridos += $row["gc_" . $i];
        }
    }
    $sql = "UPDATE teams SET win=" . $win . ", tie=" . $tie . ", lost=" . $lost . ", marcados=" . $marcados . ", sofridos=" . $sofridos . " WHERE id =" . $row["id"];
    mysqli_query($conn, $sql);
}


//mostrar tabelas de classificacao

echo '<div class="flex-tabelas">';

$letras = ["A", "B", "C", "D", "E", "F", "G", "H"];
foreach ($letras as $atual) {


    //echo '<h3>Grupo ' . $atual . '</h3>';

    echo '<table class="tabela">';
    echo '<tr>';
    echo '<th class="equipa">Grupo' . $atual . '</th>';
    echo '<th>PD</th>';
    echo '<th>V</th>';
    echo '<th>E</th>';
    echo '<th>D</th>';
    echo '<th>GM</th>';
    echo '<th>GS</th>';
    echo '<th>DG</th>';
    echo '<th>Pts</th>';
    echo '</tr>';

    $sql = "SELECT * FROM teams WHERE teams.grupo='" . $atual . "' ORDER BY ((teams.win*3)+teams.tie) DESC, (teams.marcados-teams.sofridos) DESC, teams.marcados DESC;";
    $result = mysqli_query($conn, $sql);
    $rownum = mysqli_num_rows($result);

    if ($rownum == 4) {

        while ($row = mysqli_fetch_assoc($result)) {
            $diff = $row["marcados"] - $row["sofridos"];
            $pontos = ($row["win"] * 3) + $row["tie"];
            $jogos = $row["win"] + $row["tie"] + $row["lost"];
            echo '<tr>';
            echo '<td class="equipa">' . $row["nome"] . '</td>';
            echo '<td>' . $jogos . '</td>';
            echo '<td>' . $row["win"] . '</td>';
            echo '<td>' . $row["tie"] . '</td>';
            echo '<td>' . $row["lost"] . '</td>';
            echo '<td>' . $row["marcados"] . '</td>';
            echo '<td>' . $row["sofridos"] . '</td>';
            echo '<td>' . $diff . '</td>';
            echo '<td>' . $pontos . '</td>';
            echo '</tr>';
        }
    }


    echo '</table>';
}
echo '</div>';


?>
<?php
include_once "footer.php";
?>

