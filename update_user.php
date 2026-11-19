
<?php
require __DIR__ . '/database.php';

// Datos que llegan del formulario
$id           = $_POST['id'] ?? null;
$firstname    = $_POST['firstname'] ?? '';
$lastname     = $_POST['lastname'] ?? '';
$email        = $_POST['email'] ?? '';
$mobile_phone = $_POST['mobile_phone'] ?? '';

// Verificar ID
if (!$id || !is_numeric($id)) {
    die("ID de usuario no válido.");
}

// PARTE A: ELIMINAR FOTOGRAFÍA
if (isset($_POST['delete_photo'])) {

    // Buscar fotografía actual
    $sql = 'SELECT photo_url FROM users WHERE id = $1';
    $result = pg_query_params($supa_conn, $sql, array($id));

    if (!$result) {
        die("Error al buscar la fotografía.");
    }

    $user = pg_fetch_assoc($result);

    if ($user && !empty($user['photo_url'])) {

        // Eliminar archivo físico
        $nombreFoto = basename($user['photo_url']);
        $rutaFoto = __DIR__ . '/photos/' . $nombreFoto;

        if (is_file($rutaFoto)) {
            unlink($rutaFoto);
        }

        // Eliminar ruta de PostgreSQL
        $sql = 'UPDATE users SET photo_url = NULL WHERE id = $1';

        $result = pg_query_params(
            $supa_conn,
            $sql,
            array($id)
        );

        if (!$result) {
            die("Error al eliminar la fotografía de la base de datos.");
        }
    }

    header("Location: edit_user.php");
    exit();
}

// PARTE B: ACTUALIZAR LOS DATOS
$sql = 'UPDATE users
        SET firstname = $1,
            lastname = $2,
            email = $3,
            mobile_phone = $4
        WHERE id = $5';

$result = pg_query_params(
    $supa_conn,
    $sql,
    array($firstname, $lastname, $email, $mobile_phone, $id)
);

if (!$result) {
    die("Error al actualizar los datos: " . pg_last_error($supa_conn));
}

// PARTE C: GUARDAR FOTOGRAFÍA SI SELECCIONARON UNA
if (isset($_FILES['photo']) && $_FILES['photo']['error'] !== UPLOAD_ERR_NO_FILE) {

    if ($_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
        die("Error al subir la fotografía.");
    }

    // Validar extensión
    $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, ['jpg', 'jpeg', 'png'])) {
        die("Solo se permiten fotografías JPG o PNG.");
    }

    // Verificar que sea realmente una imagen
    $imageInfo = getimagesize($_FILES['photo']['tmp_name']);

    if ($imageInfo === false) {
        die("El archivo seleccionado no es una imagen válida.");
    }

    // Crear carpeta si no existe
    $carpeta = __DIR__ . '/photos/';

    if (!is_dir($carpeta)) {
        mkdir($carpeta, 0755, true);
    }

    // Buscar fotografía anterior
    $sql = 'SELECT photo_url FROM users WHERE id = $1';
    $result = pg_query_params($supa_conn, $sql, array($id));
    $user = pg_fetch_assoc($result);

    // Eliminar fotografía anterior si existe
    if ($user && !empty($user['photo_url'])) {
        $fotoAnterior = __DIR__ . '/' . $user['photo_url'];

        if (is_file($fotoAnterior)) {
            unlink($fotoAnterior);
        }
    }

    // Nombre único para la fotografía
    $nombreFoto = uniqid('user_', true) . "." . $ext;

    // Ruta física
    $destino = $carpeta . $nombreFoto;

    // Ruta para guardar en PostgreSQL
    $photo_url = "photos/" . $nombreFoto;

    // Mover fotografía
    if (move_uploaded_file($_FILES['photo']['tmp_name'], $destino)) {

        $sql = 'UPDATE users SET photo_url = $1 WHERE id = $2';

        $result = pg_query_params(
            $supa_conn,
            $sql,
            array($photo_url, $id)
        );

        if (!$result) {
            die("Error al guardar la ruta de la foto.");
        }

    } else {
        die("No se pudo guardar la fotografía.");
    }
}

// PARTE D: VOLVER AL INDEX
header("Location: index.php");
exit();

?>
