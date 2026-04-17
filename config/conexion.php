<?php
$host     = "localhost";
$usuario  = "root";
$password = "";
$base     = "mi_spotify";

$conexion = mysqli_connect($host, $usuario, $password, $base);

if (!$conexion) {
    die("Error de conexion: " . mysqli_connect_error());
}

mysqli_set_charset($conexion, "utf8");
?>