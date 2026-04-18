<?php
session_start();
require_once '../config/conexion.php';

if (!isset($_SESSION['usuario']) || $_SESSION['rol'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

$id     = (int)$_POST['id'];
$nombre = mysqli_real_escape_string($conexion, $_POST['nombre']);

$autor_actual = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT * FROM autores WHERE id = '$id'"));
$imagen = $autor_actual['imagen'];

if ($_FILES['imagen']['name'] != '') {
    $nombre_limpio = preg_replace("/[^a-zA-Z0-9._-]/", "_", $_FILES['imagen']['name']);
    $nombre_img    = time() . '_' . $nombre_limpio;
    move_uploaded_file($_FILES['imagen']['tmp_name'], '../assets/img/' . $nombre_img);
    $imagen = mysqli_real_escape_string($conexion, $nombre_img);
}

$sql = "UPDATE autores SET nombre = '$nombre', imagen = '$imagen' WHERE id = '$id'";

if (mysqli_query($conexion, $sql)) {
    header("Location: ../admin/autores.php?exito=Artista actualizado correctamente");
} else {
    header("Location: ../admin/editar_autor.php?id=$id&error=" . mysqli_error($conexion));
}
exit();
?>