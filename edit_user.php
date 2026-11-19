
<?php

// Conexión a la base de datos
require __DIR__ . '/database.php';

session_start();

if (!isset($_SESSION['userID'])) {
    header('Location: login.html');
    exit();
}

$id = $_SESSION['userID'];

$sql = 'SELECT * FROM users WHERE id = $1';
$result = pg_query_params($supa_conn, $sql, array($id));
$user = pg_fetch_assoc($result);

if (!$user) {
    echo "No se encontró el usuario.";
    exit();
}

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Editar perfil - SB Admin 2</title>

    <!-- Fuentes -->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">

    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900"
        rel="stylesheet">

    <!-- Estilos SB Admin 2 -->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">

    <style>

        body {
            background-color: #f8f9fc;
            font-family: 'Nunito', sans-serif;
        }

        .profile-container {
            max-width: 750px;
            margin: 45px auto;
            padding: 0 15px;
        }

        .profile-card {
            border: none;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 18px rgba(0,0,0,0.08);
        }

        .profile-header {
            background: #4e73df;
            color: white;
            padding: 25px;
        }

        .profile-header h4 {
            font-weight: 700;
            margin-bottom: 5px;
        }

        .profile-header p {
            margin-bottom: 0;
            opacity: 0.9;
        }

        .profile-body {
            padding: 30px;
        }

        .form-control {
            border-radius: 8px;
            height: 43px;
            border: 1px solid #d1d3e2;
        }

        .form-control:focus {
            border-color: #4e73df;
            box-shadow: 0 0 0 0.15rem rgba(78,115,223,0.20);
        }

        label {
            font-weight: 700;
            color: #5a5c69;
        }

        .photo-container {
            text-align: center;
            background: #f8f9fc;
            border: 1px dashed #d1d3e2;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 25px;
        }

        .profile-photo {
            width: 125px;
            height: 125px;
            object-fit: cover;
            border-radius: 50%;
            border: 4px solid white;
            box-shadow: 0 3px 12px rgba(0,0,0,0.15);
            margin-bottom: 15px;
        }

        .photo-title {
            font-weight: 700;
            color: #5a5c69;
        }

        .btn-save {
            background-color: #4e73df;
            color: white;
            border: none;
            border-radius: 8px;
            padding: 11px 25px;
            font-weight: 700;
        }

        .btn-save:hover {
            background-color: #2e59d9;
            color: white;
        }

        .btn-cancel {
            background-color: #858796;
            color: white;
            border-radius: 8px;
            padding: 11px 25px;
            font-weight: 700;
        }

        .btn-cancel:hover {
            background-color: #717384;
            color: white;
        }

        .btn-delete {
            background-color: #e74a3b;
            color: white;
            border: none;
            border-radius: 7px;
            padding: 9px 16px;
            font-weight: 700;
        }

        .btn-delete:hover {
            background-color: #c92a1c;
            color: white;
        }

        .btn-file {
            border-radius: 8px;
            background: #eaecf4;
            padding: 10px;
            width: 100%;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .footer-text {
            text-align: center;
            color: #858796;
            font-size: 13px;
            margin-top: 20px;
        }

    </style>

</head>

<body>

    <div class="container-fluid">

        <div class="profile-container">

            <!-- Tarjeta principal -->
            <div class="card profile-card">

                <!-- Encabezado -->
                <div class="profile-header">

                    <h4>
                        <i class="fas fa-user-edit mr-2"></i>
                        Editar mi perfil
                    </h4>

                    <p>Actualiza tu información personal y fotografía.</p>

                </div>

                <!-- Contenido -->
                <div class="profile-body">

                    <form action="update_user.php" method="post" enctype="multipart/form-data">

                        <input type="hidden" name="id"
                            value="<?php echo htmlspecialchars($user['id']); ?>">

                        <!-- Fotografía -->
                        <div class="photo-container">

                            <?php if (!empty($user['photo_url'])) { ?>

                                <p class="photo-title">Fotografía actual</p>

                                <img src="<?php echo htmlspecialchars($user['photo_url']); ?>"
                                    class="profile-photo"
                                    alt="Foto de perfil">

                                <br>

                                <button type="submit"
                                    name="delete_photo"
                                    value="1"
                                    class="btn btn-delete"
                                    onclick="return confirm('¿Estás seguro de que deseas eliminar la fotografía?');">

                                    <i class="fas fa-trash-alt mr-1"></i>
                                    Eliminar foto

                                </button>

                            <?php } else { ?>

                                <p class="photo-title">Fotografía predeterminada</p>

                                <img src="img/undraw_profile.svg"
                                    class="profile-photo"
                                    alt="Foto predeterminada">

                                <p class="text-muted small mb-0">
                                    Todavía no tienes una fotografía personalizada.
                                </p>

                            <?php } ?>

                        </div>

                        <!-- Nombre -->
                        <div class="form-group">

                            <label>
                                <i class="fas fa-user mr-1 text-primary"></i>
                                Nombre
                            </label>

                            <input type="text"
                                name="firstname"
                                class="form-control"
                                value="<?php echo htmlspecialchars($user['firstname']); ?>"
                                required>

                        </div>

                        <!-- Apellido -->
                        <div class="form-group">

                            <label>
                                <i class="fas fa-user mr-1 text-primary"></i>
                                Apellido
                            </label>

                            <input type="text"
                                name="lastname"
                                class="form-control"
                                value="<?php echo htmlspecialchars($user['lastname']); ?>"
                                required>

                        </div>

                        <!-- Correo -->
                        <div class="form-group">

                            <label>
                                <i class="fas fa-envelope mr-1 text-primary"></i>
                                Correo electrónico
                            </label>

                            <input type="email"
                                name="email"
                                class="form-control"
                                value="<?php echo htmlspecialchars($user['email']); ?>"
                                required>

                        </div>

                        <!-- Celular -->
                        <div class="form-group">

                            <label>
                                <i class="fas fa-phone mr-1 text-primary"></i>
                                Celular
                            </label>

                            <input type="text"
                                name="mobile_phone"
                                class="form-control"
                                value="<?php echo htmlspecialchars($user['mobile_phone']); ?>"
                                required>

                        </div>

                        <!-- Estado -->
                        <div class="form-group">

                            <label>
                                <i class="fas fa-toggle-on mr-1 text-primary"></i>
                                Estado
                            </label>

                            <select name="status" class="form-control">

                                <option value="true"
                                    <?php if ($user['status'] == 't') echo 'selected'; ?>>
                                    Active
                                </option>

                                <option value="false"
                                    <?php if ($user['status'] == 'f') echo 'selected'; ?>>
                                    Inactive
                                </option>

                            </select>

                        </div>

                        <!-- Cambiar fotografía -->
                        <div class="form-group">

                            <label>
                                <i class="fas fa-camera mr-1 text-primary"></i>
                                Cambiar fotografía
                            </label>

                            <input type="file"
                                name="photo"
                                class="form-control btn-file"
                                accept=".jpg,.jpeg,.png">

                            <small class="form-text text-muted">
                                Formatos permitidos: JPG, JPEG y PNG.
                            </small>

                        </div>

                        <hr>

                        <!-- Botones -->
                        <div class="d-flex justify-content-between flex-wrap">

                            <a href="index.php" class="btn btn-cancel mb-2">

                                <i class="fas fa-arrow-left mr-1"></i>
                                Cancelar

                            </a>

                            <button type="submit" class="btn btn-save mb-2">

                                <i class="fas fa-save mr-1"></i>
                                Guardar cambios

                            </button>

                        </div>

                    </form>

                </div>

            </div>

            <div class="footer-text">
                SB Admin 2 - Administración de perfil
            </div>

        </div>

    </div>

    <!-- Bootstrap JavaScript -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

</body>

</html>
