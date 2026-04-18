<?php
session_start();
require_once '../config/conexion.php';

if (!isset($_SESSION['usuario']) || $_SESSION['rol'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

$usuarios  = mysqli_query($conexion, "SELECT * FROM usuarios WHERE rol = 'cliente' ORDER BY id DESC");
$canciones = mysqli_query($conexion, "SELECT c.*, au.nombre AS autor FROM canciones c LEFT JOIN autores au ON c.id_autor = au.id ORDER BY c.id DESC");
$albumes   = mysqli_query($conexion, "SELECT a.*, au.nombre AS autor FROM albumes a LEFT JOIN autores au ON a.id_autor = au.id ORDER BY a.id DESC");
$autores   = mysqli_query($conexion, "SELECT * FROM autores ORDER BY id DESC");
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

        <?php if (isset($_GET['exito'])) echo "<div class='exito'>" . $_GET['exito'] . "</div>"; ?>

        <!-- USUARIOS -->
        <h2>Gestion de Usuarios</h2>
        <table class="tabla-admin">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Rol</th>
                    <th>Estado</th>
                    <th>Accion</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($u = mysqli_fetch_assoc($usuarios)): ?>
                <tr>
                    <td><?php echo $u['id']; ?></td>
                    <td><?php echo $u['nombre']; ?></td>
                    <td><?php echo $u['email']; ?></td>
                    <td><?php echo $u['rol']; ?></td>
                    <td>
                        <span class="badge <?php echo $u['activo'] ? 'badge-activo' : 'badge-inactivo'; ?>">
                            <?php echo $u['activo'] ? 'Activo' : 'Inhabilitado'; ?>
                        </span>
                    </td>
                    <td>
                        <a href="../procesar/toggle_estado.php?tabla=usuarios&id=<?php echo $u['id']; ?>&estado=<?php echo $u['activo']; ?>&redirect=admin/usuarios.php"
                           class="btn-toggle <?php echo $u['activo'] ? 'btn-inhabilitar' : 'btn-habilitar'; ?>">
                            <?php echo $u['activo'] ? 'Inhabilitar' : 'Habilitar'; ?>
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <!-- CANCIONES -->
        <h2 style="margin-top:40px">Gestion de Canciones</h2>
        <table class="tabla-admin">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nombre</th>
                    <th>Autor</th>
                    <th>Estado</th>
                    <th>Accion</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($c = mysqli_fetch_assoc($canciones)): ?>
                <tr>
                    <td><?php echo $c['id']; ?></td>
                    <td><?php echo $c['nombre']; ?></td>
                    <td><?php echo $c['autor']; ?></td>
                    <td>
                        <span class="badge <?php echo $c['activo'] ? 'badge-activo' : 'badge-inactivo'; ?>">
                            <?php echo $c['activo'] ? 'Activo' : 'Inhabilitado'; ?>
                        </span>
                    </td>
                    <td>
                        <a href="../procesar/toggle_estado.php?tabla=canciones&id=<?php echo $c['id']; ?>&estado=<?php echo $c['activo']; ?>&redirect=admin/usuarios.php"
                           class="btn-toggle <?php echo $c['activo'] ? 'btn-inhabilitar' : 'btn-habilitar'; ?>">
                            <?php echo $c['activo'] ? 'Inhabilitar' : 'Habilitar'; ?>
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <!-- ALBUMES -->
        <h2 style="margin-top:40px">Gestion de Albums</h2>
        <table class="tabla-admin">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nombre</th>
                    <th>Autor</th>
                    <th>Estado</th>
                    <th>Accion</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($al = mysqli_fetch_assoc($albumes)): ?>
                <tr>
                    <td><?php echo $al['id']; ?></td>
                    <td><?php echo $al['nombre']; ?></td>
                    <td><?php echo $al['autor']; ?></td>
                    <td>
                        <span class="badge <?php echo $al['activo'] ? 'badge-activo' : 'badge-inactivo'; ?>">
                            <?php echo $al['activo'] ? 'Activo' : 'Inhabilitado'; ?>
                        </span>
                    </td>
                    <td>
                        <a href="../procesar/toggle_estado.php?tabla=albumes&id=<?php echo $al['id']; ?>&estado=<?php echo $al['activo']; ?>&redirect=admin/usuarios.php"
                           class="btn-toggle <?php echo $al['activo'] ? 'btn-inhabilitar' : 'btn-habilitar'; ?>">
                            <?php echo $al['activo'] ? 'Inhabilitar' : 'Habilitar'; ?>
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <!-- AUTORES -->
        <h2 style="margin-top:40px">Gestion de Artistas</h2>
        <table class="tabla-admin">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nombre</th>
                    <th>Estado</th>
                    <th>Accion</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($ar = mysqli_fetch_assoc($autores)): ?>
                <tr>
                    <td><?php echo $ar['id']; ?></td>
                    <td><?php echo $ar['nombre']; ?></td>
                    <td>
                        <span class="badge <?php echo $ar['activo'] ? 'badge-activo' : 'badge-inactivo'; ?>">
                            <?php echo $ar['activo'] ? 'Activo' : 'Inhabilitado'; ?>
                        </span>
                    </td>
                    <td>
                        <a href="../procesar/toggle_estado.php?tabla=autores&id=<?php echo $ar['id']; ?>&estado=<?php echo $ar['activo']; ?>&redirect=admin/usuarios.php"
                           class="btn-toggle <?php echo $ar['activo'] ? 'btn-inhabilitar' : 'btn-habilitar'; ?>">
                            <?php echo $ar['activo'] ? 'Inhabilitar' : 'Habilitar'; ?>
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

    </div>
</body>
</html>