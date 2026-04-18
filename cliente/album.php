<?php
session_start();
require_once '../config/conexion.php';

if (!isset($_SESSION['usuario']) || $_SESSION['rol'] != 'cliente') {
    header("Location: ../index.php");
    exit();
}

$id    = (int)$_GET['id'];
$album = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT a.*, au.nombre AS autor, au.id AS id_autor
         FROM albumes a LEFT JOIN autores au ON a.id_autor = au.id
         WHERE a.id = '$id' AND a.activo = 1"));

if (!$album) {
    header("Location: inicio.php");
    exit();
}

$canciones = mysqli_query($conexion, "SELECT c.*, g.nombre AS genero FROM canciones c
             LEFT JOIN generos g ON c.id_genero = g.id
             WHERE c.id_album = '$id' AND c.activo = 1 ORDER BY c.id ASC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $album['nombre']; ?> - Mi Spotify</title>
    <link rel="stylesheet" href="../assets/css/spotify.css">
</head>
<body>
<div class="app-layout">
    <aside class="sidebar">
        <div class="sidebar-logo">Mi Spotify</div>
        <nav class="sidebar-menu">
            <a href="inicio.php" class="sidebar-link"><span class="icono">⊞</span> Inicio</a>
            <a href="buscar.php" class="sidebar-link"><span class="icono">⌕</span> Buscar</a>
            <a href="playlist.php" class="sidebar-link"><span class="icono">♪</span> Mi Playlist</a>
        </nav>
    </aside>

    <main class="main-content" style="padding:0">
        <div style="padding:32px; background: linear-gradient(180deg, #1a3a2a, #121212); display:flex; align-items:flex-end; gap:24px; min-height:220px">
            <?php if ($album['imagen']): ?>
                <img src="../assets/img/<?php echo $album['imagen']; ?>"
                     style="width:160px;height:160px;border-radius:10px;object-fit:cover;box-shadow:0 8px 32px rgba(0,0,0,0.5)">
            <?php else: ?>
                <div style="width:160px;height:160px;border-radius:10px;background:#282828;display:flex;align-items:center;justify-content:center;font-size:60px">💿</div>
            <?php endif; ?>
            <div>
                <p style="font-size:12px;font-weight:700;letter-spacing:1px;text-transform:uppercase">Album</p>
                <h1 style="font-size:42px;font-weight:700;margin:8px 0"><?php echo $album['nombre']; ?></h1>
                <p>
                    <a href="autor.php?id=<?php echo $album['id_autor']; ?>" 
                       style="color:#1db954;text-decoration:none;font-weight:600">
                        <?php echo $album['autor']; ?>
                    </a>
                    • <?php echo $album['anio']; ?>
                </p>
            </div>
        </div>

        <div style="padding:32px">
            <div class="topbar-nav" style="margin-bottom:24px">
                <button onclick="history.back()">‹ Volver</button>
            </div>

            <div class="lista-header">
                <span>#</span><span></span><span>Titulo</span>
                <span>Genero</span><span>Duracion</span><span></span>
            </div>
            <div class="lista-playlist">
                <?php $n=1; while ($c = mysqli_fetch_assoc($canciones)): ?>
                <div class="fila-playlist"
                     data-id="<?php echo $c['id']; ?>"
                     data-mp3="../assets/mp3/<?php echo $c['archivo_mp3']; ?>"
                     data-nombre="<?php echo htmlspecialchars($c['nombre']); ?>"
                     data-autor="<?php echo htmlspecialchars($album['autor']); ?>"
                     data-imagen="<?php echo $c['imagen'] ? '../assets/img/'.$c['imagen'] : ''; ?>"
                     data-genero="<?php echo $c['id_genero']; ?>">
                    <span class="numero-cancion"><?php echo $n++; ?></span>
                    <div class="playlist-imagen">
                        <?php if ($c['imagen']): ?>
                            <img src="../assets/img/<?php echo $c['imagen']; ?>" alt="">
                        <?php else: ?>
                            <div class="sin-imagen-sm">♪</div>
                        <?php endif; ?>
                    </div>
                    <div class="playlist-info">
                        <span class="cancion-nombre"><?php echo $c['nombre']; ?></span>
                        <span class="cancion-autor"><?php echo $album['autor']; ?></span>
                    </div>
                    <span class="playlist-genero"><?php echo $c['genero']; ?></span>
                    <span class="playlist-duracion"><?php echo $c['duracion']; ?></span>
                    <button class="btn-play-fila" onclick="reproducir(this)">▶</button>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </main>
</div>

<div class="reproductor" id="reproductor" style="display:none">
    <div class="rep-info">
        <div class="rep-info-placeholder" id="rep-placeholder">♪</div>
        <img id="rep-imagen" src="" alt="" style="display:none">
        <div class="rep-texto">
            <span id="rep-nombre">Selecciona una cancion</span>
            <span id="rep-autor"></span>
        </div>
    </div>
    <div class="rep-controles">
        <div class="rep-botones">
            <button onclick="anterior()">⏮</button>
            <button onclick="togglePlay()" id="btn-play-pause">▶</button>
            <button onclick="siguiente()">⏭</button>
        </div>
        <div class="rep-barra">
            <span id="tiempo-actual">0:00</span>
            <input type="range" id="barra-progreso" value="0" min="0" max="100" oninput="cambiarTiempo(this.value)">
            <span id="tiempo-total">0:00</span>
        </div>
    </div>
    <div class="rep-volumen">
        <span>🔊</span>
        <input type="range" id="barra-volumen" value="100" min="0" max="100" oninput="cambiarVolumen(this.value)">
    </div>
    <audio id="audio-player"></audio>
</div>

<div class="notif-ia" id="notif-ia"></div>
<script src="../assets/js/reproductor.js"></script>
</body>
</html>