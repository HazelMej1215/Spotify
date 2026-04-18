<?php
session_start();
require_once '../config/conexion.php';

if (!isset($_SESSION['usuario']) || $_SESSION['rol'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

$tab = isset($_GET['tab']) ? $_GET['tab'] : 'usuarios';

$usuarios  = mysqli_query($conexion, "SELECT * FROM usuarios WHERE rol = 'cliente' ORDER BY id DESC");
$canciones = mysqli_query($conexion, "SELECT c.*, au.nombre AS autor, g.nombre AS genero, al.nombre AS album
             FROM canciones c 
             LEFT JOIN autores au ON c.id_autor = au.id
             LEFT JOIN generos g ON c.id_genero = g.id
             LEFT JOIN albumes al ON c.id_album = al.id
             ORDER BY g.nombre, al.nombre, c.nombre");
$albumes   = mysqli_query($conexion, "SELECT a.*, au.nombre AS autor FROM albumes a 
             LEFT JOIN autores au ON a.id_autor = au.id ORDER BY au.nombre, a.nombre");
$autores   = mysqli_query($conexion, "SELECT * FROM autores ORDER BY nombre");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion - Mi Spotify</title>
    <link rel="stylesheet" href="../assets/css/estilo.css">
    <link rel="stylesheet" href="../assets/css/panel.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <style>
        .pestanas {
            display: flex;
            gap: 8px;
            margin: 24px 0 0 0;
            border-bottom: 2px solid #282828;
        }
        .pestana {
            padding: 10px 24px;
            border-radius: 8px 8px 0 0;
            text-decoration: none;
            color: #aaa;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s;
            border: 1px solid transparent;
            border-bottom: none;
        }
        .pestana:hover { color: #fff; background: #1e1e1e; }
        .pestana.activa {
            background: #1e1e1e;
            color: #1db954;
            border-color: #282828;
            border-bottom: 2px solid #1e1e1e;
            margin-bottom: -2px;
        }
        .contenido-tab {
            background: #1e1e1e;
            border: 1px solid #282828;
            border-top: none;
            border-radius: 0 0 12px 12px;
            padding: 24px;
            margin-bottom: 40px;
        }
        .contenido-tab h3 {
            color: #1db954;
            margin-bottom: 16px;
            font-size: 18px;
        }
        .grupo {
            margin-bottom: 30px;
        }
        .grupo-header {
            color: #fff;
            font-size: 14px;
            padding: 8px 12px;
            background: #282828;
            border-radius: 6px;
            border-left: 3px solid #1db954;
            margin-bottom: 8px;
        }
        .grupo-header span {
            color: #aaa;
            font-size: 12px;
            margin-left: 8px;
        }
        @media (max-width: 768px) {
            .pestanas { flex-wrap: wrap; }
            .pestana { padding: 8px 14px; font-size: 13px; }
        }
    </style>
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
        <h2>Gestion General</h2>
        <?php if (isset($_GET['exito'])) echo "<div class='exito'>" . $_GET['exito'] . "</div>"; ?>
        <?php if (isset($_GET['error'])) echo "<div class='error'>" . $_GET['error'] . "</div>"; ?>

        <div class="pestanas">
            <a href="?tab=usuarios"  class="pestana <?php echo $tab=='usuarios'  ? 'activa':''; ?>">Clientes</a>
            <a href="?tab=canciones" class="pestana <?php echo $tab=='canciones' ? 'activa':''; ?>">Canciones</a>
            <a href="?tab=albumes"   class="pestana <?php echo $tab=='albumes'   ? 'activa':''; ?>">Albums</a>
            <a href="?tab=autores"   class="pestana <?php echo $tab=='autores'   ? 'activa':''; ?>">Artistas</a>
        </div>

        <div class="contenido-tab">

            <?php if ($tab == 'usuarios'): ?>
            <!-- ===== CLIENTES ===== -->
            <h3>Clientes Registrados</h3>
            <table class="tabla-admin">
                <thead>
                    <tr><th>#</th><th>Nombre</th><th>Email</th><th>Fecha Registro</th><th>Estado</th><th>Accion</th></tr>
                </thead>
                <tbody>
                    <?php while ($u = mysqli_fetch_assoc($usuarios)): ?>
                    <tr>
                        <td><?php echo $u['id']; ?></td>
                        <td><?php echo $u['nombre']; ?></td>
                        <td><?php echo $u['email']; ?></td>
                        <td><?php echo date('d/m/Y', strtotime($u['fecha_registro'])); ?></td>
                        <td><span class="badge <?php echo $u['activo'] ? 'badge-activo':'badge-inactivo'; ?>">
                            <?php echo $u['activo'] ? 'Activo':'Inhabilitado'; ?></span></td>
                        <td>
                            <a href="../procesar/toggle_estado.php?tabla=usuarios&id=<?php echo $u['id']; ?>&estado=<?php echo $u['activo']; ?>&redirect=admin/usuarios.php?tab=usuarios"
                               class="btn-toggle <?php echo $u['activo'] ? 'btn-inhabilitar':'btn-habilitar'; ?>">
                                <?php echo $u['activo'] ? 'Inhabilitar':'Habilitar'; ?>
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <?php elseif ($tab == 'canciones'): ?>
            <!-- ===== CANCIONES por Genero > Album ===== -->
            <h3>Canciones clasificadas por Genero y Album</h3>
            <?php
            $generos_q = mysqli_query($conexion, "SELECT * FROM generos ORDER BY nombre");
            while ($genero = mysqli_fetch_assoc($generos_q)):
                $albumes_genero = mysqli_query($conexion, "SELECT DISTINCT al.id, al.nombre AS album_nombre, au.nombre AS autor_nombre
                    FROM canciones c
                    LEFT JOIN albumes al ON c.id_album = al.id
                    LEFT JOIN autores au ON c.id_autor = au.id
                    WHERE c.id_genero = '{$genero['id']}'
                    GROUP BY al.id");
                
                $count_canciones = mysqli_fetch_row(mysqli_query($conexion, 
                    "SELECT COUNT(*) FROM canciones WHERE id_genero = '{$genero['id']}'"))[0];
                if ($count_canciones == 0) continue;
            ?>
            <div class="grupo">
                <div class="grupo-header">
                    <?php echo $genero['nombre']; ?>
                    <span><?php echo $count_canciones; ?> canciones</span>
                </div>
                <?php while ($album_row = mysqli_fetch_assoc($albumes_genero)):
                    $album_id = $album_row['id'] ? $album_row['id'] : 'NULL';
                    $canciones_grupo = mysqli_query($conexion, "SELECT c.*, au.nombre AS autor
                        FROM canciones c
                        LEFT JOIN autores au ON c.id_autor = au.id
                        WHERE c.id_genero = '{$genero['id']}' 
                        AND " . ($album_row['id'] ? "c.id_album = '{$album_row['id']}'" : "c.id_album IS NULL"));
                ?>
                <table class="tabla-admin" style="margin-bottom:12px">
                    <thead>
                        <tr>
                            <th colspan="6" style="background:#333; color:#1db954; font-size:13px">
                                Album: <?php echo $album_row['album_nombre'] ?? 'Sin Album'; ?>
                                <?php if ($album_row['autor_nombre']) echo ' — ' . $album_row['autor_nombre']; ?>
                            </th>
                        </tr>
                        <tr><th>#</th><th>Nombre</th><th>Autor</th><th>Duracion</th><th>Estado</th><th>Accion</th></tr>
                    </thead>
                    <tbody>
                        <?php while ($c = mysqli_fetch_assoc($canciones_grupo)): ?>
                        <tr>
                            <td><?php echo $c['id']; ?></td>
                            <td><?php echo $c['nombre']; ?></td>
                            <td><?php echo $c['autor']; ?></td>
                            <td><?php echo $c['duracion']; ?></td>
                            <td><span class="badge <?php echo $c['activo'] ? 'badge-activo':'badge-inactivo'; ?>">
                                <?php echo $c['activo'] ? 'Activo':'Inhabilitado'; ?></span></td>
                            <td>
                                <a href="../procesar/toggle_estado.php?tabla=canciones&id=<?php echo $c['id']; ?>&estado=<?php echo $c['activo']; ?>&redirect=admin/usuarios.php?tab=canciones"
                                   class="btn-toggle <?php echo $c['activo'] ? 'btn-inhabilitar':'btn-habilitar'; ?>">
                                    <?php echo $c['activo'] ? 'Inhabilitar':'Habilitar'; ?>
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <?php endwhile; ?>
            </div>
            <?php endwhile; ?>

            <?php elseif ($tab == 'albumes'): ?>
            <!-- ===== ALBUMS por Artista ===== -->
            <h3>Albums clasificados por Artista</h3>
            <?php
            $artistas_q = mysqli_query($conexion, "SELECT * FROM autores ORDER BY nombre");
            while ($artista = mysqli_fetch_assoc($artistas_q)):
                $albums_artista = mysqli_query($conexion, "SELECT * FROM albumes WHERE id_autor = '{$artista['id']}'");
                if (mysqli_num_rows($albums_artista) == 0) continue;
            ?>
            <div class="grupo">
                <div class="grupo-header">
                    <?php echo $artista['nombre']; ?>
                    <span><?php echo mysqli_num_rows($albums_artista); ?> albums</span>
                </div>
                <?php mysqli_data_seek($albums_artista, 0); ?>
                <table class="tabla-admin" style="margin-bottom:16px">
                    <thead>
                        <tr><th>#</th><th>Imagen</th><th>Nombre</th><th>Año</th><th>Estado</th><th>Accion</th></tr>
                    </thead>
                    <tbody>
                        <?php while ($al = mysqli_fetch_assoc($albums_artista)): ?>
                        <tr>
                            <td><?php echo $al['id']; ?></td>
                            <td><?php if ($al['imagen']): ?>
                                <img src="../assets/img/<?php echo $al['imagen']; ?>" style="width:40px;height:40px;border-radius:4px;object-fit:cover">
                                <?php else: ?>Sin imagen<?php endif; ?></td>
                            <td><?php echo $al['nombre']; ?></td>
                            <td><?php echo $al['anio']; ?></td>
                            <td><span class="badge <?php echo $al['activo'] ? 'badge-activo':'badge-inactivo'; ?>">
                                <?php echo $al['activo'] ? 'Activo':'Inhabilitado'; ?></span></td>
                            <td>
                                <a href="../procesar/toggle_estado.php?tabla=albumes&id=<?php echo $al['id']; ?>&estado=<?php echo $al['activo']; ?>&redirect=admin/usuarios.php?tab=albumes"
                                   class="btn-toggle <?php echo $al['activo'] ? 'btn-inhabilitar':'btn-habilitar'; ?>">
                                    <?php echo $al['activo'] ? 'Inhabilitar':'Habilitar'; ?>
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <?php endwhile; ?>

            <?php elseif ($tab == 'autores'): ?>
            <!-- ===== ARTISTAS ===== -->
            <h3>Artistas Registrados</h3>
            <table class="tabla-admin">
                <thead>
                    <tr><th>#</th><th>Imagen</th><th>Nombre</th><th>Albums</th><th>Canciones</th><th>Estado</th><th>Accion</th></tr>
                </thead>
                <tbody>
                    <?php while ($ar = mysqli_fetch_assoc($autores)):
                        $num_albums   = mysqli_fetch_row(mysqli_query($conexion, "SELECT COUNT(*) FROM albumes WHERE id_autor='{$ar['id']}'"))[0];
                        $num_canciones = mysqli_fetch_row(mysqli_query($conexion, "SELECT COUNT(*) FROM canciones WHERE id_autor='{$ar['id']}'"))[0];
                    ?>
                    <tr>
                        <td><?php echo $ar['id']; ?></td>
                        <td><?php if ($ar['imagen']): ?>
                            <img src="../assets/img/<?php echo $ar['imagen']; ?>" style="width:40px;height:40px;border-radius:50%;object-fit:cover">
                            <?php else: ?>Sin imagen<?php endif; ?></td>
                        <td><?php echo $ar['nombre']; ?></td>
                        <td><?php echo $num_albums; ?> albums</td>
                        <td><?php echo $num_canciones; ?> canciones</td>
                        <td><span class="badge <?php echo $ar['activo'] ? 'badge-activo':'badge-inactivo'; ?>">
                            <?php echo $ar['activo'] ? 'Activo':'Inhabilitado'; ?></span></td>
                        <td>
                            <a href="../procesar/toggle_estado.php?tabla=autores&id=<?php echo $ar['id']; ?>&estado=<?php echo $ar['activo']; ?>&redirect=admin/usuarios.php?tab=autores"
                               class="btn-toggle <?php echo $ar['activo'] ? 'btn-inhabilitar':'btn-habilitar'; ?>">
                                <?php echo $ar['activo'] ? 'Inhabilitar':'Habilitar'; ?>
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <?php endif; ?>

        </div>
    </div>
</body>
</html>