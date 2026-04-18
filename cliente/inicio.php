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
    mysqli_query($conexion, "INSERT INTO playlist (id_usuario, nombre) VALUES ('$id_usuario', 'Mi Playlist')");
    $playlist = mysqli_fetch_assoc(mysqli_query($conexion,
        "SELECT * FROM playlist WHERE id_usuario = '$id_usuario' LIMIT 1"));
}

$id_playlist = $playlist['id'];

$en_playlist = [];
$res = mysqli_query($conexion, "SELECT id_cancion FROM playlist_canciones WHERE id_playlist = '$id_playlist'");
while ($f = mysqli_fetch_assoc($res)) $en_playlist[] = $f['id_cancion'];

$generos = mysqli_query($conexion, "SELECT * FROM generos ORDER BY nombre");
$busqueda = isset($_GET['q']) ? mysqli_real_escape_string($conexion, $_GET['q']) : '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Spotify</title>
    <link rel="stylesheet" href="../assets/css/spotify.css">
</head>
<body>

<div class="app-layout">
    <!-- SIDEBAR -->
    <aside class="sidebar">
        <div class="sidebar-logo">Mi Spotify</div>
        <nav class="sidebar-menu">
            <a href="inicio.php" class="sidebar-link activo">
                <span class="icono">⊞</span> Inicio
            </a>
            <a href="buscar.php" class="sidebar-link">
                <span class="icono">⌕</span> Buscar
            </a>
            <a href="playlist.php" class="sidebar-link">
                <span class="icono">♪</span> Mi Playlist
            </a>
        </nav>
        <div class="sidebar-divider"></div>
        <div class="sidebar-seccion">Generos</div>
        <?php
        $gen_sidebar = mysqli_query($conexion, "SELECT * FROM generos ORDER BY nombre");
        while ($g = mysqli_fetch_assoc($gen_sidebar)):
        ?>
        <a href="inicio.php#genero-<?php echo $g['id']; ?>" class="sidebar-link" style="font-size:13px; padding:8px 16px">
            <?php echo $g['nombre']; ?>
        </a>
        <?php endwhile; ?>
    </aside>

    <!-- CONTENIDO -->
    <main class="main-content">
        <div class="topbar">
            <div class="topbar-nav">
                <button onclick="history.back()">‹</button>
                <button onclick="history.forward()">›</button>
            </div>
            <div class="barra-busqueda">
                <span class="icono-busqueda">⌕</span>
                <form method="GET" action="buscar.php">
                    <input type="text" name="q" placeholder="Artistas, canciones, albums..." 
                           value="<?php echo htmlspecialchars($busqueda); ?>"
                           onkeypress="if(event.key==='Enter') this.form.submit()">
                </form>
            </div>
            <div class="topbar-usuario">
                <span><?php echo $_SESSION['usuario']; ?></span>
                <a href="../procesar/logout.php" class="btn-logout-top">Cerrar Sesion</a>
            </div>
        </div>

        <form action="../procesar/guardar_playlist.php" method="POST">
            <input type="hidden" name="id_playlist" value="<?php echo $id_playlist; ?>">

            <?php while ($genero = mysqli_fetch_assoc($generos)):
                $canciones = mysqli_query($conexion, "SELECT c.*, au.nombre AS autor
                             FROM canciones c
                             LEFT JOIN autores au ON c.id_autor = au.id
                             LEFT JOIN albumes al ON c.id_album = al.id
                             WHERE c.id_genero = '{$genero['id']}'
                             AND c.activo = 1 AND au.activo = 1
                             AND (al.id IS NULL OR al.activo = 1)");
                if (mysqli_num_rows($canciones) == 0) continue;
            ?>
            <div id="genero-<?php echo $genero['id']; ?>">
                <h2 class="seccion-titulo"><?php echo $genero['nombre']; ?></h2>
                <div class="grid-canciones">
                    <?php while ($c = mysqli_fetch_assoc($canciones)):
                        $sel = in_array($c['id'], $en_playlist) ? 'seleccionada' : '';
                        $chk = $sel ? 'checked' : '';
                    ?>
                    <label class="tarjeta-cancion <?php echo $sel; ?>">
                        <input type="checkbox" name="canciones[]" value="<?php echo $c['id']; ?>" <?php echo $chk; ?>
                               onchange="this.closest('label').classList.toggle('seleccionada', this.checked)">
                        <?php if ($c['imagen']): ?>
                            <img class="tarjeta-imagen" src="../assets/img/<?php echo $c['imagen']; ?>" alt="">
                        <?php else: ?>
                            <div class="tarjeta-imagen-placeholder">♪</div>
                        <?php endif; ?>
                        <div class="tarjeta-nombre"><?php echo $c['nombre']; ?></div>
                        <div class="tarjeta-sub"><?php echo $c['autor']; ?></div>
                    </label>
                    <?php endwhile; ?>
                </div>
            </div>
            <?php endwhile; ?>

            <button type="submit" class="btn-guardar-playlist">Guardar Playlist</button>
        </form>
    </main>
</div>

<!-- REPRODUCTOR OCULTO -->
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