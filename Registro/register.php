<?php
// Configuración de la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tienda_deportiva";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener los datos del formulario
$nombre_usuario = $_POST['nombre_usuario'] ?? '';
$contrasena = $_POST['contrasena'] ?? '';
$superpoderes = isset($_POST['superpoderes']) ? 1 : 0; // 1 si está marcado, 0 si no

// Encriptar la contraseña
$contrasena_encriptada = password_hash($contrasena, PASSWORD_BCRYPT);

// Preparar la sentencia SQL
$sql = "INSERT INTO usuarios (nombre_usuario, contrasena, poderes) VALUES (?, ?, ?)";

// Preparar y vincular
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $nombre_usuario, $contrasena_encriptada, $superpoderes);

// Ejecutar la sentencia
if ($stmt->execute()) {
    if ($superpoderes) {
        // Redirigir a la página de bienvenida (registro.html) si tiene superpoderes
        header("Location: http://localhost/programacion/jefe/ventas.html");
    } else {
        // Redirigir a la página del carrito de compras (index.html) si no tiene superpoderes
        header("Location: http://localhost/programacion/index.html");

    }
    exit(); // Detener la ejecución después de la redirección
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

// Cerrar la conexión
$stmt->close();
$conn->close();
?>
