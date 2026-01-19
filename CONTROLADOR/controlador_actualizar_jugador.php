<?php
require_once("../modelo/conectar.php");

$conexion = Conectar::conexion();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificar si todos los campos requeridos están presentes
    if (isset($_POST["ID_Jugador"]) && isset($_POST["nombre"]) && isset($_POST["dorsal"]) && isset($_POST["posicion"])) {
        $id = $_POST["ID_Jugador"];
        $nombre = $_POST["nombre"];
        $dorsal = $_POST["dorsal"];
        $posicion = $_POST["posicion"];

        // Preparar y ejecutar la consulta de actualización
        $sql = "UPDATE jugadores SET nombre = :nombre, dorsal = :dorsal, posicion = :posicion WHERE ID_Jugador = :id";
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        $stmt->bindParam(':dorsal', $dorsal, PDO::PARAM_INT);
        $stmt->bindParam(':posicion', $posicion, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            // Redirigir al usuario a la página panelUsuario.php después de la actualización
            header("Location: ../vista/panelUsuario.php");
            exit();
        } else {
            echo "Error al actualizar el jugador.";
        }
    } else {
        echo "Todos los campos son requeridos.";
    }
} else {
    echo "Método de solicitud no válido.";
}
?>
