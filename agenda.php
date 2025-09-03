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

// Agregar contacto
if (isset($_POST['agregar'])) {
    $stmt = $pdo->prepare("INSERT INTO contactos (nombre, telefono, email) VALUES (?, ?, ?)");
    $stmt->execute([$_POST['nombre'], $_POST['telefono'], $_POST['email']]);
    header("Location: agenda.php"); exit;
}

// Editar contacto
if (isset($_POST['editar'])) {
    $stmt = $pdo->prepare("UPDATE contactos SET nombre=?, telefono=?, email=? WHERE id=?");
    $stmt->execute([$_POST['nombre'], $_POST['telefono'], $_POST['email'], $_POST['id']]);
    header("Location: agenda.php"); exit;
}

// Eliminar contacto
if (isset($_GET['eliminar'])) {
    $stmt = $pdo->prepare("DELETE FROM contactos WHERE id=?");
    $stmt->execute([$_GET['eliminar']]);
    header("Location: agenda.php"); exit;
}

// Obtener contactos
$contactos = $pdo->query("SELECT * FROM contactos ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);

// Contacto a editar
$editar = null;
if (isset($_GET['editar'])) {
    $stmt = $pdo->prepare("SELECT * FROM contactos WHERE id=?");
    $stmt->execute([$_GET['editar']]);
    $editar = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Agenda de Contactos</title>
<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f8f9fa;
        margin: 0;
        padding: 20px;
    }

    h1, h2 {
        text-align: center;
        color: #333;
    }

    form {
        width: 400px;
        margin: 0 auto 30px auto;
        padding: 20px;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

    form label {
        font-weight: bold;
    }

    form input {
        width: 100%;
        padding: 8px;
        margin: 6px 0 12px 0;
        border: 1px solid #ccc;
        border-radius: 6px;
        box-sizing: border-box;
    }

    form button {
        background-color: #007bff;
        color: #fff;
        padding: 10px 16px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-size: 16px;
        margin-right: 10px;
    }

    form button:hover {
        background-color: #0056b3;
    }

    form a {
        text-decoration: none;
        color: #dc3545;
        font-weight: bold;
    }

    table {
        width: 80%;
        margin: 0 auto;
        border-collapse: collapse;
        background: #fff;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

    table th, table td {
        padding: 12px 16px;
        text-align: left;
    }

    table th {
        background-color: #007bff;
        color: white;
    }

    table tr:nth-child(even) {
        background-color: #f2f2f2;
    }

    table tr:hover {
        background-color: #e9ecef;
    }

    .acciones a {
        text-decoration: none;
        padding: 6px 10px;
        border-radius: 6px;
        margin-right: 4px;
        font-size: 14px;
    }

    .acciones a.editar {
        background-color: #28a745;
        color: white;
    }

    .acciones a.editar:hover {
        background-color: #218838;
    }

    .acciones a.eliminar {
        background-color: #dc3545;
        color: white;
    }

    .acciones a.eliminar:hover {
        background-color: #c82333;
    }

</style>
</head>
<body>

<h1>Agenda de Contactos</h1>

<h2><?= $editar ? "Editar contacto" : "Nuevo contacto" ?></h2>
<form method="post">
    <input type="hidden" name="id" value="<?= $editar['id'] ?? '' ?>">
    <label>Nombre:</label>
    <input type="text" name="nombre" value="<?= $editar['nombre'] ?? '' ?>" required>

    <label>Teléfono:</label>
    <input type="text" name="telefono" value="<?= $editar['telefono'] ?? '' ?>" required>

    <label>Email:</label>
    <input type="email" name="email" value="<?= $editar['email'] ?? '' ?>">

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
        <td class="acciones">
            <a class="editar" href="agenda.php?editar=<?= $c['id'] ?>">Editar</a>
            <a class="eliminar" href="agenda.php?eliminar=<?= $c['id'] ?>" onclick="return confirm('¿Eliminar este contacto?')">Eliminar</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

</body>
</html>
