<?php
session_start();
require_once '../config/conexion.php';

if (!isset($_SESSION['usuario']) || $_SESSION['rol'] != 'cliente') {
    header("Location: ../index.php");
    exit();
}

$q = isset($_GET['q']) ? mysqli_real_escape_string($conexion, $_GET['q']) : '';

$canciones = $autores = $albumes = null;

if ($q != '') {
    $canciones = mysqli_query($conexion, "SELECT c.*, au.nombre AS autor FROM canciones c
                 LEFT JOIN autores au ON c.id_autor = au.id
                 WHERE c.nombre LIKE '%$q%' AND c.activo = 1 LIMIT 10");
    $autores   = mysqli_query($conexion, "SELECT * FROM autores WHERE nombre LIKE '%$q%' AND activo = 1 LIMIT 6");
    $albumes   = mysqli_query($conexion, "SELECT a.*, au.nombre AS autor FROM albumes a
                 LEFT JOIN autores au ON a.id_autor = au.id
                 WHERE a.nombre LIKE '%$q%' AND a.activo = 1 LIMIT 6");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buscar - Mi Spotify</title>
    <link rel="stylesheet" href="../assets/css/spotify.css">
</head>
<body>
<div class="app-layout">
    <aside class="sidebar">
        <div class="sidebar-logo">Mi Spotify</div>
        <nav class="sidebar-menu">
            <a href="inicio.php" class="sidebar-link">
                <span class="icono">⊞</span> Inicio
            </a>
            <a href="buscar.php" class="sidebar-link activo">
                <span class="icono">⌕</span> Buscar
            </a>
            <a href="playlist.php" class="sidebar-link">
                <span class="icono">♪</span> Mi Playlist
            </a>
        </nav>
    </aside>

    <main class="main-content">
        <div class="topbar">
            <div class="topbar-nav">
                <button onclick="history.back()">‹</button>
            </div>
            <div class="barra-busqueda">
                <span class="icono-busqueda">⌕</span>
                <form method="GET">
                    <input type="text" name="q" placeholder="Artistas, canciones, albums..."
                           value="<?php echo htmlspecialchars($q); ?>" autofocus
                           onkeypress="if(event.key==='Enter') this.form.submit()">
                </form>
            </div>
            <div class="topbar-usuario">
                <span><?php echo $_SESSION['usuario']; ?></span>
                <a href="../procesar/logout.php" class="btn-logout-top">Cerrar Sesion</a>
            </div>
        </div>

        <?php if ($q == ''): ?>
        <h2 class="seccion-titulo">Busca tus canciones favoritas</h2>
        <?php else: ?>

        <?php if (mysqli_num_rows($autores) > 0): ?>
        <h2 class="seccion-titulo">Artistas</h2>
        <div class="grid-canciones">
            <?php while ($a = mysqli_fetch_assoc($autores)): ?>
            <a href="autor.php?id=<?php echo $a['id']; ?>" style="text-decoration:none">
                <div class="tarjeta-cancion">
                    <?php if ($a['imagen']): ?>
                        <img class="tarjeta-imagen" src="../assets/img/<?php echo $a['imagen']; ?>" 
                             alt="" style="border-radius:50%">
                    <?php else: ?>
                        <div class="tarjeta-imagen-placeholder" style="border-radius:50%">♪</div>
                    <?php endif; ?>
                    <div class="tarjeta-nombre"><?php echo $a['nombre']; ?></div>
                    <div class="tarjeta-sub">Artista</div>
                </div>
            </a>
            <?php endwhile; ?>
        </div>
        <?php endif; ?>

        <?php if (mysqli_num_rows($albumes) > 0): ?>
        <h2 class="seccion-titulo">Albums</h2>
        <div class="grid-canciones">
            <?php while ($al = mysqli_fetch_assoc($albumes)): ?>
            <a href="album.php?id=<?php echo $al['id']; ?>" style="text-decoration:none">
                <div class="tarjeta-cancion">
                    <?php if ($al['imagen']): ?>
                        <img class="tarjeta-imagen" src="../assets/img/<?php echo $al['imagen']; ?>" alt="">
                    <?php else: ?>
                        <div class="tarjeta-imagen-placeholder">💿</div>
                    <?php endif; ?>
                    <div class="tarjeta-nombre"><?php echo $al['nombre']; ?></div>
                    <div class="tarjeta-sub"><?php echo $al['autor']; ?></div>
                </div>
            </a>
            <?php endwhile; ?>
        </div>
        <?php endif; ?>

        <?php if (mysqli_num_rows($canciones) > 0): ?>
        <h2 class="seccion-titulo">Canciones</h2>
        <div class="lista-playlist">
            <?php $n=1; while ($c = mysqli_fetch_assoc($canciones)): ?>
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
                <span class="playlist-genero"></span>
                <span class="playlist-duracion"><?php echo $c['duracion']; ?></span>
                <button class="btn-play-fila" onclick="reproducir(this)">▶</button>
            </div>
            <?php endwhile; ?>
        </div>
        <?php endif; ?>

        <?php endif; ?>
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