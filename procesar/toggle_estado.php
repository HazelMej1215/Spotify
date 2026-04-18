<?php
session_start();
require_once '../config/conexion.php';

if (!isset($_SESSION['usuario']) || $_SESSION['rol'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

$tabla    = $_GET['tabla'];
$id       = (int)$_GET['id'];
$estado   = (int)$_GET['estado'];
$redirect = $_GET['redirect'];

// Tablas permitidas
$tablas_permitidas = ['usuarios', 'canciones', 'albumes', 'autores'];
if (!in_array($tabla, $tablas_permitidas)) {
    header("Location: ../" . $redirect . "?error=Tabla no permitida");
    exit();
}

$nuevo_estado = $estado == 1 ? 0 : 1;
$accion = $nuevo_estado == 1 ? 'habilitado' : 'inhabilitado';

mysqli_query($conexion, "UPDATE $tabla SET activo = $nuevo_estado WHERE id = $id");

header("Location: ../" . $redirect . "?exito=Registro " . $accion . " correctamente");
exit();
?>