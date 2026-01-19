<?php
session_start();
require_once("../modelo/conectar.php");

// Obtener una instancia de la conexión a la base de datos
$conexion = Conectar::conexion();

// Consultar todos los equipos
$sql_equipos = "SELECT * FROM equipo";
$stmt_equipos = $conexion->prepare($sql_equipos);
if (!$stmt_equipos->execute()) {
    die("Error al ejecutar la consulta de equipos: " . $stmt_equipos->errorInfo()[2]);
}
$equipos = $stmt_equipos->fetchAll(PDO::FETCH_ASSOC);

// Consultar todos los jugadores
$sql_jugadores = "SELECT * FROM jugadores";
$stmt_jugadores = $conexion->prepare($sql_jugadores);
if (!$stmt_jugadores->execute()) {
    die("Error al ejecutar la consulta de jugadores: " . $stmt_jugadores->errorInfo()[2]);
}
$jugadores = $stmt_jugadores->fetchAll(PDO::FETCH_ASSOC);

// Consultar todos los partidos
$sql_partidos = "
SELECT p.id, p.equipo_local, p.equipo_visitante, p.fecha, e1.Foto AS foto_local, e2.foto AS foto_visitante
FROM partidos p
JOIN equipo e1 ON p.equipo_local = e1.Nombre_Equipo
JOIN equipo e2 ON p.equipo_visitante = e2.Nombre_Equipo
";
$stmt_partidos = $conexion->prepare($sql_partidos);
if (!$stmt_partidos->execute()) {
    die("Error al ejecutar la consulta de partidos: " . $stmt_partidos->errorInfo()[2]);
}
$partidos = $stmt_partidos->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrador - SoccerMatchUp</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,700">
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #1a1a1a;
            font-family: 'Source Sans Pro', sans-serif;
            margin: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            color: white;
        }

        .sidebar {
            width: 250px;
            background-color: #1a1a1a;
            color: white;
            height: 100vh;
            padding: 20px;
            box-sizing: border-box;
            position: fixed;
            top: 0;
            left: 0;
        }

        .sidebar h2 {
            text-align: center;
        }

        .sidebar ul {
            list-style-type: none;
            padding: 0;
        }

        .sidebar ul li {
            margin: 20px 0;
        }

        .sidebar ul li a {
            color: white;
            text-decoration: none;
            display: block;
            padding: 10px;
            border-radius: 4px;
            border: 1px solid orange;
        }

        .sidebar ul li a:hover {
            background-color: orange;
        }

        .main-content {
            padding: 20px;
            background-color: #1a1a1a;
            flex-grow: 1;
            margin-left: 250px;
            box-sizing: border-box;
            color: white;
        }

        .header-black {
            background: #1a1a1a;
            padding: 0;
            margin: 0;
            width: 100%;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
        }

        .header-black .navbar {
            background: #1a1a1a;
            color: #ffffff;
            border-radius: 0;
            box-shadow: none;
            border: none;
            margin: 0;
            padding: 0;
            width: 100%;
        }

        .header-black .navbar .container {
            padding: 0 20px;
        }

        .header-black .navbar .navbar-brand {
            font-weight: bold;
            color: inherit;
            display: flex;
            align-items: center;
        }

        .header-black .navbar .navbar-brand:hover {
            color: #fddb3a;
        }

        .header-black .navbar .navbar-collapse {
            padding: 0;
            margin: 0;
        }

        .header-black .navbar .navbar-collapse .ml-auto {
            margin-left: auto;
        }

        .header-black .navbar .navbar-collapse span .login {
            color: #d9d9d9;
            margin-right: .5rem;
            text-decoration: none;
            border: 1px solid rgba(255, 255, 255, 0.7);
            border-radius: 40px;
            padding: .3rem .8rem;
        }

        .header-black .navbar .navbar-toggler {
            border-color: rgba(255, 255, 255, 0.3);
        }

        .header-black .navbar .navbar-toggler:hover,
        .header-black .navbar-toggler:focus {
            background: none;
        }

        .header-black .action-button,
        .header-black .action-button:not(.disabled):active {
            border: 1px solid rgba(255, 255, 255, 0.7);
            border-radius: 40px;
            color: #ebeff1;
            box-shadow: none;
            text-shadow: none;
            padding: .3rem .8rem;
            background: transparent;
            transition: background-color 0.25s;
            outline: none;
        }

        .header-black .action-button:hover {
            background-color: orange;
            color: #1a1a1a;
        }

        footer {
            text-align: center;
            padding: 20px 0;
            background-color: #1a1a1a;
            color: #fff;
            position: relative;
            width: 100%;
        }

        .banner {
            background-image: "uploads/equipos/descarga.jpg";
        }

        h5 {
            color: orange !important;
        }

        .pagination .page-link {
            color: orange;
            background-color: transparent;
            border: 1px solid orange;
        }

        .pagination .page-item.active .page-link {
            background-color: orange;
            border-color: orange;
        }

        .page-link:hover {
            background-color: orange;
            border-color: orange;
            color: #1a1a1a;
        }
        
        .page-item {
            margin-top: 20px;
        }
        
        .text-orange {
            color: orange;
        }
        
        .list-group-item {
            display: block;
        }
        
        .partido-table {
            display: table;
            width: 100%;
        }
        
        .equipo-local, .equipo-visitante, .fecha {
            display: table-cell;
            vertical-align: middle;
        }
        
        .equipo-local, .equipo-visitante {
            width: 40%;
        }
        
        .fecha {
            width: 20%;
            text-align: right;
        }
        
        .equipo-img {
            width: 50px;
            height: 50px;
            margin-right: 10px;
        }
        
        .equipo-local span, .equipo-visitante span {
            display: inline-block;
            vertical-align: middle;
        }
        
        .navbar-text {
            margin-right: 10px;
        }
        
        table {
            background-color: #2a2a2a;
            color: white;
            margin-top: 20px;
        }
        
        th {
            background-color: #333333;
            color: orange;
        }
        
        td {
            background-color: #444444;
        }
        
        .form-control {
            background-color: #333333;
            color: white;
            border: 1px solid orange;
        }
        
        .btn-primary {
            background-color: orange;
            border-color: orange;
            color: #1a1a1a;
        }
        
        .btn-primary:hover {
            background-color: #ff9800;
            border-color: #ff9800;
        }
        .partido-table {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.equipo-local, .equipo-visitante {
    display: flex;
    align-items: center;
}

.equipo-img {
    width: 50px;
    height: 50px;
    margin-right: 10px;
}

.fecha {
    margin-left: auto;
    margin-right: 20px;
}

.boton {
    margin-left: 20px;
}

/* Ajuste de estilos para las clases específicas */
.equipo-local span, .equipo-visitante span {
    display: inline-block;
    vertical-align: middle;
}
@media (max-width: 767.98px) {
    .sidebar {
        width: 100%;
        height: auto;
        position: relative;
    }
    .custom-margin {
            margin-top: 0 !important; /* o cualquier valor que desees para dispositivos móviles */
        }

    .main-content {
        margin-left: 0;
    }

    .header-black .navbar .navbar-collapse {
        margin: 0 auto;
    }

    .header-black .navbar .navbar-brand {
        margin-left: auto;
        margin-right: auto;
    }

    .header-black .navbar .navbar-collapse .ml-auto {
        margin-left: 0;
    }
    .partido-table {
        flex-direction: column;
        align-items: flex-start;
    }

    .fecha,
    .boton {
        text-align: left;
        width: 100%;
    }

    .boton {
        text-align: left;
    }
}
.table-responsive {
    display: block;
    width: 100%;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch; /* Para un mejor desplazamiento en dispositivos móviles */
}

.table-responsive > .table {
    margin-bottom: 0;
}
    </style>
</head>
<body>
    <div class="sidebar">
    <img src="../uploads/equipos/logo.png" alt="Logo" style="height: 200px; margin-right: 10px;" class="imagen">

        <ul>
            <li><a href="principal_iniciado_admin.php">Inicio</a></li>
            <li><a href="#equipos">Info Admin</a></li>
            <li><a href="../VISTA/principal.php">Cerrar Sesión</a></li>
        </ul>
    </div>
    <div class="header">
        <div class="header-black">
            <nav class="navbar navbar-dark navbar-expand-md navigation-clean-search">
                <div class="container">
                    <img src="../uploads/equipos/logo.png" alt="Logo" style="height: 200px;" class="imagen">
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navcol-1" aria-controls="navcol-1" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navcol-1">
                        <div class="ml-auto d-flex align-items-center">
                            <span class="navbar-text">Bienvenido, <?php echo $_SESSION['nombre']; ?>(Administrador)</span>
                            <a class="btn btn-light action-button nav-item" role="button" href="../VISTA/principal.php">Cerrar Sesión</a>
                        </div>
                    </div>
                </div>
            </nav>
        </div>
    </div>

    <div class="main-content">
        <div class="container custom-margin" style="margin-top: 202px;">
            <h1 class="text-center">Panel de Administración</h1>
            <div class="col-sm-12 col-md-12 mt-2">
    <div class="card bg-dark text-white">
        <div class="card-body">
            <h5 class="card-title">Lista de Equipos</h5>
            <ul class="list-group">
                <?php foreach ($equipos as $equipo): ?>
                    <li class="list-group-item bg-dark text-white">
                        <div class="partido-table">
                            <div class="equipo-local">
                                <?php if ($equipo['Foto']): ?>
                                    <img src="<?= htmlspecialchars($equipo['Foto']) ?>" alt="Escudo de <?= htmlspecialchars($equipo['Nombre_Equipo']) ?>" class="equipo-img">
                                <?php endif; ?>
                                <span><?php echo $equipo['Nombre_Equipo']; ?></span>
                            </div>
                            <div>
                            <a href="../controlador/controlador_editar_equipo.php?id_equipo=<?= $equipo['id_equipo'] ?>"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="orange" class="bi bi-pen" viewBox="0 0 16 16"><path d="m13.498.795.149-.149a1.207 1.207 0 1 1 1.707 1.708l-.149.148a1.5 1.5 0 0 1-.059 2.059L4.854 14.854a.5.5 0 0 1-.233.131l-4 1a.5.5 0 0 1-.606-.606l1-4a.5.5 0 0 1 .131-.232l9.642-9.642a.5.5 0 0 0-.642.056L6.854 4.854a.5.5 0 1 1-.708-.708L9.44.854A1.5 1.5 0 0 1 11.5.796a1.5 1.5 0 0 1 1.998-.001m-.644.766a.5.5 0 0 0-.707 0L1.95 11.756l-.764 3.057 3.057-.764L14.44 3.854a.5.5 0 0 0 0-.708z"/></svg></a><a href="../controlador/controlador_borrar_equipo.php?id_Equipo=<?= $equipo['id_equipo'] ?>"><svg xmlns="http://www.w3.org/2000/svg" width="16" style="
    margin-left: 9px;" height="16" fill="orange" class="bi bi-trash" viewBox="0 0 16 16"><path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z"/><path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z"/></svg></a>

                            </div>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>

            <h2 id="jugadores" class="text-center">Jugadores</h2>
<div class="col-sm-12 col-md-12 mt-2">
    <div class="card bg-dark text-white">
        <div class="card-body">
            <h5 class="card-title">Lista de Jugadores</h5>
            <div class="table-responsive">
                <table class="table table-dark">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Dorsal</th>
                            <th>Posición</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($jugadores as $jugador): ?>
                            <tr>
                                <td><?php echo $jugador['nombre']; ?></td>
                                <td><?php echo $jugador['dorsal']; ?></td>
                                <td><?php echo $jugador['posicion']; ?></td>
                                <td>
                                <a href="../controlador/controlador_editar_jugador_admin.php?ID_Jugador=<?= $jugador['ID_Jugador'] ?>"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="orange" class="bi bi-pen" viewBox="0 0 16 16"><path d="m13.498.795.149-.149a1.207 1.207 0 1 1 1.707 1.708l-.149.148a1.5 1.5 0 0 1-.059 2.059L4.854 14.854a.5.5 0 0 1-.233.131l-4 1a.5.5 0 0 1-.606-.606l1-4a.5.5 0 0 1 .131-.232l9.642-9.642a.5.5 0 0 0-.642.056L6.854 4.854a.5.5 0 1 1-.708-.708L9.44.854A1.5 1.5 0 0 1 11.5.796a1.5 1.5 0 0 1 1.998-.001m-.644.766a.5.5 0 0 0-.707 0L1.95 11.756l-.764 3.057 3.057-.764L14.44 3.854a.5.5 0 0 0 0-.708z"/></svg></a>
                                <span>&nbsp;</span>
                                <a href="../controlador/controlador_borrar_jugador_admin.php?ID_Jugador=<?= $jugador['ID_Jugador'] ?>"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="orange" class="bi bi-trash" viewBox="0 0 16 16"><path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z"/><path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z"/></svg></a>
</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

            <h2 id="partidos" class="text-center">Partidos</h2>
            <div class="col-sm-12 col-md- mt-2">
            <div class="card bg-dark text-white">
    <div class="card-body">
        <h5 class="card-title">Próximos Partidos</h5>
        <ul class="list-group">
            <?php foreach ($partidos as $partido) { 
                $fecha_formateada = date('d-m-Y', strtotime($partido['fecha']));
            ?>
                <li class="list-group-item bg-dark text-white">
                    <div class="partido-table">
                        <div class="equipo-local">
                            <img src="<?php echo $partido['foto_local']; ?>" alt="<?php echo $partido['equipo_local']; ?>" class="equipo-img">
                            <span><?php echo $partido['equipo_local']; ?></span>
                        </div>
                        <div class="equipo-visitante">
                            <img src="<?php echo $partido['foto_visitante']; ?>" alt="<?php echo $partido['equipo_visitante']; ?>" class="equipo-img">
                            <span><?php echo $partido['equipo_visitante']; ?></span>
                        </div>
                        <div class="fecha"><?php echo $fecha_formateada; ?></div>
                        <div class="boton">
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#resultadoModal<?php echo $partido['id']; ?>">Resultado</button>
                        </div>
                    </div>
                </li>

                <div class="modal fade" id="resultadoModal<?php echo $partido['id']; ?>" tabindex="-1" aria-labelledby="resultadoModalLabel<?php echo $partido['id']; ?>" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="resultadoModalLabel<?php echo $partido['id']; ?>">Añadir Resultado</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form action="../controlador/controlador_guardar_resultado.php" method="POST">
                                    <input type="hidden" name="partido_id" value="<?php echo $partido['id']; ?>">
                                    <div class="form-group">
                                        <label for="goles_local<?php echo $partido['id']; ?>">Goles Equipo Local</label>
                                        <input type="number" class="form-control" id="goles_local<?php echo $partido['id']; ?>" name="goles_local" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="goles_visitante<?php echo $partido['id']; ?>">Goles Equipo Visitante</label>
                                        <input type="number" class="form-control" id="goles_visitante<?php echo $partido['id']; ?>" name="goles_visitante" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Guardar Resultado</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </ul>
    </div>
</div>

            <h2 class="text-center">Añadir Partido</h2>
            <form action="../controlador/controlador_insercion_partido.php" method="POST">
                <div class="form-group">
                    <label for="equipo_local">Equipo Local:</label>
                    <select name="equipo_local" id="equipo_local" class="form-control">
                        <?php foreach ($equipos as $equipo): ?>
                            <option value="<?php echo $equipo['Nombre_Equipo']; ?>"><?php echo $equipo['Nombre_Equipo']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="equipo_visitante">Equipo Visitante:</label>
                    <select name="equipo_visitante" id="equipo_visitante" class="form-control">
                        <?php foreach ($equipos as $equipo): ?>
                            <option value="<?php echo $equipo['Nombre_Equipo']; ?>"><?php echo $equipo['Nombre_Equipo']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="fecha">Fecha:</label>
                    <input type="datetime-local" name="fecha" id="fecha" class="form-control">
                </div>
                <button type="submit" class="btn btn-primary" >Añadir Partido</button>
            </form>
        </div>
    </div>
    <footer>
        <p>&copy; 2024 SoccerMatchUp. Todos los derechos reservados.</p>
        <section class="mb-4">
            <!-- Facebook -->
            <a class="btn btn-link btn-floating btn-lg m-1" href="#!" role="button" data-mdb-ripple-color="dark"><i class="fab fa-facebook-f text-orange"></i></a>
            <!-- Twitter -->
            <a class="btn btn-link btn-floating btn-lg m-1" href="#!" role="button" data-mdb-ripple-color="dark"><i class="fab fa-twitter text-orange"></i></a>
            <!-- Google -->
            <a class="btn btn-link btn-floating btn-lg m-1" href="#!" role="button" data-mdb-ripple-color="dark"><i class="fab fa-google text-orange"></i></a>
            <!-- Instagram -->
            <a class="btn btn-link btn-floating btn-lg m-1" href="#!" role="button" data-mdb-ripple-color="dark"><i class="fab fa-instagram text-orange"></i></a>
            <!-- Linkedin -->
            <a class="btn btn-link btn-floating btn-lg m-1" href="#!" role="button" data-mdb-ripple-color="dark"><i class="fab fa-linkedin text-orange"></i></a>
            <!-- Github -->
            <a class="btn btn-link btn-floating btn-lg m-1" href="#!" role="button" data-mdb-ripple-color="white"><i class="fab fa-github text-orange"></i></a>
        </section>
    </footer>


    
</body>
</html>
