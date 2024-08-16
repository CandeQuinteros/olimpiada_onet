<?php
header('Content-Type: application/json');

$mysqli = new mysqli('localhost', 'root', 'password', 'tienda_deportiva');

if ($mysqli->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Error de conexión']));
}

$result = $mysqli->query("SELECT * FROM productos");

$productos = [];
while ($row = $result->fetch_assoc()) {
    $productos[] = $row;
}

echo json_encode($productos);

$mysqli->close();
?>
