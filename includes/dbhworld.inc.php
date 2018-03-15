<?php

$user = 'root';
$password = 'root';
$db = 'world_championship';
$host = 'localhost';
$port = 8889;

$conn = mysqli_connect(
    "$host:$port",
    $user,
    $password,
    $db
);