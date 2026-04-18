<?php
session_start();
require_once '../config/conexion.php';

if (!isset($_SESSION['usuario']) || $_SESSION['rol'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

$total_canciones = mysqli_fetch_row(mysqli_query($conexion, "SELECT COUNT(*) FROM canciones"))[0];
$total_autores   = mysqli_fetch_row(mysqli_query($conexion, "SELECT COUNT(*) FROM autores"))[0];
$total_albumes   = mysqli_fetch_row(mysqli_query($conexion, "SELECT COUNT(*) FROM albumes"))[0];
$total_usuarios  = mysqli_fetch_row(mysqli_query($conexion, "SELECT COUNT(*) FROM usuarios WHERE rol='cliente'"))[0];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Admin - Mi Spotify</title>
    <link rel="stylesheet" href="../assets/css/estilo.css">
    <link rel="stylesheet" href="../assets/css/panel.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-logo">Mi Spotify</div>
        <div class="nav-links">
            <a href="panel.php">Dashboard</a>
            <a href="autores.php">Autores</a>
            <a href="albumes.php">Álbumes</a>
            <a href="canciones.php">Canciones</a>
            <a href="usuarios.php">Gestion</a>
        </div>
        <div class="nav-usuario">
            <?php echo $_SESSION['usuario']; ?>
            <a href="../procesar/logout.php" class="btn-logout">Cerrar Sesión</a>
        </div>
    </nav>

    <div class="contenedor-panel">
        <h2>Dashboard Admin </h2>
        <p>Bienvenido al panel de administración</p>

        <div class="tarjetas">
            <div class="tarjeta">
                <span class="tarjeta-icono"></span>
                <span class="tarjeta-numero"><?php echo $total_canciones; ?></span>
                <span class="tarjeta-label">Canciones</span>
            </div>
            <div class="tarjeta">
                <span class="tarjeta-icono"></span>
                <span class="tarjeta-numero"><?php echo $total_autores; ?></span>
                <span class="tarjeta-label">Autores</span>
            </div>
            <div class="tarjeta">
                <span class="tarjeta-icono"></span>
                <span class="tarjeta-numero"><?php echo $total_albumes; ?></span>
                <span class="tarjeta-label">Álbumes</span>
            </div>
            <div class="tarjeta">
                <span class="tarjeta-icono"></span>
                <span class="tarjeta-numero"><?php echo $total_usuarios; ?></span>
                <span class="tarjeta-label">Clientes</span>
            </div>
        </div>

        <div class="accesos-rapidos">
            <a href="autores.php" class="btn-accion">Nuevo Autor</a>
            <a href="albumes.php" class="btn-accion"> Nuevo Álbum</a>
            <a href="canciones.php" class="btn-accion"> Nueva Canción</a>
        </div>
    </div>
</body>
</html>