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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Mi Spotify</title>
    <link rel="stylesheet" href="assets/css/estilo.css">
</head>
<body>
    <div class="contenedor-login">
        <div class="logo">🎵</div>
        <h1>Crear Cuenta</h1>
        <p class="subtitulo">Unete a Mi Spotify</p>

        <?php if (isset($_GET['error'])) echo "<div class='error'>" . $_GET['error'] . "</div>"; ?>
        <?php if (isset($_GET['exito'])) echo "<div class='exito'>" . $_GET['exito'] . "</div>"; ?>

        <form action="procesar/registro.php" method="POST">
            <input type="text" name="nombre" placeholder="Nombre completo" required>
            <input type="email" name="email" placeholder="Correo electronico" required>
            <input type="password" name="password" placeholder="Contrasena" required>
            <select name="rol">
                <option value="cliente">Cliente</option>
                <option value="admin">Administrador</option>
            </select>
            <button type="submit">CREAR CUENTA</button>
        </form>

        <div class="divisor">o</div>
        <div class="enlace-registro">
            Ya tienes cuenta? <a href="index.php">Inicia sesion</a>
        </div>
    </div>
</body>
</html>