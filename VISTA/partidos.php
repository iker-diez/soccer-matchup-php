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
    // Consulta SQL para obtener la información del equipo del usuario
    $sql = "SELECT * FROM equipo WHERE ID_Equipo = :id_equipo";
    $stmt = $conexion->prepare($sql);

    if (!$stmt->execute([':id_equipo' => $id_equipo_usuario])) {
        die("Error al ejecutar la consulta: " . $stmt->errorInfo()[2]);
    }

    $equipo = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($equipo) {
        $nombre_equipo = $equipo['Nombre_Equipo'];
        $foto_equipo = $equipo['Foto'];

        // Consulta SQL para obtener los partidos del equipo del usuario
        $sql = "SELECT p.*, el.Foto as foto_local, ev.Foto as foto_visitante 
                FROM partidos p
                JOIN equipo el ON p.equipo_local = el.Nombre_Equipo
                JOIN equipo ev ON p.equipo_visitante = ev.Nombre_Equipo
                WHERE el.ID_Equipo = :id_equipo OR ev.ID_Equipo = :id_equipo";
        $stmt = $conexion->prepare($sql);

        if (!$stmt->execute([':id_equipo' => $id_equipo_usuario])) {
            die("Error al ejecutar la consulta: " . $stmt->errorInfo()[2]);
        }

        $partidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Consulta SQL para obtener los resultados de los partidos del equipo del usuario
        $sql = "SELECT p.*, r.goles_local, r.goles_visitante, el.Foto as foto_local, ev.Foto as foto_visitante 
                FROM partidos p
                JOIN resultados r ON p.id = r.id_partido
                JOIN equipo el ON p.equipo_local = el.Nombre_Equipo
                JOIN equipo ev ON p.equipo_visitante = ev.Nombre_Equipo
                WHERE el.ID_Equipo = :id_equipo OR ev.ID_Equipo = :id_equipo";
        $stmt = $conexion->prepare($sql);

        if (!$stmt->execute([':id_equipo' => $id_equipo_usuario])) {
            die("Error al ejecutar la consulta: " . $stmt->errorInfo()[2]);
        }

        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $nombre_equipo = null;
    }
} else {
    $nombre_equipo = null;
}

// Obtener la página actual
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Partidos de Mi Equipo</title>
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
            background-color: orange;
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
            margin-left: 250px;
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
            border: 1px solid white;
            border-radius: 40px;
            color: #ebeff1;
            box-shadow: none;
            text-shadow: none;
            padding: .3rem .8rem;
            background: transparent;
            transition: background-color .2s, color .2s;
        }

        .header-black .action-button:hover,
        .header-black .action-button:active:focus,
        .header-black .action-button:focus {
            background: orange;
            color: #1a1a1a;
        }

        .header-black .btn-light {
            color: white;
            border-color: white;
            border-width: 1px;
        }

        .btn-light.custom {
            color: orange;
            border-color: orange;
            border-width: 1px;
        }

        .btn-light.custom:hover {
            background-color: orange;
            color: #1a1a1a;
        }

        a {
            text-decoration: none !important;
        }

        .navbar-dark .navbar-nav .nav-link {
            color: white;
        }

        a.nav-link {
            color: white;
        }

        a.nav-link:hover {
            color: orange;
        }

        .list-group-item.active {
            background-color: orange !important;
            border-color: orange !important;
        }

        a.list-group-item:hover {
            background-color: orange !important;
            border-color: orange !important;
        }

        .list-group-item {
            border-color: orange;
            color: white;
        }

        .text-orange {
            color: orange !important;
        }

        .page-link {
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
            border: 1px solid white;
        }

        .partido-table {
            display: table;
            width: 100%;
        }

        .equipo-local,
        .equipo-visitante,
        .fecha {
            display: table-cell;
            vertical-align: middle;
        }

        .equipo-local,
        .equipo-visitante {
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

        .equipo-local span,
        .equipo-visitante span {
            display: inline-block;
            vertical-align: middle;
        }

        .navbar-text {
            margin-right: 10px;
        }

        .custom-orange {
            background-color: orange;
            border-color: orange;
        }

        .custom-orange:hover {
            background-color: darkorange;
            /* Color al pasar el ratón por encima */
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
                <li><a href="principal_iniciado.php">Inicio</a></li>
                <li><a href="panelUsuario.php" >Mi equipo</a></li>
                <li><a href="partidos.php" >Mis Partidos</a></li>
            </ul>
        </div>

        <div class="container custom-margin" style="margin-top: 202px;">
            <h1 class="text-center">Equipo: <?php echo htmlspecialchars($nombre_equipo); ?></h1>

            <?php if ($nombre_equipo): ?>
                


                <div class="col-sm-12 col-md-12 mt-2">
                  <div class="card bg-dark text-white">
                     <div class="card-body">
                        <h5 class="card-title" style="color: orange;">Próximos Partidos</h5>
                        <ul class="list-group">
                            <?php foreach ($partidos as $partido) { 
                                // Convertir la fecha al formato deseado
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
                                </div>
                            </li>
                <?php } ?>
                        </ul>
                            </div>
                            </div>
                            </div>

                            <div class="col-sm-12 col-md-12 mt-2">
    <div class="card bg-dark text-white">
        <div class="card-body">
            <h5 class="card-title" style="color: orange;">Últimos Resultados</h5>
            <ul class="list-group">
                <?php foreach ($resultados as $resultado): ?>
                    <li class="list-group-item bg-dark text-white">
                        <div class="row">
                            <div class="col-md-4 text-center">
                                <img src="<?php echo $resultado['foto_local']; ?>" alt="<?php echo $resultado['equipo_local']; ?>" class="equipo-img">
                                <p><?php echo $resultado['equipo_local']; ?></p>
                            </div>
                            <div class="col-md-4 text-center">
                                <p><?php echo $resultado['fecha']; ?></p>
                                <p><?php echo $resultado['goles_local']; ?> - <?php echo $resultado['goles_visitante']; ?></p>
                            </div>
                            <div class="col-md-4 text-center">
                                <img src="<?php echo $resultado['foto_visitante']; ?>" alt="<?php echo $resultado['equipo_visitante']; ?>" class="equipo-img">
                                <p><?php echo $resultado['equipo_visitante']; ?></p>
                            </div>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>
            <?php else: ?>
                <p class="text-center">No se ha encontrado el equipo del usuario.</p>
            <?php endif; ?>
        </div>
    </div>

    <footer style="text-align: center;">
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
</body>

</html>
