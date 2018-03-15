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

        echo '<div class="apostas-e-acc">';
        echo '<div class="res-fase-grupos">';
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

        $jogo = 1;
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

            } else {
                if ($row["stat"] != 3 && $row["stat"] != 2)
                    $checkdt = false;
                if (is_null($row["res1"])) {
                    echo '<td id="' . 'J' . $jogo . '_1' . '">-</td>';
                } else {
                    echo '<td id="' . 'J' . $jogo . '_1' . '">' . $row["res1"] . '</td>';
                }
                if (is_null($row["res2"])) {
                    echo '<td id="' . 'J' . $jogo . '_2' . '">-</td>';
                } else {
                    echo '<td id="' . 'J' . $jogo . '_2' . '">' . $row["res2"] . '</td>';
                }
            }
            echo '<td>' . $row["eq2"] . '</td>';
            echo '<td>' . $row["grupo"];
            echo '</tr>';
            $jogo++;

        }
        echo '</table>';
        echo '</div>';

        ?>
        <?php
        //Apostas Fase de grupos + pontos acc

        echo '<div class="apostas-fase-grupos">';
        echo '<h2 class="heading-apostas">Apostas</h2>';
        echo '<table class="todas-apostas">';
        $sql = "SELECT * FROM players";
        $result = mysqli_query($conn, $sql);
        $atual = 1;
        while ($row = mysqli_fetch_assoc($result)) {
            $i = 1;
            echo '<td>';
            echo '<table class="apostas">';
            echo '<tr>';
            echo '<th colspan="2">' . $row["nome"] . '</th>';
            echo '</tr>';
            while ($i <= 48) {
                echo '<tr>';
                echo '<td id="B' . $atual . 'J' . $i . '_1' . '">' . $row["J" . $i . "_1"] . '</td>';
                echo '<td id="B' . $atual . 'J' . $i . '_2' . '">' . $row["J" . $i . "_2"] . '</td>';
                echo '</tr>';
                $i++;
            }
            $atual++;
            echo '</table>';
            echo '</td>';
        }
        echo '</tr></table>';
        echo '</div>';
        ?>

        <?php
        //Pontos Fase de grupos TODO
        //fazer aqui tabela e resto em javascript
        //tenho J2_1 e 3J2_1(res1 do jogo 2 e a respetiva aposta do jogador 3)
        //P3J2->pontos ganhos nesse jogo
        //PT3J2->pontos acc ate esse jogo
        //A tabela abaixo tem o id = 'fg-' + nome do jogador
        //usar para javascript
        mysqli_data_seek($result, 0);
        $atual = 1;
        while ($row = mysqli_fetch_assoc($result)) {
            $i = 1;
            echo '<td>';
            echo '<table class="pontos-acc" id="fg-' . $row["nome"] . '">';
            echo '<tr>';
            echo '<th colspan="2">' . $row["nome"] . '</th>';
            echo '</tr>';
            while ($i <= 48) {
                echo '<tr>';
                echo '<td id="P' . $atual . 'J' . $i . '">-</td>';
                echo '<td id="PT' . $atual . 'J' . $i . '">-</td>';
                echo '</tr>';
                $i++;
            }
            $atual++;
            echo '</table>';
            echo '</td>';
        }
        echo '</tr></table>';
        echo '</div>';
        ?>

        <?php
        //Oitavos de final
        echo '<div class="apostas-e-acc">';
        echo '<div class="res-oitavos">';
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

        $jogo = 49;
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


            } else {
                if ($row["stat"] != 3 && $row["stat"] != 2)
                    $checkdt = false;
                if (is_null($row["res1"])) {
                    echo '<td id="' . 'J' . $jogo . '_1' . '">-</td>';
                } else {
                    echo '<td id="' . 'J' . $jogo . '_1' . '">' . $row["res1"] . '</td>';
                }
                if (is_null($row["res2"])) {
                    echo '<td id="' . 'J' . $jogo . '_2' . '">-</td>';
                } else {
                    echo '<td id="' . 'J' . $jogo . '_2' . '">' . $row["res2"] . '</td>';
                }
            }
            if (is_null($row["eq2"]))
                echo '<td>' . $row["ph2"] . '</td>';
            else
                echo '<td>' . $row["eq2"] . '</td>';
            echo '</tr>';
            $jogo++;


        }

        echo '</table>';
        echo '</div>';


        ?>
        <?php
        //Apostas Oitavos

        echo '<div class="apostas-oitavos">';
        echo '<h2 class="heading-apostas">Apostas</h2>';
        echo '<table class="todas-apostas">';
        $sql = "SELECT * FROM players";
        $result = mysqli_query($conn, $sql);
        $atual = 1;
        while ($row = mysqli_fetch_assoc($result)) {
            $i = 49;
            echo '<td>';
            echo '<table class="apostas">';
            echo '<tr>';
            echo '<th colspan="2">' . $row["nome"] . '</th>';
            echo '</tr>';
            while ($i <= 56) {
                echo '<tr>';
                echo '<td id="B' . $atual . 'J' . $i . '_1' . '">' . $row["J" . $i . "_1"] . '</td>';
                echo '<td id="B' . $atual . 'J' . $i . '_2' . '">' . $row["J" . $i . "_2"] . '</td>';
                echo '</tr>';
                $i++;
            }
            $atual++;
            echo '</table>';
            echo '</td>';
        }
        echo '</tr></table>';
        echo '</div>';
        ?>

        <?php
        //Pontos Fase de grupos TODO
        //A tabela abaixo tem o id = 'oit-' + nome do jogador
        //usar para javascript
        mysqli_data_seek($result, 0);
        $atual = 1;
        while ($row = mysqli_fetch_assoc($result)) {
            $i = 49;
            echo '<td>';
            echo '<table class="pontos-acc" id="oit-' . $row["nome"] . '">';
            echo '<tr>';
            echo '<th colspan="2">' . $row["nome"] . '</th>';
            echo '</tr>';
            while ($i <= 56) {
                echo '<tr>';
                echo '<td id="P' . $atual . 'J' . $i . '">-</td>';
                echo '<td id="PT' . $atual . 'J' . $i . '">-</td>';
                echo '</tr>';
                $i++;
            }
            $atual++;
            echo '</table>';
            echo '</td>';
        }
        echo '</tr></table>';
        echo '</div>';
        ?>

        <?php
        //Quartos de final
        echo '<div class="apostas-e-acc">';
        echo '<div class="res-quartos">';
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

        $jogo = 57;
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


            } else {
                if ($row["stat"] != 3 && $row["stat"] != 2)
                    $checkdt = false;
                if (is_null($row["res1"])) {
                    echo '<td id="' . 'J' . $jogo . '_1' . '">-</td>';
                } else {
                    echo '<td id="' . 'J' . $jogo . '_1' . '">' . $row["res1"] . '</td>';
                }
                if (is_null($row["res2"])) {
                    echo '<td id="' . 'J' . $jogo . '_2' . '">-</td>';
                } else {
                    echo '<td id="' . 'J' . $jogo . '_2' . '">' . $row["res2"] . '</td>';
                }
            }
            if (is_null($row["eq2"]))
                echo '<td>' . $row["ph2"] . '</td>';
            else
                echo '<td>' . $row["eq2"] . '</td>';
            echo '</tr>';


        }

        echo '</table>';
        echo '</div>';
        ?>
        <?php
        //Apostas quartos + pontos acc

        echo '<div class="apostas-quartos">';
        echo '<h2 class="heading-apostas">Apostas</h2>';
        echo '<table class="todas-apostas">';
        $sql = "SELECT * FROM players";
        $result = mysqli_query($conn, $sql);
        $atual = 1;
        while ($row = mysqli_fetch_assoc($result)) {
            $i = 57;
            echo '<td>';
            echo '<table class="apostas">';
            echo '<tr>';
            echo '<th colspan="2">' . $row["nome"] . '</th>';
            echo '</tr>';
            while ($i <= 60) {
                echo '<tr>';
                echo '<td id="B' . $atual . 'J' . $i . '_1' . '">' . $row["J" . $i . "_1"] . '</td>';
                echo '<td id="B' . $atual . 'J' . $i . '_2' . '">' . $row["J" . $i . "_2"] . '</td>';
                echo '</tr>';
                $i++;
            }
            $atual++;
            echo '</table>';
            echo '</td>';
        }
        echo '</tr></table>';
        echo '</div>';
        ?>

        <?php
        //A tabela abaixo tem o id = 'qua-' + nome do jogador
        //usar para javascript
        mysqli_data_seek($result, 0);
        $atual = 1;
        while ($row = mysqli_fetch_assoc($result)) {
            $i = 57;
            echo '<td>';
            echo '<table class="pontos-acc" id="qua-' . $row["nome"] . '">';
            echo '<tr>';
            echo '<th colspan="2">' . $row["nome"] . '</th>';
            echo '</tr>';
            while ($i <= 60) {
                echo '<tr>';
                echo '<td id="P' . $atual . 'J' . $i . '">-</td>';
                echo '<td id="PT' . $atual . 'J' . $i . '">-</td>';
                echo '</tr>';
                $i++;
            }
            $atual++;
            echo '</table>';
            echo '</td>';
        }
        echo '</tr></table>';
        echo '</div>';
        ?>

        <?php

        //Semi-Finais
        echo '<div class="apostas-e-acc">';
        echo '<div class="res-semi">';
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

        $jogo = 61;
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


            } else {
                if ($row["stat"] != 3 && $row["stat"] != 2)
                    $checkdt = false;
                if (is_null($row["res1"])) {
                    echo '<td id="' . 'J' . $jogo . '_1' . '">-</td>';
                } else {
                    echo '<td id="' . 'J' . $jogo . '_1' . '">' . $row["res1"] . '</td>';
                }
                if (is_null($row["res2"])) {
                    echo '<td id="' . 'J' . $jogo . '_2' . '">-</td>';
                } else {
                    echo '<td id="' . 'J' . $jogo . '_2' . '">' . $row["res2"] . '</td>';
                }
            }
            if (is_null($row["eq2"]))
                echo '<td>' . $row["ph2"] . '</td>';
            else
                echo '<td>' . $row["eq2"] . '</td>';
            echo '</tr>';
            $jogo++;
        }

        echo '</table>';
        echo '</div>';
        ?>

        <?php
        //Apostas semi + pontos acc

        echo '<div class="apostas-semi">';
        echo '<h2 class="heading-apostas">Apostas</h2>';
        echo '<table class="todas-apostas">';
        $sql = "SELECT * FROM players";
        $result = mysqli_query($conn, $sql);
        $atual = 1;
        while ($row = mysqli_fetch_assoc($result)) {
            $i = 61;
            echo '<td>';
            echo '<table class="apostas">';
            echo '<tr>';
            echo '<th colspan="2">' . $row["nome"] . '</th>';
            echo '</tr>';
            while ($i <= 62) {
                echo '<tr>';
                echo '<td id="B' . $atual . 'J' . $i . '_1' . '">' . $row["J" . $i . "_1"] . '</td>';
                echo '<td id="B' . $atual . 'J' . $i . '_2' . '">' . $row["J" . $i . "_2"] . '</td>';
                echo '</tr>';
                $i++;
            }
            $atual++;
            echo '</table>';
            echo '</td>';
        }
        echo '</tr></table>';
        echo '</div>';
        ?>

        <?php
        //A tabela abaixo tem o id = 'semi-' + nome do jogador
        //usar para javascript
        mysqli_data_seek($result, 0);
        $atual = 1;
        while ($row = mysqli_fetch_assoc($result)) {
            $i = 61;
            echo '<td>';
            echo '<table class="pontos-acc" id="semi-' . $row["nome"] . '">';
            echo '<tr>';
            echo '<th colspan="2">' . $row["nome"] . '</th>';
            echo '</tr>';
            while ($i <= 62) {
                echo '<tr>';
                echo '<td id="P' . $atual . 'J' . $i . '">-</td>';
                echo '<td id="PT' . $atual . 'J' . $i . '">-</td>';
                echo '</tr>';
                $i++;
            }
            $atual++;
            echo '</table>';
            echo '</td>';
        }
        echo '</tr></table>';
        echo '</div>';
        ?>


        <?php
        //Terceiro Lugar
        echo '<div class="apostas-e-acc">';
        echo '<div class="res-terceiro">';
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

        $jogo = 63;
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

            } else {
                if ($row["stat"] != 3 && $row["stat"] != 2)
                    $checkdt = false;
                if (is_null($row["res1"])) {
                    echo '<td id="' . 'J' . $jogo . '_1' . '">-</td>';
                } else {
                    echo '<td id="' . 'J' . $jogo . '_1' . '">' . $row["res1"] . '</td>';
                }
                if (is_null($row["res2"])) {
                    echo '<td id="' . 'J' . $jogo . '_2' . '">-</td>';
                } else {
                    echo '<td id="' . 'J' . $jogo . '_2' . '">' . $row["res2"] . '</td>';
                }
            }
            if (is_null($row["eq2"]))
                echo '<td>' . $row["ph2"] . '</td>';
            else
                echo '<td>' . $row["eq2"] . '</td>';
            echo '</tr>';


        }

        echo '</table>';
        echo '</div>';
        ?>

        <?php
        //Apostas terceiro + pontos acc

        echo '<div class="apostas-terceiro">';
        echo '<h2 class="heading-apostas">Apostas</h2>';
        echo '<table class="todas-apostas">';
        $sql = "SELECT * FROM players";
        $result = mysqli_query($conn, $sql);
        $atual = 1;
        while ($row = mysqli_fetch_assoc($result)) {
            $i = 63;
            echo '<td>';
            echo '<table class="apostas">';
            echo '<tr>';
            echo '<th colspan="2">' . $row["nome"] . '</th>';
            echo '</tr>';
            echo '<tr>';
            echo '<td id="B' . $atual . 'J' . $i . '_1' . '">' . $row["J" . $i . "_1"] . '</td>';
            echo '<td id="B' . $atual . 'J' . $i . '_2' . '">' . $row["J" . $i . "_2"] . '</td>';
            echo '</tr>';
            $atual++;
            echo '</table>';
            echo '</td>';
        }
        echo '</tr></table>';
        echo '</div>';
        ?>

        <?php
        //A tabela abaixo tem o id = 'terc-' + nome do jogador
        //usar para javascript
        mysqli_data_seek($result, 0);
        $atual = 1;
        while ($row = mysqli_fetch_assoc($result)) {
            $i = 63;
            echo '<td>';
            echo '<table class="pontos-acc" id="terc-' . $row["nome"] . '">';
            echo '<tr>';
            echo '<th colspan="2">' . $row["nome"] . '</th>';
            echo '</tr>';
            echo '<tr>';
            echo '<td id="P' . $atual . 'J' . $i . '">-</td>';
            echo '<td id="PT' . $atual . 'J' . $i . '">-</td>';
            echo '</tr>';
            $atual++;
            echo '</table>';
            echo '</td>';
        }
        echo '</tr></table>';
        echo '</div>';
        ?>

        <?php
        //Final
        echo '<div class="apostas-e-acc">';
        echo '<div class="res-final">';
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

        $jogo = 64;
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


            } else {
                if ($row["stat"] != 3 && $row["stat"] != 2)
                    $checkdt = false;
                if (is_null($row["res1"])) {
                    echo '<td id="' . 'J' . $jogo . '_1' . '">-</td>';
                } else {
                    echo '<td id="' . 'J' . $jogo . '_1' . '">' . $row["res1"] . '</td>';
                }
                if (is_null($row["res2"])) {
                    echo '<td id="' . 'J' . $jogo . '_2' . '">-</td>';
                } else {
                    echo '<td id="' . 'J' . $jogo . '_2' . '">' . $row["res2"] . '</td>';
                }
            }
            if (is_null($row["eq2"]))
                echo '<td>' . $row["ph2"] . '</td>';
            else
                echo '<td>' . $row["eq2"] . '</td>';
            echo '</tr>';

        }

        echo '</table>';
        echo '</div>';
        ?>


        <?php
        //Apostas final + pontos acc

        echo '<div class="apostas-final">';
        echo '<h2 class="heading-apostas">Apostas</h2>';
        echo '<table class="todas-apostas">';
        $sql = "SELECT * FROM players";
        $result = mysqli_query($conn, $sql);
        $atual = 1;
        while ($row = mysqli_fetch_assoc($result)) {
            $i = 64;
            echo '<td>';
            echo '<table class="apostas">';
            echo '<tr>';
            echo '<th colspan="2">' . $row["nome"] . '</th>';
            echo '</tr>';
            echo '<tr>';
            echo '<td id="B' . $atual . 'J' . $i . '_1' . '">' . $row["J" . $i . "_1"] . '</td>';
            echo '<td id="B' . $atual . 'J' . $i . '_2' . '">' . $row["J" . $i . "_2"] . '</td>';
            echo '</tr>';
            $atual++;
            echo '</table>';
            echo '</td>';
        }
        echo '</tr></table>';
        echo '</div>';
        ?>

        <?php
        //A tabela abaixo tem o id = 'final-' + nome do jogador
        //usar para javascript
        mysqli_data_seek($result, 0);
        $atual = 1;
        while ($row = mysqli_fetch_assoc($result)) {
            $i = 64;
            echo '<td>';
            echo '<table class="pontos-acc" id="final-' . $row["nome"] . '">';
            echo '<tr>';
            echo '<th colspan="2">' . $row["nome"] . '</th>';
            echo '</tr>';
            echo '<tr>';
            echo '<td id="P' . $atual . 'J' . $i . '">-</td>';
            echo '<td id="PT' . $atual . 'J' . $i . '">-</td>';
            echo '</tr>';
            $atual++;
            echo '</table>';
            echo '</td>';
        }
        echo '</tr></table>';
        echo '</div>';
        ?>
        <script src="history.js"></script>
        <?php
        include_once "../footer.php";
        ?>
