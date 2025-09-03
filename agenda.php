<?php

$host = "localhost";
$dbname = "agenda_db";
$usuario = "root";
$contrasena = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $usuario, $contrasena);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}


if (isset($_POST['agregar'])) {
    $stmt = $pdo->prepare("INSERT INTO contactos (nombre, telefono, email) VALUES (?, ?, ?)");
    $stmt->execute([$_POST['nombre'], $_POST['telefono'], $_POST['email']]);
    header("Location: agenda.php");
    exit;
}


if (isset($_POST['editar'])) {
    $stmt = $pdo->prepare("UPDATE contactos SET nombre=?, telefono=?, email=? WHERE id=?");
    $stmt->execute([$_POST['nombre'], $_POST['telefono'], $_POST['email'], $_POST['id']]);
    header("Location: agenda.php");
    exit;
}


if (isset($_GET['eliminar'])) {
    $stmt = $pdo->prepare("DELETE FROM contactos WHERE id=?");
    $stmt->execute([$_GET['eliminar']]);
    header("Location: agenda.php");
    exit;
}


$stmt = $pdo->query("SELECT * FROM contactos ORDER BY nombre");
$contactos = $stmt->fetchAll(PDO::FETCH_ASSOC);


$editar = null;
if (isset($_GET['editar'])) {
    $stmt = $pdo->prepare("SELECT * FROM contactos WHERE id=?");
    $stmt->execute([$_GET['editar']]);
    $editar = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Agenda de Contactos</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { border-collapse: collapse; width: 70%; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background: #f4f4f4; }
        form { margin-top: 20px; }
        input, button { padding: 6px; margin: 4px 0; }
    </style>
</head>
<body>
    <h1>Agenda de Contactos</h1>

    <h2><?= $editar ? "Editar contacto" : "Nuevo contacto" ?></h2>
    <form method="post">
        <input type="hidden" name="id" value="<?= $editar['id'] ?? '' ?>">
        <label>Nombre:</label><br>
        <input type="text" name="nombre" value="<?= $editar['nombre'] ?? '' ?>" required><br>
        <label>Teléfono:</label><br>
        <input type="text" name="telefono" value="<?= $editar['telefono'] ?? '' ?>" required><br>
        <label>Email:</label><br>
        <input type="email" name="email" value="<?= $editar['email'] ?? '' ?>"><br>
        <button type="submit" name="<?= $editar ? 'editar' : 'agregar' ?>">
            <?= $editar ? "Actualizar" : "Guardar" ?>
        </button>
        <?php if ($editar): ?>
            <a href="agenda.php">Cancelar</a>
        <?php endif; ?>
    </form>

    <h2>Lista de contactos</h2>
    <table>
        <tr>
            <th>Nombre</th>
            <th>Teléfono</th>
            <th>Email</th>
            <th>Acciones</th>
        </tr>
        <?php foreach ($contactos as $c): ?>
        <tr>
            <td><?= htmlspecialchars($c['nombre']) ?></td>
            <td><?= htmlspecialchars($c['telefono']) ?></td>
            <td><?= htmlspecialchars($c['email']) ?></td>
            <td>
                <a href="agenda.php?editar=<?= $c['id'] ?>">Editar</a> | 
                <a href="agenda.php?eliminar=<?= $c['id'] ?>" onclick="return confirm('¿Eliminar este contacto?')">Eliminar</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
