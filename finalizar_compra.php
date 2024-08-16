<?php
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$carrito = $input['carrito'];
$total = array_sum(array_column($carrito, 'precio'));

$mysqli = new mysqli('localhost', 'root', 'password', 'tienda_deportiva');

if ($mysqli->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Error de conexión']));
}

$mysqli->begin_transaction();

try {
    $stmt = $mysqli->prepare("INSERT INTO pedidos (id_usuario, total) VALUES (1, ?)");
    $stmt->bind_param('d', $total);
    $stmt->execute();
    $id_pedido = $mysqli->insert_id;
    $stmt->close();

    foreach ($carrito as $item) {
        $stmt = $mysqli->prepare("INSERT INTO detalles_pedido (id_pedido, id_producto, cantidad, precio) VALUES (?, ?, 1, ?)");
        $stmt->bind_param('iid', $id_pedido, $item['id'], $item['precio']);
        $stmt->execute();
        $stmt->close();
    }

    $mysqli->commit();
    echo json_encode(['success' => true]);

} catch (Exception $e) {
    $mysqli->rollback();
    echo json_encode(['success' => false, 'message' => 'Error al registrar la compra']);
}

$mysqli->close();
?>
