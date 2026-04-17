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
    <title>Mi Spotify - Login</title>
    <link rel="stylesheet" href="assets/css/estilo.css">
</head>
<body>
    <div class="contenedor-login">
        <h1>🎵 Mi Spotify</h1>
        <form action="procesar/login.php" method="POST">
            <input type="email" name="email" placeholder="Correo electrónico" required>
            <input type="password" name="password" placeholder="Contraseña" required>
            <button type="submit">Iniciar Sesión</button>
        </form>
        <p>¿No tienes cuenta? <a href="registro.php">Regístrate</a></p>
    </div>
</body>
</html>