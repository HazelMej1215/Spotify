<?php
session_start();

if (isset($_SESSION['usuario'])) {
    if ($_SESSION['rol'] == 'admin') {
        header("Location: admin/panel.php");
    } else {
        header("Location: cliente/inicio.php");
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Spotify - Iniciar Sesión</title>
    <link rel="stylesheet" href="assets/css/estilo.css">
</head>
<body>
    <div class="contenedor-login">
        <div class="logo">🎵</div>
        <h1>Mi Spotify</h1>
        <p class="subtitulo">Inicia sesión para continuar</p>

        <?php if (isset($_GET['error'])) echo "<div class='error'>" . $_GET['error'] . "</div>"; ?>
        <?php if (isset($_GET['exito'])) echo "<div class='exito'>" . $_GET['exito'] . "</div>"; ?>

        <form action="procesar/login.php" method="POST">
            <input type="email" name="email" placeholder="Correo electrónico" required>
            <input type="password" name="password" placeholder="Contraseña" required>
            <button type="submit">INICIAR SESIÓN</button>
        </form>

        <div class="divisor">o</div>

        <div class="enlace-registro">
            ¿No tienes cuenta? <a href="registro.php">Regístrate gratis</a>
        </div>
    </div>
</body>
</html>