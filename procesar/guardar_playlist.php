<?php
session_start();
require_once '../config/conexion.php';

if (!isset($_SESSION['usuario']) || $_SESSION['rol'] != 'cliente') {
    header("Location: ../index.php");
    exit();
}

$id_playlist = $_POST['id_playlist'];
$canciones   = isset($_POST['canciones']) ? $_POST['canciones'] : [];

// Limpiar playlist anterior
mysqli_query($conexion, "DELETE FROM playlist_canciones WHERE id_playlist = '$id_playlist'");

// Guardar nuevas canciones
foreach ($canciones as $id_cancion) {
    $id_cancion = (int)$id_cancion;
    mysqli_query($conexion, "INSERT INTO playlist_canciones (id_playlist, id_cancion) 
                             VALUES ('$id_playlist', '$id_cancion')");
}

header("Location: ../cliente/playlist.php?exito=Playlist actualizada correctamente");
exit();
?>