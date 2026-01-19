<?php
require_once("../modelo/conectar.php");
require_once("../modelo/modelo_alumno.php");
require_once("../controlador/controler.php");

$nom = htmlspecialchars($_POST["nombre_equipo"]);
$descripcion = htmlspecialchars($_POST["descripcion"]);
$id_equipo = htmlspecialchars($_POST["id_equipo"]);
$ruta_foto = '';

if ($_FILES['foto_equipo']['error'] == UPLOAD_ERR_OK) {
    $foto_equipo = $_FILES['foto_equipo']['name'];
    $tmp_name = $_FILES['foto_equipo']['tmp_name'];
    $uploads_dir = '../uploads/equipos'; // Asegúrate de que este directorio existe y tiene permisos de escritura
    $ruta_foto = $uploads_dir . '/' . basename($foto_equipo);
    
    if (!move_uploaded_file($tmp_name, $ruta_foto)) {
        die("Error al subir la foto del equipo.");
    }
} else {
    die("Error en la subida de la foto del equipo.");
}

$insercion = new modelo_alumno();
$consulta = $insercion->insertar_equipo($id_equipo, $nom, $descripcion, $ruta_foto);

header("Location: ../vista/panelUsuario.php");
?>
