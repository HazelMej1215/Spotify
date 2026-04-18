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

$canciones = mysqli_query($conexion, "SELECT c.*, au.nombre AS autor, g.nombre AS genero
             FROM playlist_canciones pc
             JOIN canciones c ON pc.id_cancion = c.id
             LEFT JOIN autores au ON c.id_autor = au.id
             LEFT JOIN generos g ON c.id_genero = g.id
             WHERE pc.id_playlist = '$id_playlist'
             AND c.activo = 1");

$total = mysqli_num_rows($canciones);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Playlist - Mi Spotify</title>
    <link rel="stylesheet" href="../assets/css/spotify.css">
</head>
<body>

<!-- LAYOUT -->
<div class="app-layout">
    <aside class="sidebar">
        <div class="sidebar-logo">Mi Spotify</div>
        <nav class="sidebar-menu">
            <a href="inicio.php" class="sidebar-link"><span class="icono">⊞</span> Inicio</a>
            <a href="buscar.php" class="sidebar-link"><span class="icono">⌕</span> Buscar</a>
            <a href="playlist.php" class="sidebar-link activo"><span class="icono">♪</span> Mi Playlist</a>
        </nav>
    </aside>

    <main class="main-content" style="background: linear-gradient(180deg, #1a3a2a 0%, #121212 40%)">

        <div class="topbar">
            <div class="topbar-nav">
                <button onclick="history.back()">‹</button>
                <button onclick="history.forward()">›</button>
            </div>
            <div class="topbar-usuario">
                <span><?php echo $_SESSION['usuario']; ?></span>
                <a href="../procesar/logout.php" class="btn-logout-top">Cerrar Sesion</a>
            </div>
        </div>

        <?php if (isset($_GET['exito'])) echo "<div style='background:rgba(29,185,84,0.15);border:1px solid #1db954;color:#1db954;padding:12px;border-radius:8px;margin-bottom:16px;font-size:14px'>" . $_GET['exito'] . "</div>"; ?>

        <div class="playlist-header">
            <div class="playlist-header-img">♪</div>
            <div class="playlist-header-info">
                <p style="font-size:12px;font-weight:700;letter-spacing:1px;text-transform:uppercase">Playlist</p>
                <h1>Mi Playlist</h1>
                <p><?php echo $_SESSION['usuario']; ?> • <?php echo $total; ?> canciones</p>
            </div>
        </div>

        <?php if ($total == 0): ?>
        <div class="playlist-vacia">
            <p>Tu playlist esta vacia</p>
            <a href="inicio.php" class="btn-verde">Agregar canciones</a>
        </div>
        <?php else: ?>
        <div class="lista-header">
            <span>#</span><span></span><span>Titulo</span>
            <span>Genero</span><span>Duracion</span><span></span>
        </div>
        <div class="lista-playlist" style="padding-bottom: 20px">
            <?php $n = 1; while ($c = mysqli_fetch_assoc($canciones)): ?>
            <div class="fila-playlist"
                 data-id="<?php echo $c['id']; ?>"
                 data-mp3="../assets/mp3/<?php echo $c['archivo_mp3']; ?>"
                 data-nombre="<?php echo htmlspecialchars($c['nombre']); ?>"
                 data-autor="<?php echo htmlspecialchars($c['autor']); ?>"
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
                    <span class="cancion-autor"><?php echo $c['autor']; ?></span>
                </div>
                <span class="playlist-genero"><?php echo $c['genero']; ?></span>
                <span class="playlist-duracion"><?php echo $c['duracion']; ?></span>
                <button class="btn-play-fila" onclick="reproducir(this)">▶</button>
            </div>
            <?php endwhile; ?>
        </div>
        <?php endif; ?>

    </main>
</div>

<!-- REPRODUCTOR -->
<div id="reproductor">
    <div class="rep-info">
        <div class="rep-info-placeholder" id="rep-placeholder">♪</div>
        <img id="rep-imagen" src="" alt="" style="display:none; width:56px; height:56px; border-radius:6px; object-fit:cover">
        <div class="rep-texto">
            <span id="rep-nombre" style="font-size:14px; font-weight:600; color:#fff">Selecciona una cancion</span>
            <span id="rep-autor" style="font-size:12px; color:#aaa"></span>
        </div>
    </div>
    <div class="rep-controles">
        <div class="rep-botones">
            <button onclick="anterior()" style="background:transparent;border:none;color:#aaa;font-size:18px;cursor:pointer">⏮</button>
            <button onclick="togglePlay()" id="btn-play-pause" style="background:#fff;color:#000;border:none;width:36px;height:36px;border-radius:50%;font-size:14px;cursor:pointer">▶</button>
            <button onclick="siguiente()" style="background:transparent;border:none;color:#aaa;font-size:18px;cursor:pointer">⏭</button>
        </div>
        <div class="rep-barra" style="display:flex;align-items:center;gap:10px;width:100%;max-width:500px">
            <span id="tiempo-actual" style="font-size:11px;color:#aaa;min-width:35px;text-align:center">0:00</span>
            <input type="range" id="barra-progreso" value="0" min="0" max="100" 
                   oninput="cambiarTiempo(this.value)"
                   style="flex:1;height:4px;accent-color:#1db954;cursor:pointer">
            <span id="tiempo-total" style="font-size:11px;color:#aaa;min-width:35px;text-align:center">0:00</span>
        </div>
    </div>
    <div style="display:flex;align-items:center;gap:8px;min-width:150px;justify-content:flex-end">
        <span style="color:#aaa">🔊</span>
        <input type="range" id="barra-volumen" value="100" min="0" max="100"
               oninput="cambiarVolumen(this.value)"
               style="width:80px;height:4px;accent-color:#1db954;cursor:pointer">
    </div>
    <audio id="audio-player"></audio>
</div>

<div class="notif-ia" id="notif-ia"></div>
<script src="../assets/js/reproductor.js"></script>
</body>
</html>