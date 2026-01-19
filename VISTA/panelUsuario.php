<?php
session_start();
require_once("../modelo/conectar.php");

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['ID'])) {
    header("Location: ../vista/login.php");
    exit;
}

// Obtener una instancia de la conexión a la base de datos
$conexion = Conectar::conexion();

// Obtener el ID del usuario desde la sesión
$id_usuario = $_SESSION['ID'];

// Consulta SQL para obtener el ID del equipo del usuario
$sql = "SELECT id_equipo FROM alumno WHERE ID = :id_usuario";
$stmt = $conexion->prepare($sql);

if (!$stmt->execute([':id_usuario' => $id_usuario])) {
    die("Error al ejecutar la consulta: " . $stmt->errorInfo()[2]);
}

$id_equipo_usuario = $stmt->fetchColumn();

if ($id_equipo_usuario) {
    // Consulta SQL para verificar si el equipo existe en la tabla 'equipo'
    $sql = "SELECT * FROM equipo WHERE ID_Equipo = :id_equipo";
    $stmt = $conexion->prepare($sql);

    if (!$stmt->execute([':id_equipo' => $id_equipo_usuario])) {
        die("Error al ejecutar la consulta: " . $stmt->errorInfo()[2]);
    }

    $equipo = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($equipo) {
        $nombre_equipo = $equipo['Nombre_Equipo'];
        $foto_equipo = $equipo['Foto'];

        // Consulta SQL para obtener los jugadores del equipo del usuario
        $sql = "SELECT * FROM jugadores WHERE ID_Equipo = :id_equipo";
        $stmt = $conexion->prepare($sql);

        if (!$stmt->execute([':id_equipo' => $id_equipo_usuario])) {
            die("Error al ejecutar la consulta: " . $stmt->errorInfo()[2]);
        }

        $alumnos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Consulta SQL para obtener los partidos del equipo del usuario
        $sql = "SELECT * FROM partidos WHERE equipo_local = :nombre_equipo OR equipo_visitante = :nombre_equipo";
        $stmt = $conexion->prepare($sql);

        if (!$stmt->execute([':nombre_equipo' => $nombre_equipo])) {
            die("Error al ejecutar la consulta: " . $stmt->errorInfo()[2]);
        }

        $partidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $nombre_equipo = null;
    }
} else {
    $nombre_equipo = null;
}
$current_page = basename($_SERVER['PHP_SELF']);

   
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Untitled</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,700">
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
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
            background-color:  orange;
        }
        .sidebar ul li a.active,
        .sidebar ul li a:hover {
            background-color: orange;
        }

        .main-content {
            padding: 20px;
            background-color: #1a1a1a;
            flex-grow: 1;
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

        .header-black .navbar .navbar-collapse span .login:hover {
            color: #1a1a1a;
            background-color: orange;
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
        .page-item{
            margin-top:20px;
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
.navbar-text{
    margin-right:10px;
}
.custom-orange {
    background-color: orange;
    border-color: orange;
}

.custom-orange:hover {
    background-color: darkorange; /* Color al pasar el ratón por encima */
    border-color: darkorange;
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
                                <span class="navbar-text">Bienvenido, <?php echo $_SESSION['nombre']; ?>(Capitan)</span>
                                <a class="btn btn-light action-button nav-item" role="button" href="../VISTA/principal.php">Cerrar Sesión</a>
                           
                        </div>
                    </div>
                </div>
            </nav>
        </div>
    </div>


    <div class="main-content">

    <div class="sidebar">
<img src="../uploads/equipos/logo.png" alt="Logo" style="height: 200px; margin-right: 10px;" class="imagen">
        <ul>
            <li><a href="principal_iniciado.php" >Inicio</a></li>
            <li><a href="panelUsuario.php" >Mi equipo</a></li>
            <li><a href="partidos.php" >Mis Partidos</a></li>
        </ul>
    </div>
        <div class="container custom-margin" style="margin-top: 202px;">
            <?php if ($nombre_equipo): ?>
                <h1 class="text-center" style="color: orange">Información del equipo</h1>
                <div class="row">
                    <div class="col-md-6">
                        <h2 style=" margin-left: 15px"> <?php echo $nombre_equipo; ?></h2>
                        <?php if ($foto_equipo): ?>
                            <img   src="<?= htmlspecialchars($foto_equipo) ?>" alt="Escudo de <?= htmlspecialchars($nombre_equipo) ?>" class="img-thumbnail" style="max-width: 100px; margin-left:15px;" />
                        <?php endif; ?>
                    </div>
                </div>

                <div class="col-sm-12 col-md-12 mt-2">
    <div class="card bg-dark text-white">
        <div class="card-body">
            <h5 class="card-title">Jugadores  <a style="color: white; margin-left:15px;" href="#" class="btn btn-warning custom-orange" data-bs-toggle="modal" data-bs-target="#dlgContact1">Añadir jugador</a></h5>
            
            <div class="table-responsive">
    <table class="table table-dark table-striped">
        <thead>
            <tr>
                <th>Dorsal</th>
                <th>Nombre</th>
                <th>Posicion</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($alumnos as $alumno): ?>
                <tr>
                    <td><?php echo $alumno['dorsal']; ?></td>
                    <td><?php echo $alumno['nombre']; ?></td>
                    <td><?php echo $alumno['posicion']; ?></td>
                    <td>
                        <a href="../controlador/controlador_editar_jugador.php?ID_Jugador=<?= $alumno['ID_Jugador'] ?>"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="orange" class="bi bi-pen" viewBox="0 0 16 16"><path d="m13.498.795.149-.149a1.207 1.207 0 1 1 1.707 1.708l-.149.148a1.5 1.5 0 0 1-.059 2.059L4.854 14.854a.5.5 0 0 1-.233.131l-4 1a.5.5 0 0 1-.606-.606l1-4a.5.5 0 0 1 .131-.232l9.642-9.642a.5.5 0 0 0-.642.056L6.854 4.854a.5.5 0 1 1-.708-.708L9.44.854A1.5 1.5 0 0 1 11.5.796a1.5 1.5 0 0 1 1.998-.001m-.644.766a.5.5 0 0 0-.707 0L1.95 11.756l-.764 3.057 3.057-.764L14.44 3.854a.5.5 0 0 0 0-.708z"/></svg></a>
                        <span>&nbsp;</span>
                        <a href="../controlador/controlador_borrar_jugador.php?ID_Jugador=<?= $alumno['ID_Jugador'] ?>"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="orange" class="bi bi-trash" viewBox="0 0 16 16"><path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z"/><path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z"/></svg></a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
        </div>
    </div>
</div>


<div class="col-sm-12 col-md-12 mt-2">
    <div class="card bg-dark text-white">
        <div class="card-body">
            <h5 class="card-title">Partidos</h5>
            <table class="table table-dark table-striped">
            <thead>
                        <tr>
                            <th>ID</th>
                            <th>Equipo Local</th>
                            <th>Equipo Visitante</th>
                            <th>Fecha</th>
                         
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($partidos as $partido): ?>
                            <tr>
                                <td><?php echo $partido['id']; ?></td>
                                <td><?php echo $partido['equipo_local']; ?></td>
                                <td><?php echo $partido['equipo_visitante']; ?></td>
                                <td><?php echo $partido['fecha']; ?></td>
                        
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
            </table>
        </div>
    </div>
</div>
            <?php else: ?>
                <h1 class="text-center" style="color: orange;">Inscribir equipo</h1>
                <form method="post" action="../controlador/controlador_insercion_equipo.php" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="nombre_equipo" style="color: orange;">Nombre del equipo:</label>
                        <input type="text" class="form-control" id="nombre_equipo" name="nombre_equipo" required>
                    </div>
                    <div class="form-group">
                        <label for="foto_equipo" style="color: orange;">Foto del equipo:</label>
                        <input type="file" class="form-control" id="foto_equipo" name="foto_equipo" accept="image/*">
                    </div>
                    <div class="mb-3">
                        <span style="color: orange;"><i class="bi bi-pencil me-1"></i>Descripcion</span>
                        <textarea class="form-control" id="message" name="descripcion" placeholder="Escribe la descripcion de tu equipo..." rows="5"></textarea>
                        <span id="lname-error" class="error-message"></span>
                    </div>
                    
                    <input type="hidden" name="id_equipo" value="<?php echo htmlspecialchars($id_equipo_usuario); ?>">
                    <button type="submit" class="btn btn-warning" style="color: dark;">Inscribir equipo</button>
                </form>

            
            <?php endif; ?>
        </div>
    </div>
</body>
<footer>
            <p>&copy; 2024 SoccerMatchUp. Todos los derechos reservados.</p>
            <section class="mb-4">
      <!-- Facebook -->
      <a
        class="btn btn-link btn-floating btn-lg m-1"
        href="#!"
        role="button"
        data-mdb-ripple-color="dark"
        ><i class="fab fa-facebook-f text-orange"></i
      ></a>

      <!-- Twitter -->
      <a
        class="btn btn-link btn-floating btn-lg m-1"
        href="#!"
        role="button"
        data-mdb-ripple-color="dark"
        ><i class="fab fa-twitter text-orange"></i
      ></a>

      <!-- Google -->
      <a
        class="btn btn-link btn-floating btn-lg m-1"
        href="#!"
        role="button"
        data-mdb-ripple-color="dark"
        ><i class="fab fa-google text-orange"></i
      ></a>

      <!-- Instagram -->
      <a
        class="btn btn-link btn-floating btn-lg m-1"
        href="#!"
        role="button"
        data-mdb-ripple-color="dark"
        ><i class="fab fa-instagram text-orange"></i
      ></a>

      <!-- Linkedin -->
      <a
        class="btn btn-link btn-floating btn-lg m-1"
        href="#!"
        role="button"
        data-mdb-ripple-color="dark"
        ><i class="fab fa-linkedin text-orange"></i
      ></a>
      <!-- Github -->
      <a
        class="btn btn-link btn-floating btn-lg m-1"
        href="#!"
        role="button"
        data-mdb-ripple-color="white"
        ><i class="fab fa-github text-orange"></i
      ></a>
    </section>
        </footer>

<div id="dlgContact1" class="modal fade" tabindex="-1" aria-labelledby="dlgContactLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="dlgContactLabel" style="color: orange"; >Añadir Jugador</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <form action="../controlador/controlador_insercion_jugador.php" method="POST">
                    <div class="mb-3">
                        <span style="color: orange"><i class="bi bi-person me-1"></i>Nombre</span>
                        <input id="fname" name="name" type="text" placeholder="Name" class="form-control" required />
                    </div>
                    <div class="mb-3">
                        <span style="color: orange"><i class="bi bi-person me-1"></i>Dorsal</span>
                        <input id="fname" name="dorsal" type="text" placeholder="Dorsal" class="form-control" required />
                    </div>
                    <div class="mb-3">
                        <span style="color: orange"><i class="bi bi-person me-1"></i>Posicion</span>
                        <input id="fname" name="posicion" type="text" placeholder="Position" class="form-control" required />
                    </div>
                    <input type="hidden" name="id_equipo" value="<?php echo $id_equipo_usuario; ?>">
                    <button type="submit" class="btn btn-primary custom-orange">Enviar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Editar Jugador</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editForm" action="../controlador/editar_jugador.php" method="post">
                        <input type="hidden" name="ID_Jugador" id="edit_id">
                        <div class="mb-3">
                            <label for="edit_nombre" class="form-label">Nombre:</label>
                            <input type="text" class="form-control" id="edit_nombre" name="nombre" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_dorsal" class="form-label">Dorsal:</label>
                            <input type="text" class="form-control" id="edit_dorsal" name="dorsal" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_posicion" class="form-label">Posición:</label>
                            <input type="text" class="form-control" id="edit_posicion" name="posicion" required>
                        </div>
                        <button type="submit" class="btn btn-primary ">Actualizar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</html>
