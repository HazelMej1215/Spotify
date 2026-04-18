<?php
session_start();
require_once '../config/conexion.php';

if (!isset($_SESSION['usuario']) || $_SESSION['rol'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

$id       = (int)$_POST['id'];
$nombre   = mysqli_real_escape_string($conexion, $_POST['nombre']);
$id_autor = (int)$_POST['id_autor'];
$anio     = (int)$_POST['anio'];

$album_actual = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT * FROM albumes WHERE id = '$id'"));
$imagen = $album_actual['imagen'];

if ($_FILES['imagen']['name'] != '') {
    $nombre_limpio = preg_replace("/[^a-zA-Z0-9._-]/", "_", $_FILES['imagen']['name']);
    $nombre_img    = time() . '_' . $nombre_limpio;
    move_uploaded_file($_FILES['imagen']['tmp_name'], '../assets/img/' . $nombre_img);
    $imagen = mysqli_real_escape_string($conexion, $nombre_img);
}

$sql = "UPDATE albumes SET 
        nombre = '$nombre',
        id_autor = '$id_autor',
        anio = '$anio',
        imagen = '$imagen'
        WHERE id = '$id'";

if (mysqli_query($conexion, $sql)) {
    header("Location: ../admin/albumes.php?exito=Album actualizado correctamente");
} else {
    header("Location: ../admin/editar_album.php?id=$id&error=" . mysqli_error($conexion));
}
exit();
?>