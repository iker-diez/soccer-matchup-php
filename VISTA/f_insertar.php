<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="../CCS/registro.css">
    <style>
        body {
          background: #ff7f00;
          background: linear-gradient(to right, #ff6200, #ffae33);
        }
        .card-img-left {
          width: 45%;
          background: scroll center url('../uploads/equipos/fondo.jpg');
          background-size: cover;
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
        a {
          color: orange;
        }
        .form-control:focus {
          border-color: #ff7f00;
          box-shadow: 0 0 0 0.25rem rgba(255, 127, 0, 0.25);
        }
        @media (max-width: 768px) {
          .card-img-left {
            display: none;
          }
        }
    </style>
</head>
<body>
  <div class="container">
    <div class="row">
      <div class="col-lg-10 col-xl-9 mx-auto">
        <div class="card flex-row my-5 border-0 shadow rounded-3 overflow-hidden">
          <div class="card-img-left d-none d-md-flex">
            <!-- Imagen de fondo definida en CSS -->
          </div>
          <div class="card-body p-4 p-sm-5">
            <h5 class="card-title text-center mb-5 fw-light fs-5">Registro</h5>
            <form action="../controlador/controlador_insercion.php" method="POST">
              <div class="form-floating mb-3">
                <input type="text" class="form-control" id="floatingInputUsername" placeholder="myusername" required autofocus name="nombre">
                <label for="floatingInputUsername">Nombre</label>
              </div>
              <div class="form-floating mb-3">
                <input type="email" class="form-control" id="floatingInputEmail" placeholder="name@example.com" name="correo">
                <label for="floatingInputEmail">Correo</label>
              </div>
              <div class="form-floating mb-3">
                <input type="password" class="form-control" id="floatingPassword" placeholder="Password" name="contraseña">
                <label for="floatingPassword">Contraseña</label>
              </div>
              <div class="d-grid mb-2">
                <button class="btn btn-lg btn-primary btn-login fw-bold text-uppercase custom-orange" type="submit">Registrar</button>
              </div>
              <a class="d-block text-center mt-2 small" href="inicio_sesion.php">¿Ya tienes cuenta? Inicia Sesion</a>
              <hr class="my-4">
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>