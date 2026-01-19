<?php
require_once("../modelo/conectar.php");

$conexion = Conectar::conexion();

// Verificar si el parámetro ID_Jugador está presente en la URL
if (isset($_GET["ID_Jugador"])) {
    // Obtener el valor del parámetro ID_Jugador de la URL
    $id = $_GET["ID_Jugador"];

    // Preparar y ejecutar la consulta SQL de eliminación
    $sql = "DELETE FROM jugadores WHERE ID_Jugador = :id";
    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    // Redirigir al usuario a la página panelUsuario.php después de la eliminación
    header("Location: ../vista/panelAdmin.php");
    exit();
} else {
    echo "ID_Jugador no especificado.";
}
?>

