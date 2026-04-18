<?php
session_start();
require_once '../config/conexion.php';

if (!isset($_SESSION['usuario']) || $_SESSION['rol'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

$id                = (int)$_POST['id'];
$nombre            = mysqli_real_escape_string($conexion, $_POST['nombre']);
$id_autor          = (int)$_POST['id_autor'];
$id_genero         = (int)$_POST['id_genero'];
$id_album          = $_POST['id_album'] != '' ? (int)$_POST['id_album'] : null;
$fecha_lanzamiento = mysqli_real_escape_string($conexion, $_POST['fecha_lanzamiento']);
$duracion          = mysqli_real_escape_string($conexion, $_POST['duracion']);

$cancion_actual = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT * FROM canciones WHERE id = '$id'"));

$imagen      = $cancion_actual['imagen'];
$archivo_mp3 = $cancion_actual['archivo_mp3'];

if ($_FILES['imagen']['name'] != '') {
    $nombre_limpio = preg_replace("/[^a-zA-Z0-9._-]/", "_", $_FILES['imagen']['name']);
    $nombre_img    = time() . '_' . $nombre_limpio;
    move_uploaded_file($_FILES['imagen']['tmp_name'], '../assets/img/' . $nombre_img);
    $imagen = mysqli_real_escape_string($conexion, $nombre_img);
}

if ($_FILES['archivo_mp3']['name'] != '') {
    $nombre_limpio_mp3 = preg_replace("/[^a-zA-Z0-9._-]/", "_", $_FILES['archivo_mp3']['name']);
    $nombre_mp3        = time() . '_' . $nombre_limpio_mp3;
    move_uploaded_file($_FILES['archivo_mp3']['tmp_name'], '../assets/mp3/' . $nombre_mp3);
    $archivo_mp3 = mysqli_real_escape_string($conexion, $nombre_mp3);
}

$album_sql = $id_album ? "'$id_album'" : "NULL";

$sql = "UPDATE canciones SET 
        nombre = '$nombre',
        id_autor = '$id_autor',
        id_genero = '$id_genero',
        id_album = $album_sql,
        fecha_lanzamiento = '$fecha_lanzamiento',
        duracion = '$duracion',
        imagen = '$imagen',
        archivo_mp3 = '$archivo_mp3'
        WHERE id = '$id'";

if (mysqli_query($conexion, $sql)) {
    header("Location: ../admin/canciones.php?exito=Cancion actualizada correctamente");
} else {
    header("Location: ../admin/editar_cancion.php?id=$id&error=" . mysqli_error($conexion));
}
exit();
?>