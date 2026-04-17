<?php
session_start();
require_once '../config/conexion.php';

if (!isset($_SESSION['usuario']) || $_SESSION['rol'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

$autores  = mysqli_query($conexion, "SELECT * FROM autores ORDER BY nombre");
$generos  = mysqli_query($conexion, "SELECT * FROM generos ORDER BY nombre");
$albumes  = mysqli_query($conexion, "SELECT * FROM albumes ORDER BY nombre");
$canciones = mysqli_query($conexion, "SELECT c.*, au.nombre AS autor, g.nombre AS genero, al.nombre AS album 
             FROM canciones c
             LEFT JOIN autores au ON c.id_autor = au.id
             LEFT JOIN generos g ON c.id_genero = g.id
             LEFT JOIN albumes al ON c.id_album = al.id
             ORDER BY c.id DESC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Canciones - Mi Spotify</title>
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
        </div>
        <div class="nav-usuario">
            <?php echo $_SESSION['usuario']; ?>
            <a href="../procesar/logout.php" class="btn-logout">Cerrar Sesion</a>
        </div>
    </nav>

    <div class="contenedor-panel">
        <h2>Canciones</h2>

        <?php if (isset($_GET['exito'])) echo "<div class='exito'>" . $_GET['exito'] . "</div>"; ?>
        <?php if (isset($_GET['error'])) echo "<div class='error'>" . $_GET['error'] . "</div>"; ?>

        <div class="contenedor-form">
            <h3>Nueva Cancion</h3>
            <form action="../procesar/guardar_cancion.php" method="POST" enctype="multipart/form-data">
                <div class="campo">
                    <label>Nombre de la Cancion</label>
                    <input type="text" name="nombre" placeholder="Ej: Titi Me Pregunto" required>
                </div>
                <div class="campo">
                    <label>Autor</label>
                    <select name="id_autor" required>
                        <option value="">Selecciona un autor</option>
                        <?php while ($autor = mysqli_fetch_assoc($autores)): ?>
                        <option value="<?php echo $autor['id']; ?>"><?php echo $autor['nombre']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="campo">
                    <label>Genero</label>
                    <select name="id_genero" required>
                        <option value="">Selecciona un genero</option>
                        <?php while ($genero = mysqli_fetch_assoc($generos)): ?>
                        <option value="<?php echo $genero['id']; ?>"><?php echo $genero['nombre']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="campo">
                    <label>Album</label>
                    <select name="id_album">
                        <option value="">Sin album</option>
                        <?php while ($album = mysqli_fetch_assoc($albumes)): ?>
                        <option value="<?php echo $album['id']; ?>"><?php echo $album['nombre']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="campo">
                    <label>Fecha de Lanzamiento</label>
                    <input type="date" name="fecha_lanzamiento">
                </div>
                <div class="campo">
                    <label>Duracion (ej: 3:45)</label>
                    <input type="text" name="duracion" placeholder="3:45">
                </div>
                <div class="campo">
                    <label>Imagen de la Cancion</label>
                    <input type="file" name="imagen" accept="image/*">
                </div>
                <div class="campo">
                    <label>Archivo MP3</label>
                    <input type="file" name="archivo_mp3" accept="audio/mp3,audio/*">
                </div>
                <button type="submit" class="btn-guardar">Guardar Cancion</button>
            </form>
        </div>

        <table class="tabla-admin">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Imagen</th>
                    <th>Nombre</th>
                    <th>Autor</th>
                    <th>Genero</th>
                    <th>Album</th>
                    <th>Duracion</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($cancion = mysqli_fetch_assoc($canciones)): ?>
                <tr>
                    <td><?php echo $cancion['id']; ?></td>
                    <td>
                        <?php if ($cancion['imagen']): ?>
                            <img src="../assets/img/<?php echo $cancion['imagen']; ?>" alt="cancion">
                        <?php else: ?>
                            Sin imagen
                        <?php endif; ?>
                    </td>
                    <td><?php echo $cancion['nombre']; ?></td>
                    <td><?php echo $cancion['autor']; ?></td>
                    <td><?php echo $cancion['genero']; ?></td>
                    <td><?php echo $cancion['album']; ?></td>
                    <td><?php echo $cancion['duracion']; ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>