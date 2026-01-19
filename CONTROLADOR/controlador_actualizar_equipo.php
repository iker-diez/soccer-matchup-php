<?php
require_once("../modelo/conectar.php");

$conexion = Conectar::conexion();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_equipo = $_POST['ID_Equipo'];
    $nombre_equipo = $_POST['Nombre_Equipo'];
    $foto_equipo = $_POST['Foto'];

    if (!empty($id_equipo) && !empty($nombre_equipo) && !empty($foto_equipo)) {
        $sql = "UPDATE equipo SET Nombre_Equipo = :nombre, Foto = :foto WHERE ID_Equipo = :id";
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':nombre', $nombre_equipo, PDO::PARAM_STR);
        $stmt->bindParam(':foto', $foto_equipo, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id_equipo, PDO::PARAM_INT);

        if ($stmt->execute()) {
            header('Location: ../vista/panelAdmin.php?mensaje=Equipo actualizado');
        } else {
            header('Location: ../vista/panelAdmin.php?mensaje=Error al actualizar equipo');
        }
    } else {
        header('Location: ../vista/panelAdmin.php?mensaje=Datos incompletos');
    }
} else {
    header('Location: ../vista/panelAdmin.php');
}
?>