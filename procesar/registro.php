<?php
session_start();
require_once '../config/conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre   = mysqli_real_escape_string($conexion, $_POST['nombre']);
    $email    = mysqli_real_escape_string($conexion, $_POST['email']);
    $password = MD5($_POST['password']);
    $rol      = $_POST['rol'] == 'admin' ? 'admin' : 'cliente';

    $verificar = mysqli_query($conexion, "SELECT id FROM usuarios WHERE email = '$email'");
    if (mysqli_num_rows($verificar) > 0) {
        header("Location: ../registro.php?error=El correo ya esta registrado");
        exit();
    }

    $sql = "INSERT INTO usuarios (nombre, email, password, rol, activo)
            VALUES ('$nombre', '$email', '$password', '$rol', 1)";

    if (mysqli_query($conexion, $sql)) {
        header("Location: ../index.php?exito=Cuenta creada exitosamente");
    } else {
        header("Location: ../registro.php?error=Error al registrar");
    }
    exit();
}
?>