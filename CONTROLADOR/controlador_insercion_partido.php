<?php
require_once("../modelo/conectar.php");
require_once("../modelo/modelo_alumno.php");
require_once("../controlador/controler.php");
$equipo_local=htmlspecialchars($_POST["equipo_local"]);
$equipo_visitante=htmlspecialchars($_POST["equipo_visitante"]);
$fecha=htmlspecialchars($_POST["fecha"]);
$insercion=new modelo_alumno();
$consulta=$insercion->insertar_partido($fecha, $equipo_local, $equipo_visitante);
// require_once("../vista/principal.php");
header("Location: ../vista/panelAdmin.php");

?>