<?php
require_once("../modelo/conectar.php");

$conexion = Conectar::conexion();

header('Content-Type: application/json');
var_dump($_GET['id']);die;
if (isset($_GET['id'])) {
    $id = $_GET['id'];
   
    $sql = "SELECT * FROM jugadores WHERE ID_Jugador = :id";
    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $jugador = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($jugador) {
        echo json_encode($jugador);
    } else {
        echo json_encode(['error' => 'Jugador no encontrado']);
    }
} else {
    echo json_encode(['error' => 'ID_Jugador no especificado']);
}
?>