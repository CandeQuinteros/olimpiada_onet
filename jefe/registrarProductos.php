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
} else {
    echo "Conexión exitosa.<br>";
}

// Obtener los datos del formulario
$codigo_producto = $_POST['codigo'] ?? '';
$descripcion = $_POST['descripcion'] ?? '';
$precio = $_POST['precio'] ?? '';  // Nota: Asegúrate de que este valor sea numérico

// Mostrar los valores recibidos para depuración
echo "Código del Producto: " . htmlspecialchars($codigo_producto) . "<br>";
echo "Descripción: " . htmlspecialchars($descripcion) . "<br>";
echo "Precio: " . htmlspecialchars($precio) . "<br>";

// Verificar que los campos no estén vacíos
if (empty($codigo_producto) || empty($descripcion) || empty($precio)) {
    die("Todos los campos son obligatorios.");
} else {
    echo "Todos los campos fueron completados.<br>";
}

// Preparar la sentencia SQL
$sql = "INSERT INTO productos (codigo_producto, descripcion, precio) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);

// Verificar si la preparación fue exitosa
if ($stmt === false) {
    die("Error al preparar la consulta: " . $conn->error);
} else {
    echo "Consulta preparada exitosamente.<br>";
}

// Vincular parámetros y ejecutar
$stmt->bind_param("ssd", $codigo_producto, $descripcion, $precio);

// Verificar si la ejecución fue exitosa
if ($stmt->execute() === false) {
    die("Error al ejecutar la consulta: " . $stmt->error);
} else {
    echo "Producto cargado exitosamente.<br>";
}

// Cerrar la conexión
$stmt->close();
$conn->close();
?>
