<?php
session_start();
require_once '../config/conexion.php';

if (!isset($_SESSION['usuario']) || $_SESSION['rol'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

$nombre            = mysqli_real_escape_string($conexion, $_POST['nombre']);
$id_autor          = $_POST['id_autor'];
$id_genero         = $_POST['id_genero'];
$id_album          = $_POST['id_album'] != '' ? $_POST['id_album'] : 'NULL';
$fecha_lanzamiento = $_POST['fecha_lanzamiento'];
$duracion          = mysqli_real_escape_string($conexion, $_POST['duracion']);
$imagen            = '';
$archivo_mp3       = '';

if ($_FILES['imagen']['name'] != '') {
    $nombre_img = time() . '_' . $_FILES['imagen']['name'];
    move_uploaded_file($_FILES['imagen']['tmp_name'], '../assets/img/' . $nombre_img);
    $imagen = $nombre_img;
}

if ($_FILES['archivo_mp3']['name'] != '') {
    $nombre_mp3 = time() . '_' . $_FILES['archivo_mp3']['name'];
    move_uploaded_file($_FILES['archivo_mp3']['tmp_name'], '../assets/mp3/' . $nombre_mp3);
    $archivo_mp3 = $nombre_mp3;
}

$sql = "INSERT INTO canciones (nombre, id_autor, id_genero, id_album, fecha_lanzamiento, duracion, imagen, archivo_mp3)
        VALUES ('$nombre', '$id_autor', '$id_genero', " . ($id_album == 'NULL' ? 'NULL' : "'$id_album'") . ", '$fecha_lanzamiento', '$duracion', '$imagen', '$archivo_mp3')";

if (mysqli_query($conexion, $sql)) {
    header("Location: ../admin/canciones.php?exito=Cancion guardada correctamente");
} else {
    header("Location: ../admin/canciones.php?error=Error al guardar");
}
exit();
?>