<?php
session_start();
require_once '../config/conexion.php';

if (!isset($_SESSION['usuario']) || $_SESSION['rol'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

$nombre = mysqli_real_escape_string($conexion, $_POST['nombre']);
$imagen = '';

if ($_FILES['imagen']['name'] != '') {
    $nombre_img = time() . '_' . $_FILES['imagen']['name'];
    move_uploaded_file($_FILES['imagen']['tmp_name'], '../assets/img/' . $nombre_img);
    $imagen = $nombre_img;
}

$sql = "INSERT INTO autores (nombre, imagen) VALUES ('$nombre', '$imagen')";

if (mysqli_query($conexion, $sql)) {
    header("Location: ../admin/autores.php?exito=Autor guardado correctamente");
} else {
    header("Location: ../admin/autores.php?error=Error al guardar");
}
exit();
?>