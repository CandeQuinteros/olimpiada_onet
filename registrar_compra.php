<?php
// registrar_compra.php

header('Content-Type: application/json');

// Conexión a la base de datos
$conexion = new mysqli("localhost", "usuario", "contraseña", "nombre_base_datos");

if ($conexion->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Error de conexión a la base de datos']));
}

// Obtener los datos enviados desde el frontend
$datosCompra = json_decode(file_get_contents('php://input'), true);
$carrito = $datosCompra['carrito'];
$total = $datosCompra['total'];
$fecha = date('Y-m-d H:i:s');

// Insertar la compra en la base de datos
$consulta = $conexion->prepare("INSERT INTO compras (total, fecha, estado) VALUES (?, ?, 'pendiente de entrega')");
$consulta->bind_param('ds', $total, $fecha);

if ($consulta->execute()) {
    $idCompra = $consulta->insert_id;

    // Insertar los productos de la compra
    $consultaProducto = $conexion->prepare("INSERT INTO productos_comprados (compra_id, producto_id, cantidad) VALUES (?, ?, ?)");

    foreach ($carrito as $producto) {
        $consultaProducto->bind_param('iii', $idCompra, $producto['id'], $producto['cantidad']);
        $consultaProducto->execute();
    }

    echo json_encode(['success' => true, 'message' => 'Compra registrada con éxito']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al registrar la compra']);
}

$consulta->close();
$conexion->close();
?>
