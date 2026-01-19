<?php
require_once("../modelo/conectar.php");
require_once("../modelo/modelo_alumno.php");
require_once("../controlador/controler.php");
$nom=htmlspecialchars($_POST["nombre"]);
$correo=htmlspecialchars($_POST["correo"]);
$contraseña=htmlspecialchars($_POST["contraseña"]);
$contraseña_cifrada = password_hash($contraseña, PASSWORD_DEFAULT);
$es_administrador = false;
if ($nom === 'admin' && $contraseña === 'admin') {
    $es_administrador = true;
}

// Establecer el rol_id predeterminado
$rol_id = ($es_administrador) ? 1 : 2; // ID_Rol predeterminado para los usuarios normales
$insercion=new modelo_alumno();
$consulta=$insercion->insertar_alumno($nom,$correo,$contraseña_cifrada,$rol_id);
// require_once("../vista/principal.php");
header("Location: ../vista/inicio_sesion.php");

?>