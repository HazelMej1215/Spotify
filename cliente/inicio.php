<?php
session_start();
require_once '../config/conexion.php';

if (!isset($_SESSION['usuario']) || $_SESSION['rol'] != 'cliente') {
    header("Location: ../index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Spotify - Inicio</title>
    <link rel="stylesheet" href="../assets/css/estilo.css">
    <link rel="stylesheet" href="../assets/css/panel.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-logo">🎵 Mi Spotify</div>
        <div class="nav-usuario">
            👤 <?php echo $_SESSION['usuario']; ?>
            <a href="../procesar/logout.php" class="btn-logout">Cerrar Sesión</a>
        </div>
    </nav>

    <div class="contenedor-panel">
        <h2>¡Bienvenido, <?php echo $_SESSION['usuario']; ?>! 🎶</h2>
        <p>Aquí podrás armar tu playlist favorita.</p>
    </div>
</body>
</html>