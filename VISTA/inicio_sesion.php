<?php
require_once("../MODELO/conectar.php");

// Obtener una instancia de la conexión a la base de datos
$conexion = Conectar::conexion();

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = htmlspecialchars($_POST["correo"]);
    $contraseña = htmlspecialchars($_POST["contraseña"]);

    if (empty($correo) || empty($contraseña)) {
        $error = 'Por favor, complete todos los campos.';
    } else {
        // Verificar si el usuario es el administrador
        if ($correo == "admin@gmail.com" && $contraseña == "admin") {
            session_start();
            $_SESSION['ID'] = "admin";
            $_SESSION['nombre'] = "Administrador";
            header("Location: ../vista/principal_iniciado_admin.php");
            exit;
        }

        // Consulta SQL para seleccionar el registro del usuario con el correo electrónico proporcionado
        $sql = "SELECT ID, nombre, Contraseña FROM alumno WHERE CorreoElectronico = :correo";
        $stmt = $conexion->prepare($sql);
        $stmt->execute(['correo' => $correo]);
        $usuario = $stmt->fetch();

        // Verificar si se encontró un usuario con el correo electrónico proporcionado
        if ($usuario) {
            // Verificar si la contraseña proporcionada coincide con la almacenada en la base de datos
            if (password_verify($contraseña, $usuario['Contraseña'])) {
                // La contraseña es correcta, guardar el ID y el nombre del usuario en la sesión
                session_start();
                $_SESSION['ID'] = $usuario['ID'];
                $_SESSION['nombre'] = $usuario['nombre'];

                // Redirigir al usuario a la página deseada
                header("Location: ../vista/principal_iniciado.php");
                exit;
            } else {
                // La contraseña no coincide
                $error = 'Contraseña incorrecta.';
            }
        } else {
            // No se encontró ningún usuario con el correo electrónico proporcionado
            $error = 'No se encontró ningún usuario con ese correo electrónico.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="../CCS/login.css"></link>
    <style>
        .login {
            min-height: 100vh;
        }
        .bg-image {
            background-image: url('../uploads/equipos/fondo.jpg');
            background-size: cover;
            background-position: center;
            height: 100vh;
        }
        .login-heading {
            font-weight: 300;
        }
        .btn-login {
            font-size: 0.9rem;
            letter-spacing: 0.05rem;
            padding: 0.75rem 1rem;
        }
        .custom-orange {
            background-color: orange;
            border-color: orange;
        }
        .custom-orange:hover {
            background-color: darkorange;
            border-color: darkorange;
        }
        .form-control:focus {
            border-color: #ff7f00;
            box-shadow: 0 0 0 0.25rem rgba(255, 127, 0, 0.25);
        }
        @media (max-width: 768px) {
            .bg-image {
                display: none;
            }
            .login {
                margin-top: 0;
                padding: 2rem;
            }
        }
    </style>
    <script>
        window.onload = function() {
            var error = "<?php echo $error; ?>";
            if (error) {
                var errorModal = new bootstrap.Modal(document.getElementById('errorModal'), {});
                errorModal.show();
            }
        }
    </script>
</head>
<body>
    <div class="container-fluid ps-md-0">
        <div class="row g-0">
            <div class="d-none d-md-flex col-md-4 col-lg-6 bg-image"></div>
            <div class="col-md-8 col-lg-6">
                <div class="login d-flex align-items-center py-5">
                    <div class="container">
                        <div class="row">
                            <div class="col-md-9 col-lg-8 mx-auto">
                                <h3 class="login-heading mb-4">Bienvenido de nuevo</h3>

                                <!-- Sign In Form -->
                                <form method="POST">
                                    <div class="form-floating mb-3">
                                        <input type="email" class="form-control" id="floatingInput" placeholder="name@example.com" name="correo">
                                        <label for="floatingInput">Correo Electronico</label>
                                    </div>
                                    <div class="form-floating mb-3">
                                        <input type="password" class="form-control" id="floatingPassword" placeholder="Password" name="contraseña">
                                        <label for="floatingPassword">Contraseña</label>
                                    </div>
                                    <div class="d-grid">
                                        <button class="btn btn-lg btn-primary btn-login text-uppercase fw-bold mb-2 custom-orange" type="submit">Inicia Sesion</button>
                                    </div>
                                </form>
                                
                                <!-- Error Modal -->
                                <div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="errorModalLabel">Error</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <?php echo $error; ?>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary custo" data-bs-dismiss="modal">Cerrar</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
