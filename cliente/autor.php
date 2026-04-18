<?php
session_start();
require_once '../config/conexion.php';

if (!isset($_SESSION['usuario']) || $_SESSION['rol'] != 'cliente') {
    header("Location: ../index.php");
    exit();
}

$id    = (int)$_GET['id'];
$autor = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT * FROM autores WHERE id = '$id' AND activo = 1"));

if (!$autor) {
    header("Location: inicio.php");
    exit();
}

$albumes   = mysqli_query($conexion, "SELECT * FROM albumes WHERE id_autor = '$id' AND activo = 1");
$canciones = mysqli_query($conexion, "SELECT c.*, g.nombre AS genero FROM canciones c
             LEFT JOIN generos g ON c.id_genero = g.id
             WHERE c.id_autor = '$id' AND c.activo = 1 ORDER BY c.id DESC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $autor['nombre']; ?> - Mi Spotify</title>
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
        <!-- HEADER ARTISTA -->
        <div style="padding:32px; background: linear-gradient(180deg, #1a3a2a, #121212); min-height:250px; display:flex; align-items:flex-end; gap:24px">
            <?php if ($autor['imagen']): ?>
                <img src="../assets/img/<?php echo $autor['imagen']; ?>" 
                     style="width:160px;height:160px;border-radius:50%;object-fit:cover;box-shadow:0 8px 32px rgba(0,0,0,0.5)">
            <?php else: ?>
                <div style="width:160px;height:160px;border-radius:50%;background:#282828;display:flex;align-items:center;justify-content:center;font-size:60px">♪</div>
            <?php endif; ?>
            <div>
                <p style="font-size:12px;font-weight:700;letter-spacing:1px;text-transform:uppercase">Artista</p>
                <h1 style="font-size:48px;font-weight:700;margin:8px 0"><?php echo $autor['nombre']; ?></h1>
            </div>
        </div>

        <div style="padding:32px">
            <div class="topbar-nav" style="margin-bottom:24px">
                <button onclick="history.back()">‹ Volver</button>
            </div>

            <!-- ALBUMS -->
            <?php if (mysqli_num_rows($albumes) > 0): ?>
            <h2 class="seccion-titulo">Albums</h2>
            <div class="grid-canciones" style="margin-bottom:32px">
                <?php while ($al = mysqli_fetch_assoc($albumes)): ?>
                <a href="album.php?id=<?php echo $al['id']; ?>" style="text-decoration:none">
                    <div class="tarjeta-cancion">
                        <?php if ($al['imagen']): ?>
                            <img class="tarjeta-imagen" src="../assets/img/<?php echo $al['imagen']; ?>" alt="">
                        <?php else: ?>
                            <div class="tarjeta-imagen-placeholder">💿</div>
                        <?php endif; ?>
                        <div class="tarjeta-nombre"><?php echo $al['nombre']; ?></div>
                        <div class="tarjeta-sub"><?php echo $al['anio']; ?></div>
                    </div>
                </a>
                <?php endwhile; ?>
            </div>
            <?php endif; ?>

            <!-- CANCIONES -->
            <h2 class="seccion-titulo">Canciones populares</h2>
            <div class="lista-playlist">
                <?php $n=1; while ($c = mysqli_fetch_assoc($canciones)): ?>
                <div class="fila-playlist"
                     data-id="<?php echo $c['id']; ?>"
                     data-mp3="../assets/mp3/<?php echo $c['archivo_mp3']; ?>"
                     data-nombre="<?php echo htmlspecialchars($c['nombre']); ?>"
                     data-autor="<?php echo htmlspecialchars($autor['nombre']); ?>"
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
                        <span class="cancion-autor"><?php echo $c['genero']; ?></span>
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