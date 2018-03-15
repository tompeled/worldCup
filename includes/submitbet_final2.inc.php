<?php
if (isset($_POST["submit"])) {
    session_start();
    if (isset($_SESSION["u_id"])) {
        include_once "dbhworld.inc.php";

        $i = 63;
        while ($i < 65) {
            $var1 = "J" . $i . "_1";
            $var2 = "J" . $i . "_2";
            if (!empty($_POST[$var1]) || $_POST[$var1]==0) {
                $sql = "UPDATE players SET J" . $i . "_1 =" . $_POST[$var1] . " WHERE players.id =" . $_SESSION["u_id"];
                mysqli_query($conn, $sql);

            }
            if (!empty($_POST[$var2]) || $_POST[$var2]==0) {
                $sql = "UPDATE players SET J" . $i . "_2 =" . $_POST[$var2] . " WHERE players.id =" . $_SESSION["u_id"];
                mysqli_query($conn, $sql);

            }
            $i++;
        }

        header("Location: ../bet.php?bet=success");
        exit();


    } else {
        header("Location: ../bet.php?bet=errorid");
        exit();
    }
} else {
    header("Location: ../index.php");
    exit();
}