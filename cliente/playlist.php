<?php
session_start();
require_once '../config/conexion.php';

if (!isset($_SESSION['usuario']) || $_SESSION['rol'] != 'cliente') {
    header("Location: ../index.php");
    exit();
}

$id_usuario = $_SESSION['id_usuario'];
$playlist   = mysqli_fetch_assoc(mysqli_query($conexion,
    "SELECT * FROM playlist WHERE id_usuario = '$id_usuario' LIMIT 1"));
$id_playlist = $playlist['id'];

$canciones = mysqli_query($conexion, "SELECT c.*, au.nombre AS autor, g.nombre AS genero, al.nombre AS album
             FROM playlist_canciones pc
             JOIN canciones c ON pc.id_cancion = c.id
             LEFT JOIN autores au ON c.id_autor = au.id
             LEFT JOIN generos g ON c.id_genero = g.id
             LEFT JOIN albumes al ON c.id_album = al.id
             WHERE pc.id_playlist = '$id_playlist'");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Playlist - Mi Spotify</title>
    <link rel="stylesheet" href="../assets/css/estilo.css">
    <link rel="stylesheet" href="../assets/css/panel.css">
    <link rel="stylesheet" href="../assets/css/cliente.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-logo">Mi Spotify</div>
        <div class="nav-links">
            <a href="inicio.php">Inicio</a>
            <a href="playlist.php">Mi Playlist</a>
        </div>
        <div class="nav-usuario">
            <?php echo $_SESSION['usuario']; ?>
            <a href="../procesar/logout.php" class="btn-logout">Cerrar Sesion</a>
        </div>
    </nav>

    <div class="contenedor-panel">
        <h2>Mi Playlist</h2>

        <?php if (isset($_GET['exito'])) echo "<div class='exito'>" . $_GET['exito'] . "</div>"; ?>

        <?php if (mysqli_num_rows($canciones) == 0): ?>
            <div class="playlist-vacia">
                <p>Tu playlist esta vacia.</p>
                <a href="inicio.php" class="btn-accion-link">Agregar canciones</a>
            </div>
        <?php else: ?>
            <div class="lista-playlist">
                <?php $numero = 1; while ($cancion = mysqli_fetch_assoc($canciones)): ?>
                <div class="fila-playlist" data-id="<?php echo $cancion['id']; ?>" 
                     data-mp3="../assets/mp3/<?php echo $cancion['archivo_mp3']; ?>"
                     data-nombre="<?php echo $cancion['nombre']; ?>"
                     data-autor="<?php echo $cancion['autor']; ?>"
                     data-imagen="<?php echo $cancion['imagen'] ? '../assets/img/' . $cancion['imagen'] : ''; ?>"
                     data-genero="<?php echo $cancion['id_genero']; ?>">
                    <span class="numero-cancion"><?php echo $numero++; ?></span>
                    <div class="playlist-imagen">
                        <?php if ($cancion['imagen']): ?>
                            <img src="../assets/img/<?php echo $cancion['imagen']; ?>" alt="cancion">
                        <?php else: ?>
                            <div class="sin-imagen-sm">♪</div>
                        <?php endif; ?>
                    </div>
                    <div class="playlist-info">
                        <span class="cancion-nombre"><?php echo $cancion['nombre']; ?></span>
                        <span class="cancion-autor"><?php echo $cancion['autor']; ?></span>
                    </div>
                    <span class="playlist-genero"><?php echo $cancion['genero']; ?></span>
                    <span class="playlist-duracion"><?php echo $cancion['duracion']; ?></span>
                    <button class="btn-play" onclick="reproducir(this)">▶</button>
                </div>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- REPRODUCTOR -->
    <div class="reproductor" id="reproductor">
        <div class="reproductor-info">
            <img id="rep-imagen" src="" alt="">
            <div>
                <span id="rep-nombre">Selecciona una cancion</span>
                <span id="rep-autor"></span>
            </div>
        </div>
        <div class="reproductor-controles">
            <button onclick="anterior()">⏮</button>
            <button onclick="togglePlay()" id="btn-play-pause">▶</button>
            <button onclick="siguiente()">⏭</button>
        </div>
        <div class="reproductor-barra">
            <span id="tiempo-actual">0:00</span>
            <input type="range" id="barra-progreso" value="0" min="0" max="100" oninput="cambiarTiempo(this.value)">
            <span id="tiempo-total">0:00</span>
        </div>
        <audio id="audio-player"></audio>
    </div>

    <link rel="stylesheet" href="../assets/css/reproductor.css">
    <script src="../assets/js/reproductor.js"></script>
</body>
</html>