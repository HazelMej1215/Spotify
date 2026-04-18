<?php
session_start();
require_once '../config/conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email    = mysqli_real_escape_string($conexion, $_POST['email']);
    $password = MD5($_POST['password']);

    $sql = "SELECT * FROM usuarios WHERE email = '$email' AND password = '$password'";
    $resultado = mysqli_query($conexion, $sql);

    if (mysqli_num_rows($resultado) == 1) {
        $usuario = mysqli_fetch_assoc($resultado);

        // Verificar si esta inhabilitado
        if ($usuario['activo'] == 0) {
            header("Location: ../index.php?error=Tu cuenta ha sido inhabilitada");
            exit();
        }

        $_SESSION['usuario']    = $usuario['nombre'];
        $_SESSION['id_usuario'] = $usuario['id'];
        $_SESSION['rol']        = $usuario['rol'];

        if ($usuario['rol'] == 'admin') {
            header("Location: ../admin/panel.php");
        } else {
            header("Location: ../cliente/inicio.php");
        }
        exit();
    } else {
        header("Location: ../index.php?error=Correo o contrasena incorrectos");
        exit();
    }
}
?>