
<?php
session_start();

require __DIR__ . '/database.php';

// Obtener datos del formulario
$e_email = trim($_POST['email'] ?? '');
$p_assword = $_POST['pswd'] ?? '';

if ($e_email === '' || $p_assword === '') {
    header('Location: login.html?error=campos');
    exit;
}

// Consultar usuario existente en Supabase
$sql = "SELECT id, firstname, lastname, email, password
        FROM users
        WHERE email = $1 AND status = TRUE
        LIMIT 1";

$resultado = pg_query_params(
    $supa_conn,
    $sql,
    [$e_email]
);

if (!$resultado) {
    error_log(pg_last_error($supa_conn));
    die("Error al consultar la base de datos.");
}

$usuario = pg_fetch_assoc($resultado);

// Validar contraseña almacenada
if ($usuario) {
    $passwordGuardada = $usuario['password'];

    // Compatibilidad con registros antiguos que usan MD5
    if (preg_match('/^[a-f0-9]{32}$/i', $passwordGuardada)) {
        $valida = hash_equals(
            strtolower($passwordGuardada),
            md5($p_assword)
        );
    } else {
        $valida = password_verify(
            $p_assword,
            $passwordGuardada
        );
    }

    if ($valida) {
        session_regenerate_id(true);

        $_SESSION['user_id'] = $usuario['id'];
        $_SESSION['firstname'] = $usuario['firstname'];
        $_SESSION['lastname'] = $usuario['lastname'];
        $_SESSION['email'] = $usuario['email'];

        header('Location: index.html');
        exit;
    }
}

// Correo inexistente o contraseña incorrecta
header('Location: login.html?error=credenciales');
exit;

?>