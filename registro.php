<?php
session_start();
if (isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro - Mi Spotify</title>
    <link rel="stylesheet" href="assets/css/estilo.css">
</head>
<body>
    <div class="contenedor-login">
        <h1>🎵 Crear Cuenta</h1>
        <?php if (isset($_GET['error'])) echo "<div class='error'>" . $_GET['error'] . "</div>"; ?>
        <?php if (isset($_GET['exito'])) echo "<div class='exito'>" . $_GET['exito'] . "</div>"; ?>
        <form action="procesar/registro.php" method="POST">
            <input type="text" name="nombre" placeholder="Nombre completo" required>
            <input type="email" name="email" placeholder="Correo electrónico" required>
            <input type="password" name="password" placeholder="Contraseña" required>
            <button type="submit">Registrarse</button>
        </form>
        <p>¿Ya tienes cuenta? <a href="index.php">Inicia Sesión</a></p>
    </div>
</body>
</html>