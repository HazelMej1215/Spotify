<?php
session_start();
require_once '../config/conexion.php';

if (!isset($_SESSION['usuario']) || $_SESSION['rol'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

$autores = mysqli_query($conexion, "SELECT * FROM autores ORDER BY nombre");
$albumes = mysqli_query($conexion, "SELECT a.*, au.nombre AS autor FROM albumes a 
           LEFT JOIN autores au ON a.id_autor = au.id ORDER BY a.id DESC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Albums - Mi Spotify</title>
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
        <h2>Albums</h2>

        <?php if (isset($_GET['exito'])) echo "<div class='exito'>" . $_GET['exito'] . "</div>"; ?>
        <?php if (isset($_GET['error'])) echo "<div class='error'>" . $_GET['error'] . "</div>"; ?>

        <div class="contenedor-form">
            <h3>Nuevo Album</h3>
            <form action="../procesar/guardar_album.php" method="POST" enctype="multipart/form-data">
                <div class="campo">
                    <label>Nombre del Album</label>
                    <input type="text" name="nombre" placeholder="Ej: Un Verano Sin Ti" required>
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
                    <label>Año de lanzamiento</label>
                    <input type="number" name="anio" placeholder="Ej: 2024" min="1900" max="2030">
                </div>
                <div class="campo">
                    <label>Imagen del Album</label>
                    <input type="file" name="imagen" accept="image/*">
                </div>
                <button type="submit" class="btn-guardar">Guardar Album</button>
            </form>
        </div>

        <table class="tabla-admin">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Imagen</th>
                    <th>Nombre</th>
                    <th>Autor</th>
                    <th>Año</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($album = mysqli_fetch_assoc($albumes)): ?>
                <tr>
                    <td><?php echo $album['id']; ?></td>
                    <td>
                        <?php if ($album['imagen']): ?>
                            <img src="../assets/img/<?php echo $album['imagen']; ?>" alt="album">
                        <?php else: ?>
                            Sin imagen
                        <?php endif; ?>
                    </td>
                    <td><?php echo $album['nombre']; ?></td>
                    <td><?php echo $album['autor']; ?></td>
                    <td><?php echo $album['anio']; ?></td>
                    <td>
                        <a href="editar_album.php?id=<?php echo $album['id']; ?>" class="btn-toggle btn-habilitar">Editar</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>