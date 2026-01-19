<?php
session_start();
require_once("../modelo/conectar.php");

$conexion = Conectar::conexion();

// Comprobamos si el usuario está autenticado
$is_logged_in = isset($_SESSION['user_id']);

// Consultas para obtener datos
// Variables de paginación para equipos
$equipos_por_pagina = 5;
$pagina_actual_equipos = isset($_GET['pagina_equipos']) ? (int)$_GET['pagina_equipos'] : 1;
$offset_equipos = ($pagina_actual_equipos - 1) * $equipos_por_pagina;

// Consulta para obtener el total de equipos
$sql_total_equipos = "SELECT COUNT(*) FROM equipo";
$total_equipos = $conexion->query($sql_total_equipos)->fetchColumn();
$total_paginas_equipos = ceil($total_equipos / $equipos_por_pagina);

// Consulta para obtener los equipos con paginación
$sql_equipos = "SELECT Nombre_Equipo, Foto FROM equipo LIMIT $offset_equipos, $equipos_por_pagina";
$resultado_equipos = $conexion->query($sql_equipos);
$equipos = $resultado_equipos->fetchAll();

// Variables de paginación para partidos
$partidos_por_pagina = 5;
$pagina_actual_partidos = isset($_GET['pagina_partidos']) ? (int)$_GET['pagina_partidos'] : 1;
$offset_partidos = ($pagina_actual_partidos - 1) * $partidos_por_pagina;

// Consulta para obtener el total de partidos
$sql_total_partidos = "SELECT COUNT(*) FROM partidos";
$total_partidos = $conexion->query($sql_total_partidos)->fetchColumn();
$total_paginas_partidos = ceil($total_partidos / $partidos_por_pagina);

// Consulta para obtener los partidos con paginación
$sql_partidos = "
SELECT DISTINCT p.equipo_local, p.equipo_visitante, p.fecha, e1.Foto AS foto_local, e2.foto AS foto_visitante
FROM partidos p
JOIN equipo e1 ON p.equipo_local = e1.Nombre_Equipo
JOIN equipo e2 ON p.equipo_visitante = e2.Nombre_Equipo
LIMIT $offset_partidos, $partidos_por_pagina";
$resultado_partidos = $conexion->query($sql_partidos);
$partidos = $resultado_partidos->fetchAll();

// Consulta para obtener las sedes
$sql_sedes = "SELECT * FROM sedes";
$resultado_sedes = $conexion->query($sql_sedes);
$sedes = $resultado_sedes->fetchAll();

$sql_resultados = "
SELECT r.id, r.id_partido, r.goles_local, r.goles_visitante, p.fecha, 
       e1.Nombre_Equipo AS equipo_local, e1.Foto AS foto_local, 
       e2.Nombre_Equipo AS equipo_visitante, e2.Foto AS foto_visitante
FROM resultados r
JOIN partidos p ON r.id_partido = p.id
JOIN equipo e1 ON p.equipo_local = e1.Nombre_Equipo
JOIN equipo e2 ON p.equipo_visitante = e2.Nombre_Equipo
ORDER BY p.fecha DESC";
$resultado_resultados = $conexion->query($sql_resultados);
$resultados = $resultado_resultados->fetchAll();

$current_page = basename($_SERVER['REQUEST_URI']);


?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SoccerMatchUp</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,700">
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script>
        // Redirecciona a la página 1 si no hay parámetros 'pagina_equipos' y 'pagina_partidos' en la URL
        document.addEventListener('DOMContentLoaded', function() {
            const params = new URLSearchParams(window.location.search);
            if (!params.has('pagina_equipos')) {
                params.set('pagina_equipos', '1');
            }
            if (!params.has('pagina_partidos')) {
                params.set('pagina_partidos', '1');
            }
            if (!window.location.search) {
                window.location.href = window.location.pathname + '?' + params.toString();
            }
        });
    </script>
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
    box-sizing: border-box;
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

/* Media Queries */
@media (max-width: 767.98px) {
    .sidebar {
        width: 100%;
        height: auto;
        position: relative;
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
                                <span class="navbar-text">Bienvenido, <?php echo $_SESSION['nombre']; ?>(Administrador)</span>
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
                                <li><a href="">Inicio</a></li>
                                <li><a href="panelAdmin.php">Panel Admin</a></li>
                                <li><a href="#acerca">Acerca de</a></li>
                            </ul>
                        </div>
                    
        <div class="container mt-5 pt-5">
        <div class="banner">
        <img src="../uploads/equipos/banner.jpg" style="height: 350px;  width:100%;"/>
        </div> <!-- Aquí se agrega el banner -->
            <div class="row">
                <!-- Principales equipos -->
                <div class="col-sm-12 col-md-6 mt-2">
                    <div class="card bg-dark text-white">
                        <div class="card-body">
                            <h5 class="card-title">Principales Equipos</h5>
                            <ul class="list-group">
                                <?php foreach ($equipos as $equipo) { ?>
                                    <li class="list-group-item bg-dark text-white d-flex align-items-center">
                                        <img src="<?php echo $equipo['Foto']; ?>" alt="<?php echo $equipo['Nombre_Equipo']; ?>" style="width: 50px; height: 50px; margin-right: 10px;">
                                        <?php echo $equipo['Nombre_Equipo']; ?>
                                    </li>
                                <?php } ?>
                            </ul>
                            <nav>
                                <ul class="pagination">
                                    <?php for ($i = 1; $i <= $total_paginas_equipos; $i++) { ?>
                                        <li class="page-item <?php echo $i == $pagina_actual_equipos ? 'active' : ''; ?>">
                                            <a class="page-link" href="?pagina_equipos=<?php echo $i; ?>&pagina_partidos=<?php echo $pagina_actual_partidos; ?>"><?php echo $i; ?></a>
                                        </li>
                                    <?php } ?>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>

                <!-- Próximos partidos -->
                <div class="col-sm-12 col-md-6 mt-2">
    <div class="card bg-dark text-white">
        <div class="card-body">
            <h5 class="card-title">Próximos Partidos</h5>
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
            <nav>
                <ul class="pagination">
                    <?php for ($i = 1; $i <= $total_paginas_partidos; $i++) { ?>
                        <li class="page-item <?php echo $i == $pagina_actual_partidos ? 'active' : ''; ?>">
                            <a class="page-link" href="?pagina_equipos=<?php echo $pagina_actual_equipos; ?>&pagina_partidos=<?php echo $i; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php } ?>
                </ul>
            </nav>
        </div>
    </div>
</div>

<div class="col-sm-12 col-md-6 mt-2">
    <div class="card bg-dark text-white">
        <div class="card-body">
            <h5 class="card-title">Sedes Disponibles</h5>
            <div class="table-responsive">
            <table class="table table-dark table-striped">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Nombre</th>
                        <th scope="col">Ciudad</th>
                        <th scope="col">Dirección</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($sedes as $index => $sede) { ?>
                        <tr>
                            <th scope="row"><?php echo $index + 1; ?></th>
                            <td><?php echo $sede['nombre']; ?></td>
                            <td><?php echo $sede['Localidad']; ?></td>
                            <td><?php echo $sede['direccion']; ?></td>
                        </tr>
                    <?php } ?> 
                </tbody>
            </table>
            </div>
        </div>
    </div>
</div>

                <!-- Últimos resultados -->
                <!-- Últimos resultados -->
<div class="col-sm-12 col-md-6 mt-2">
    <div class="card bg-dark text-white">
        <div class="card-body">
            <h5 class="card-title">Últimos Resultados</h5>
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
            </div>
        </div>
            </div>
        </div>

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
    </div>
</body>

</html>
