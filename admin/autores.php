<?php
session_start();
require_once '../config/conexion.php';

if (!isset($_SESSION['usuario']) || $_SESSION['rol'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

$autores = mysqli_query($conexion, "SELECT * FROM autores ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Autores - Mi Spotify</title>
    <link rel="stylesheet" href="../assets/css/estilo.css">
    <link rel="stylesheet" href="../assets/css/panel.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-logo">🎵 Mi Spotify</div>
        <div class="nav-links">
            <a href="panel.php">Dashboard</a>
            <a href="autores.php">Autores</a>
            <a href="albumes.php">Álbumes</a>
            <a href="canciones.php">Canciones</a>
        </div>
        <div class="nav-usuario">
            👤 <?php echo $_SESSION['usuario']; ?>
            <a href="../procesar/logout.php" class="btn-logout">Cerrar Sesión</a>
        </div>
    </nav>

    <div class="contenedor-panel">
        <h2>🎤 Autores</h2>

        <?php if (isset($_GET['exito'])) echo "<div class='exito'>" . $_GET['exito'] . "</div>"; ?>
        <?php if (isset($_GET['error'])) echo "<div class='error'>" . $_GET['error'] . "</div>"; ?>

        <div class="contenedor-form">
            <h3>➕ Nuevo Autor</h3>
            <form action="../procesar/guardar_autor.php" method="POST" enctype="multipart/form-data">
                <div class="campo">
                    <label>Nombre del Autor</label>
                    <input type="text" name="nombre" placeholder="Ej: Bad Bunny" required>
                </div>
                <div class="campo">
                    <label>Imagen del Autor</label>
                    <input type="file" name="imagen" accept="image/*">
                </div>
                <button type="submit" class="btn-guardar">Guardar Autor</button>
            </form>
        </div>

        <table class="tabla-admin">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Imagen</th>
                    <th>Nombre</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($autor = mysqli_fetch_assoc($autores)): ?>
                <tr>
                    <td><?php echo $autor['id']; ?></td>
                    <td>
                        <?php if ($autor['imagen']): ?>
                            <img src="../assets/img/<?php echo $autor['imagen']; ?>" alt="autor">
                        <?php else: ?>
                            🎤
                        <?php endif; ?>
                    </td>
                    <td><?php echo $autor['nombre']; ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>