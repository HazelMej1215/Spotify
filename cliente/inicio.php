<?php
session_start();
require_once '../config/conexion.php';

if (!isset($_SESSION['usuario']) || $_SESSION['rol'] != 'cliente') {
    header("Location: ../index.php");
    exit();
}

$id_usuario = $_SESSION['id_usuario'];

$playlist = mysqli_fetch_assoc(mysqli_query($conexion, 
    "SELECT * FROM playlist WHERE id_usuario = '$id_usuario' LIMIT 1"));

if (!$playlist) {
    mysqli_query($conexion, "INSERT INTO playlist (id_usuario, nombre) 
                             VALUES ('$id_usuario', 'Mi Playlist')");
    $playlist = mysqli_fetch_assoc(mysqli_query($conexion, 
        "SELECT * FROM playlist WHERE id_usuario = '$id_usuario' LIMIT 1"));
}

$id_playlist = $playlist['id'];

$en_playlist = [];
$resultado = mysqli_query($conexion, "SELECT id_cancion FROM playlist_canciones WHERE id_playlist = '$id_playlist'");
while ($fila = mysqli_fetch_assoc($resultado)) {
    $en_playlist[] = $fila['id_cancion'];
}

$generos = mysqli_query($conexion, "SELECT * FROM generos ORDER BY nombre");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Spotify - Inicio</title>
    <link rel="stylesheet" href="../assets/css/estilo.css">
    <link rel="stylesheet" href="../assets/css/panel.css">
    <link rel="stylesheet" href="../assets/css/cliente.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-logo">Mi Spotify</div>
        <div class="nav-links">
            <a href="inicio.php" class="activo">Inicio</a>
            <a href="playlist.php">Mi Playlist</a>
        </div>
        <div class="nav-usuario">
            <?php echo $_SESSION['usuario']; ?>
            <a href="../procesar/logout.php" class="btn-logout">Cerrar Sesion</a>
        </div>
    </nav>

    <div class="contenedor-panel">
        <h2>Canciones disponibles</h2>
        <p class="subtitulo-panel">Selecciona las canciones que quieres agregar a tu playlist</p>

        <form action="../procesar/guardar_playlist.php" method="POST">
            <input type="hidden" name="id_playlist" value="<?php echo $id_playlist; ?>">

            <?php while ($genero = mysqli_fetch_assoc($generos)):
                // Solo canciones activas, de autores activos y albums activos
                $canciones = mysqli_query($conexion, "SELECT c.*, au.nombre AS autor, al.nombre AS album
                             FROM canciones c
                             LEFT JOIN autores au ON c.id_autor = au.id
                             LEFT JOIN albumes al ON c.id_album = al.id
                             WHERE c.id_genero = '{$genero['id']}'
                             AND c.activo = 1
                             AND au.activo = 1
                             AND (al.id IS NULL OR al.activo = 1)");
                
                if (mysqli_num_rows($canciones) == 0) continue;
            ?>
            <div class="seccion-genero">
                <h3><?php echo $genero['nombre']; ?></h3>
                <div class="grid-canciones">
                    <?php while ($cancion = mysqli_fetch_assoc($canciones)): 
                        $checked = in_array($cancion['id'], $en_playlist) ? 'checked' : '';
                    ?>
                    <label class="tarjeta-cancion <?php echo $checked ? 'seleccionada' : ''; ?>">
                        <input type="checkbox" name="canciones[]" 
                               value="<?php echo $cancion['id']; ?>" <?php echo $checked; ?>
                               onchange="this.closest('label').classList.toggle('seleccionada', this.checked)">
                        <div class="cancion-imagen">
                            <?php if ($cancion['imagen']): ?>
                                <img src="../assets/img/<?php echo $cancion['imagen']; ?>" alt="cancion">
                            <?php else: ?>
                                <div class="sin-imagen">♪</div>
                            <?php endif; ?>
                        </div>
                        <div class="cancion-info">
                            <span class="cancion-nombre"><?php echo $cancion['nombre']; ?></span>
                            <span class="cancion-autor"><?php echo $cancion['autor']; ?></span>
                            <span class="cancion-duracion"><?php echo $cancion['duracion']; ?></span>
                        </div>
                    </label>
                    <?php endwhile; ?>
                </div>
            </div>
            <?php endwhile; ?>

            <button type="submit" class="btn-guardar-playlist">Guardar Playlist</button>
        </form>
    </div>
</body>
</html>