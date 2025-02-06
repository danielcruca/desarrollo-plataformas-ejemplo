<?php

// Configuración de la base de datos
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'usuarios');

// Conexión a la base de datos
$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

// Verificar la conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Función para registrar un nuevo usuario
function registrar_usuario($nombre, $email, $password) {
    global $conn;
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    $query = "INSERT INTO usuarios (nombre, email, password) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sss", $nombre, $email, $password_hash);
    $stmt->execute();
    return $stmt->insert_id;
}

// Función para iniciar sesión
function iniciar_sesion($email, $password) {
    global $conn;
    $query = "SELECT * FROM usuarios WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $usuario = $stmt->get_result()->fetch_assoc();
    if ($usuario && password_verify($password, $usuario['password'])) {
        return $usuario;
    } else {
        return false;
    }
}

// Función para obtener la lista de usuarios
function obtener_usuarios() {
    global $conn;
    $query = "SELECT * FROM usuarios";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $usuarios = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    return $usuarios;
}

// Función para eliminar un usuario
function eliminar_usuario($id) {
    global $conn;
    $query = "DELETE FROM usuarios WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->affected_rows;
}

// Ejemplo de uso
if (isset($_POST['registrar'])) {
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $id = registrar_usuario($nombre, $email, $password);
    if ($id) {
        echo "Usuario registrado con éxito!";
    } else {
        echo "Error al registrar usuario.";
    }
}

if (isset($_POST['iniciar_sesion'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $usuario = iniciar_sesion($email, $password);
    if ($usuario) {
        echo "Bienvenido, " . $usuario['nombre'];
    } else {
        echo "Error al iniciar sesión.";
    }
}

if (isset($_GET['eliminar'])) {
    $id = $_GET['eliminar'];
    $filas_afectadas = eliminar_usuario($id);
    if ($filas_afectadas) {
        echo "Usuario eliminado con éxito!";
    } else {
        echo "Error al eliminar usuario.";
    }
}

?>

<!-- Formulario de registro -->
<form action="" method="post">
    <label for="nombre">Nombre:</label>
    <input type="text" id="nombre" name="nombre"><br><br>
    <label for="email">Email:</label>
    <input type="email" id="email" name="email"><br><br>
    <label for="password">Contraseña:</label>
    <input type="password" id="password" name="password"><br><br>
    <input type="submit" value="Registrar" name="registrar">
</form>

<!-- Formulario de inicio de sesión -->
<form action="" method="post">
    <label for="email">Email:</label>
    <input type="email" id="email" name="email"><br><br>
    <label for="password">Contraseña:</label>
    <input type="password" id="password" name="password"><br><br>
    <input type="submit" value="Iniciar sesión" name="iniciar_sesion">
</form>

<!-- Lista de usuarios -->
<h2>Usuarios registrados:</h2>
<ul>
    <?php foreach (obtener_usuarios() as $usuario) { ?>
        <li>
            <?= $usuario['nombre'] ?> (<?= $usuario['email'] ?>)
            <a href="?eliminar=<?= $usuario['id'] ?>">Eliminar</a>
        </li>
   
?>