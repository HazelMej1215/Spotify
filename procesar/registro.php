<?php
session_start();
require_once '../config/conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre   = mysqli_real_escape_string($conexion, $_POST['nombre']);
    $email    = mysqli_real_escape_string($conexion, $_POST['email']);
    $password = MD5($_POST['password']);

    // Verificar si el email ya existe
    $verificar = mysqli_query($conexion, "SELECT id FROM usuarios WHERE email = '$email'");
    if (mysqli_num_rows($verificar) > 0) {
        header("Location: ../registro.php?error=El correo ya está registrado");
        exit();
    }

    $sql = "INSERT INTO usuarios (nombre, email, password, rol)
            VALUES ('$nombre', '$email', '$password', 'cliente')";

    if (mysqli_query($conexion, $sql)) {
        header("Location: ../index.php?exito=Cuenta creada exitosamente");
    } else {
        header("Location: ../registro.php?error=Error al registrar");
    }
    exit();
}
?>