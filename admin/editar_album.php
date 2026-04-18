<?php
session_start();
require_once '../config/conexion.php';

if (!isset($_SESSION['usuario']) || $_SESSION['rol'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

$id    = (int)$_GET['id'];
$album = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT * FROM albumes WHERE id = '$id'"));

if (!$album) {
    header("Location: albumes.php?error=Album no encontrado");
    exit();
}

$autores = mysqli_query($conexion, "SELECT * FROM autores ORDER BY nombre");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Album - Mi Spotify</title>
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
            <a href="albumes.php">Albums</a>
            <a href="canciones.php">Canciones</a>
            <a href="usuarios.php">Gestion</a>
        </div>
        <div class="nav-usuario">
            <?php echo $_SESSION['usuario']; ?>
            <a href="../procesar/logout.php" class="btn-logout">Cerrar Sesion</a>
        </div>
    </nav>

    <div class="contenedor-panel">
        <h2>Editar Album</h2>

        <?php if (isset($_GET['error'])) echo "<div class='error'>" . $_GET['error'] . "</div>"; ?>

        <div class="contenedor-form">
            <h3>Modificar datos del Album</h3>

            <?php if ($album['imagen']): ?>
            <div style="margin-bottom:20px">
                <img src="../assets/img/<?php echo $album['imagen']; ?>" 
                     style="width:120px;height:120px;border-radius:10px;object-fit:cover">
            </div>
            <?php endif; ?>

            <form action="../procesar/actualizar_album.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?php echo $album['id']; ?>">

                <div class="campo">
                    <label>Nombre del Album</label>
                    <input type="text" name="nombre" value="<?php echo $album['nombre']; ?>" required>
                </div>
                <div class="campo">
                    <label>Autor</label>
                    <select name="id_autor" required>
                        <?php while ($autor = mysqli_fetch_assoc($autores)): ?>
                        <option value="<?php echo $autor['id']; ?>"
                            <?php echo $autor['id'] == $album['id_autor'] ? 'selected' : ''; ?>>
                            <?php echo $autor['nombre']; ?>
                        </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="campo">
                    <label>Año de Lanzamiento</label>
                    <input type="number" name="anio" value="<?php echo $album['anio']; ?>" min="1900" max="2030">
                </div>
                <div class="campo">
                    <label>Nueva Imagen</label>
                    <input type="file" name="imagen" accept="image/*">
                    <small style="color:#aaa">Dejar vacio para mantener la imagen actual</small>
                </div>
                <div style="display:flex; gap:12px; margin-top:16px">
                    <button type="submit" class="btn-guardar">Guardar Cambios</button>
                    <a href="albumes.php" style="padding:12px 24px;border-radius:50px;border:1px solid #555;color:#aaa;text-decoration:none;font-size:14px">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>