<?php
session_start();
require_once("../modelo/conectar.php");

// Verificar que se haya enviado el formulario

    // Obtener los datos del formulario
    $id_partido = $_POST['partido_id'];
    $goles_local = $_POST['goles_local'];
    $goles_visitante = $_POST['goles_visitante'];

    // Obtener una instancia de la conexión a la base de datos
    $conexion = Conectar::conexion();

    // Insertar el resultado en la tabla
    $sql_insertar_resultado = "
        INSERT INTO resultados (id_partido, goles_local, goles_visitante)
        VALUES (:id_partido, :goles_local, :goles_visitante)
    ";
    $stmt_insertar_resultado = $conexion->prepare($sql_insertar_resultado);
    $stmt_insertar_resultado->bindParam(':id_partido', $id_partido, PDO::PARAM_INT);
    $stmt_insertar_resultado->bindParam(':goles_local', $goles_local, PDO::PARAM_INT);
    $stmt_insertar_resultado->bindParam(':goles_visitante', $goles_visitante, PDO::PARAM_INT);

    if ($stmt_insertar_resultado->execute()) {
        $_SESSION['mensaje'] = "Resultado insertado exitosamente";
    } else {
        $_SESSION['mensaje'] = "Error al insertar el resultado: " . $stmt_insertar_resultado->errorInfo()[2];
    }

    // Redirigir de vuelta a la página de administración
    header("Location: ../vista/panelAdmin.php");
    exit;

?>

