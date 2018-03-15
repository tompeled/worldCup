<?php
session_start();
if (!isset($_SESSION["u_id"])) {
    header("Location: ./index.php");
    exit();
}
?>
<?php
include_once "header.php"
?>

<section class="main-container">
    <div class="main-wrapper">
        <h2>Apostas</h2>
        <?php
        if (isset($_SESSION["u_id"])) {
            echo "You are logged in!";
            print_r($_SESSION);
        }
        ?>
    </div>


    <?php
    include_once "includes/dbhworld.inc.php";
    //FASE DE GRUPOS E 4 PRIMEIROS
    $id = $_SESSION["u_id"];


    $sql = "SELECT * FROM players WHERE players.id='$id';";
    $result2 = mysqli_query($conn, $sql);
    $result2Check = mysqli_num_rows($result2);
    if ($result2Check != 1) {
        header("Location: ../index.php?db=error");
        exit();
    }

    $player = mysqli_fetch_assoc($result2);


    $sql = "SELECT teams.nome, teams.id FROM teams";
    $result = mysqli_query($conn, $sql);
    if (date_create("now") < date_create("12-06-2018 19:00:00")){
        ?>
        <h2>Fase de Grupos e 4 Primeiros</h2>
        <div>
            <form class="bet-form" action="includes/submitbet.inc.php" method="POST">

                Primeiro Lugar
                <select name="primeiro">
                    <option value="" selected disabled hidden>Escolher Aqui</option>
                    <?php
                    mysqli_data_seek($result, 0);
                    while ($row = mysqli_fetch_assoc($result)) {
                        if ($player["final1"] == $row["id"])
                            echo '<option value="' . $row["id"] . '" selected="selected">' . $row["nome"] . '</option>';
                        else
                            echo '<option value="' . $row["id"] . '">' . $row["nome"] . '</option>';
                    }
                    ?>
                </select>
                Segundo Lugar
                <select name="segundo">
                    <option value="" selected disabled hidden>Escolher Aqui</option>
                    <?php
                    mysqli_data_seek($result, 0);
                    while ($row = mysqli_fetch_assoc($result)) {
                        if ($player["final2"] == $row["id"])
                            echo '<option value="' . $row["id"] . '" selected="selected">' . $row["nome"] . '</option>';
                        else
                            echo '<option value="' . $row["id"] . '">' . $row["nome"] . '</option>';
                    }
                    ?>
                </select>
                Terceiro Lugar
                <select name="terceiro">
                    <option value="" selected disabled hidden>Escolher Aqui</option>
                    <?php
                    mysqli_data_seek($result, 0);
                    while ($row = mysqli_fetch_assoc($result)) {
                        if ($player["final3"] == $row["id"])
                            echo '<option value="' . $row["id"] . '" selected="selected">' . $row["nome"] . '</option>';
                        else
                            echo '<option value="' . $row["id"] . '">' . $row["nome"] . '</option>';
                    }
                    ?>
                </select>
                Quarto Lugar
                <select name="quarto">
                    <option value="" selected disabled hidden>Escolher Aqui</option>
                    <?php
                    mysqli_data_seek($result, 0);
                    while ($row = mysqli_fetch_assoc($result)) {
                        if ($player["final4"] == $row["id"])
                            echo '<option value="' . $row["id"] . '" selected="selected">' . $row["nome"] . '</option>';
                        else
                            echo '<option value="' . $row["id"] . '">' . $row["nome"] . '</option>';
                    }
                    ?>
                </select>
        </div>

        <table class="jogos">
            <tr>
                <th>Dia</th>
                <th>Hora</th>
                <th></th>
                <th class="bet"></th>
                <th class="bet"></th>
                <th></th>
            </tr>
            <?php


            $sql = 'SELECT games.id, games.dia_hora, teams.nome AS eq1, t.nome AS eq2, games.res1, games.res2 FROM games JOIN teams ON games.equipa1=teams.id JOIN teams t ON games.equipa2=t.id;';

            $result = mysqli_query($conn, $sql);


            while ($row = mysqli_fetch_assoc($result)) {
                echo '<tr>';
                $date = date_create($row["dia_hora"]);
                echo '<td>' . date_format($date, 'j/n') . '</td>';
                echo '<td>' . date_format($date, 'G:i') . '</td>';
                echo '<td>' . $row["eq1"] . '</td>';

                $name1 = "J" . $row["id"] . "_1";
                $name2 = "J" . $row["id"] . "_2";

                if (is_null($player["J" . $row["id"] . "_1"])) {
                    echo "<td><input type='number' name='$name1'></td>";
                } else {
                    $ph = $player["J" . $row["id"] . "_1"];
                    echo "<td><input type='number' name='$name1' placeholder='$ph'></td>";
                }
                if (is_null($player["J" . $row["id"] . "_2"])) {
                    echo "<td><input type='number' name='$name2'></td>";
                } else {
                    $ph = $player["J" . $row["id"] . "_2"];
                    echo "<td><input type='number' name='$name2' placeholder='$ph'></td>";
                }
                echo '<td>' . $row["eq2"] . '</td>';
                echo '</tr>';

            }

            ?>
        </table>

        <button type="submit" name="submit">Submeter Apostas</button>


        <?php
    } // OITAVOS DE FINAL
    elseif (date_create("now") < date_create("29-06-2018 19:00:00")) {
    ?>
    <h2>Oitavos de Final</h2>
    <form class="bet-form" action="includes/submitbet_sixteen.inc.php" method="POST">

        <table class="jogos">
            <tr>
                <th>Dia</th>
                <th>Hora</th>
                <th></th>
                <th class="bet"></th>
                <th class="bet"></th>
                <th></th>
            </tr>
            <?php

            $id = $_SESSION["u_id"];


            $sql = "SELECT knockout.id, knockout.dia_hora, teams.nome AS eq1, t.nome AS eq2, knockout.res1, knockout.res2, knockout.ph1, knockout.ph2 FROM knockout LEFT JOIN teams ON knockout.equipa1=teams.id LEFT JOIN teams t ON knockout.equipa2=t.id WHERE knockout.id<=8";

            $result = mysqli_query($conn, $sql);

            $sql = "SELECT * FROM players WHERE players.id='$id';";
            $result2 = mysqli_query($conn, $sql);
            $result2Check = mysqli_num_rows($result2);
            if ($result2Check != 1) {
                header("Location: ../index.php?db=error");
                exit();
            }

            $player = mysqli_fetch_assoc($result2);

            while ($row = mysqli_fetch_assoc($result)) {
                echo '<tr>';
                $date = date_create($row["dia_hora"]);
                echo '<td>' . date_format($date, 'j/n') . '</td>';
                echo '<td>' . date_format($date, 'G:i') . '</td>';
                if (is_null($row["eq1"]))
                    echo '<td>' . $row["ph1"] . '</td>';
                else
                    echo '<td>' . $row["eq1"] . '</td>';

                $name1 = "J" . ($row["id"] + 48) . "_1";
                $name2 = "J" . ($row["id"] + 48) . "_2";

                if (is_null($player["J" . ($row["id"] + 48) . "_1"])) {
                    echo "<td><input type='number' name='$name1'></td>";
                } else {
                    $ph = $player["J" . ($row["id"] + 48) . "_1"];
                    echo "<td><input type='number' name='$name1' placeholder='$ph'></td>";
                }
                if (is_null($player["J" . ($row["id"] + 48) . "_2"])) {
                    echo "<td><input type='number' name='$name2'></td>";
                } else {
                    $ph = $player["J" . ($row["id"] + 48) . "_2"];
                    echo "<td><input type='number' name='$name2' placeholder='$ph'></td>";
                }
                if (is_null($row["eq2"]))
                    echo '<td>' . $row["ph2"] . '</td>';
                else
                    echo '<td>' . $row["eq2"] . '</td>';
                echo '</tr>';

            }

            ?>
        </table>

        <button type="submit" name="submit">Submeter Apostas Dos Oitavos</button>
        <?php
        } elseif (date_create("now") < date_create("05-07-2018 19:00:00")) {
        ?>
        <h2>Quartos de Final</h2>
        <form class="bet-form" action="includes/submitbet_eight.inc.php" method="POST">

            <table class="jogos">
                <tr>
                    <th>Dia</th>
                    <th>Hora</th>
                    <th></th>
                    <th class="bet"></th>
                    <th class="bet"></th>
                    <th></th>
                </tr>
                <?php

                $id = $_SESSION["u_id"];


                $sql = "SELECT knockout.id, knockout.dia_hora, teams.nome AS eq1, t.nome AS eq2, knockout.res1, knockout.res2, knockout.ph1, knockout.ph2 FROM knockout LEFT JOIN teams ON knockout.equipa1=teams.id LEFT JOIN teams t ON knockout.equipa2=t.id WHERE 8<knockout.id AND knockout.id<=12";

                $result = mysqli_query($conn, $sql);

                $sql = "SELECT * FROM players WHERE players.id='$id';";
                $result2 = mysqli_query($conn, $sql);
                $result2Check = mysqli_num_rows($result2);
                if ($result2Check != 1) {
                    header("Location: ../index.php?db=error");
                    exit();
                }

                $player = mysqli_fetch_assoc($result2);

                while ($row = mysqli_fetch_assoc($result)) {
                    echo '<tr>';
                    $date = date_create($row["dia_hora"]);
                    echo '<td>' . date_format($date, 'j/n') . '</td>';
                    echo '<td>' . date_format($date, 'G:i') . '</td>';
                    if (is_null($row["eq1"]))
                        echo '<td>' . $row["ph1"] . '</td>';
                    else
                        echo '<td>' . $row["eq1"] . '</td>';

                    $name1 = "J" . ($row["id"] + 48) . "_1";
                    $name2 = "J" . ($row["id"] + 48) . "_2";

                    if (is_null($player["J" . ($row["id"] + 48) . "_1"])) {
                        echo "<td><input type='number' name='$name1'></td>";
                    } else {
                        $ph = $player["J" . ($row["id"] + 48) . "_1"];
                        echo "<td><input type='number' name='$name1' placeholder='$ph'></td>";
                    }
                    if (is_null($player["J" . ($row["id"] + 48) . "_2"])) {
                        echo "<td><input type='number' name='$name2'></td>";
                    } else {
                        $ph = $player["J" . ($row["id"] + 48) . "_2"];
                        echo "<td><input type='number' name='$name2' placeholder='$ph'></td>";
                    }
                    if (is_null($row["eq2"]))
                        echo '<td>' . $row["ph2"] . '</td>';
                    else
                        echo '<td>' . $row["eq2"] . '</td>';
                    echo '</tr>';

                }

                ?>
            </table>

            <button type="submit" name="submit">Submeter Apostas Dos Quartos</button>
            <?php } elseif (date_create("now") < date_create("09-07-2018 19:00:00")) {
            ?>
            <h2>Semi-Finais</h2>
            <form class="bet-form" action="includes/submitbet_semi.inc.php" method="POST">

                <table class="jogos">
                    <tr>
                        <th>Dia</th>
                        <th>Hora</th>
                        <th></th>
                        <th class="bet"></th>
                        <th class="bet"></th>
                        <th></th>
                    </tr>
                    <?php

                    $id = $_SESSION["u_id"];


                    $sql = "SELECT knockout.id, knockout.dia_hora, teams.nome AS eq1, t.nome AS eq2, knockout.res1, knockout.res2, knockout.ph1, knockout.ph2 FROM knockout LEFT JOIN teams ON knockout.equipa1=teams.id LEFT JOIN teams t ON knockout.equipa2=t.id WHERE 12<knockout.id AND knockout.id<=14";

                    $result = mysqli_query($conn, $sql);

                    $sql = "SELECT * FROM players WHERE players.id='$id';";
                    $result2 = mysqli_query($conn, $sql);
                    $result2Check = mysqli_num_rows($result2);
                    if ($result2Check != 1) {
                        header("Location: ../index.php?db=error");
                        exit();
                    }

                    $player = mysqli_fetch_assoc($result2);

                    while ($row = mysqli_fetch_assoc($result)) {
                        echo '<tr>';
                        $date = date_create($row["dia_hora"]);
                        echo '<td>' . date_format($date, 'j/n') . '</td>';
                        echo '<td>' . date_format($date, 'G:i') . '</td>';
                        if (is_null($row["eq1"]))
                            echo '<td>' . $row["ph1"] . '</td>';
                        else
                            echo '<td>' . $row["eq1"] . '</td>';

                        $name1 = "J" . ($row["id"] + 48) . "_1";
                        $name2 = "J" . ($row["id"] + 48) . "_2";

                        if (is_null($player["J" . ($row["id"] + 48) . "_1"])) {
                            echo "<td><input type='number' name='$name1'></td>";
                        } else {
                            $ph = $player["J" . ($row["id"] + 48) . "_1"];
                            echo "<td><input type='number' name='$name1' placeholder='$ph'></td>";
                        }
                        if (is_null($player["J" . ($row["id"] + 48) . "_2"])) {
                            echo "<td><input type='number' name='$name2'></td>";
                        } else {
                            $ph = $player["J" . ($row["id"] + 48) . "_2"];
                            echo "<td><input type='number' name='$name2' placeholder='$ph'></td>";
                        }
                        if (is_null($row["eq2"]))
                            echo '<td>' . $row["ph2"] . '</td>';
                        else
                            echo '<td>' . $row["eq2"] . '</td>';
                        echo '</tr>';

                    }

                    ?>
                </table>

                <button type="submit" name="submit">Submeter Apostas Das Semi</button>
                <?php } elseif (date_create("now") < date_create("13-07-2018 19:00:00")) {
                ?>
                <h2>Terceiro Lugar</h2>
                <form class="bet-form" action="includes/submitbet_final2.inc.php" method="POST">

                    <table class="jogos">
                        <tr>
                            <th>Dia</th>
                            <th>Hora</th>
                            <th></th>
                            <th class="bet"></th>
                            <th class="bet"></th>
                            <th></th>
                        </tr>
                        <?php

                        $id = $_SESSION["u_id"];


                        $sql = "SELECT knockout.id, knockout.dia_hora, teams.nome AS eq1, t.nome AS eq2, knockout.res1, knockout.res2, knockout.ph1, knockout.ph2 FROM knockout LEFT JOIN teams ON knockout.equipa1=teams.id LEFT JOIN teams t ON knockout.equipa2=t.id WHERE 14<knockout.id AND knockout.id<=16";

                        $result = mysqli_query($conn, $sql);

                        $sql = "SELECT * FROM players WHERE players.id='$id';";
                        $result2 = mysqli_query($conn, $sql);
                        $result2Check = mysqli_num_rows($result2);
                        if ($result2Check != 1) {
                            header("Location: ../index.php?db=error");
                            exit();
                        }

                        $player = mysqli_fetch_assoc($result2);
                        $i = 1;
                        while ($row = mysqli_fetch_assoc($result)) {
                        if ($i != 1){ ?>
                    </table>
                    <h2>Final</h2>
                    <table class="jogos">
                        <tr>
                            <th>Dia</th>
                            <th>Hora</th>
                            <th></th>
                            <th class="bet"></th>
                            <th class="bet"></th>
                            <th></th>
                        </tr>
                        <?php
                        }
                        echo '<tr>';
                        $date = date_create($row["dia_hora"]);
                        echo '<td>' . date_format($date, 'j/n') . '</td>';
                        echo '<td>' . date_format($date, 'G:i') . '</td>';
                        if (is_null($row["eq1"]))
                            echo '<td>' . $row["ph1"] . '</td>';
                        else
                            echo '<td>' . $row["eq1"] . '</td>';

                        $name1 = "J" . ($row["id"] + 48) . "_1";
                        $name2 = "J" . ($row["id"] + 48) . "_2";

                        if (is_null($player["J" . ($row["id"] + 48) . "_1"])) {
                            echo "<td><input type='number' name='$name1'></td>";
                        } else {
                            $ph = $player["J" . ($row["id"] + 48) . "_1"];
                            echo "<td><input type='number' name='$name1' placeholder='$ph'></td>";
                        }
                        if (is_null($player["J" . ($row["id"] + 48) . "_2"])) {
                            echo "<td><input type='number' name='$name2'></td>";
                        } else {
                            $ph = $player["J" . ($row["id"] + 48) . "_2"];
                            echo "<td><input type='number' name='$name2' placeholder='$ph'></td>";
                        }
                        if (is_null($row["eq2"]))
                            echo '<td>' . $row["ph2"] . '</td>';
                        else
                            echo '<td>' . $row["eq2"] . '</td>';
                        echo '</tr>';
                        $i++;
                        }

                        ?>
                    </table>

                    <button type="submit" name="submit">Submeter Apostas Do Terceiro e Final</button>
                    <?php }
                    ?>
                </form>
</section>


<?php
include_once "footer.php"
?>

