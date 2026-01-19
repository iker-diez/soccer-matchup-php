<?php
require_once("../modelo/conectar.php");

$conexion = Conectar::conexion();

// Verificar si el parámetro ID_Jugador está presente en la URL
if (isset($_GET["ID_Jugador"])) {
    $id = $_GET["ID_Jugador"];

    // Obtener los datos del jugador
    $sql = "SELECT * FROM jugadores WHERE ID_Jugador = :id";
    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $jugador = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verificar si el jugador existe
    if (!$jugador) {
        echo "Jugador no encontrado.";
        exit();
    }
} else {
    echo "ID_Jugador no especificado.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Jugador</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #ff7f00;
          background: linear-gradient(to right, #ff6200, #ffae33);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        h1 {
            color: #333;
        }
        form {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #333;
        }
        input[type="text"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button {
            background-color: orange;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }
      
    </style>
</head>
<body>
    <form action="controlador_actualizar_jugador_admin.php" method="post">
        <h1>Editar jugador </h1>
        <input type="hidden" name="ID_Jugador" value="<?= htmlspecialchars($jugador['ID_Jugador']) ?>">
        <label for="nombre">Nombre:</label>
        <input type="text" name="nombre" id="nombre" value="<?= htmlspecialchars($jugador['nombre']) ?>" required>
        <label for="dorsal">Dorsal:</label>
        <input type="text" name="dorsal" id="dorsal" value="<?= htmlspecialchars($jugador['dorsal']) ?>" required>
        <label for="posicion">Posición:</label>
        <input type="text" name="posicion" id="posicion" value="<?= htmlspecialchars($jugador['posicion']) ?>" required>
        <button type="submit">Actualizar</button>
    </form>
</body>
</html>

