<?php
require_once("../modelo/conectar.php");
require_once("../modelo/modelo_alumno.php");
require_once("../controlador/controler.php");
$nom=htmlspecialchars($_POST["name"]);
$id_equipo=htmlspecialchars($_POST["id_equipo"]);
$dorsal=htmlspecialchars($_POST["dorsal"]);
$posicion=htmlspecialchars($_POST["posicion"]);
$insercion=new modelo_alumno();
$consulta=$insercion->insertar_jugador($id_equipo, $nom, $dorsal, $posicion);
// require_once("../vista/principal.php");
header("Location: ../vista/panelUsuario.php");

?>