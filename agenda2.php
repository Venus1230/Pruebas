<?php
// ---------- Conexión a la base de datos ----------
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

// ---------- Agregar contacto ----------
if (isset($_POST['agregar'])) {
    $stmt = $pdo->prepare("INSERT INTO contactos (nombre, telefono, email) VALUES (?, ?, ?)");
    $stmt->execute([$_POST['nombre'], $_POST['telefono'], $_POST['email']]);
    header("Location: agenda2.php");
    exit;
}

// ---------- Editar contacto ----------
if (isset($_POST['editar'])) {
    $stmt = $pdo->prepare("UPDATE contactos SET nombre=?, telefono=?, email=? WHERE id=?");
    $stmt->execute([$_POST['nombre'], $_POST['telefono'], $_POST['email'], $_POST['id']]);
    header("Location: agenda2.php");
    exit;
}

// ---------- Eliminar contacto ----------
if (isset($_GET['eliminar'])) {
    $stmt = $pdo->prepare("DELETE FROM contactos WHERE id=?");
    $stmt->execute([$_GET['eliminar']]);
    header("Location: agenda2.php");
    exit;
}

// ---------- Obtener lista completa (para renderizar la tabla) ----------
$stmt = $pdo->query("SELECT * FROM contactos ORDER BY nombre");
$contactos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ---------- Si se solicita editar, traer ese contacto ----------
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
    <title>Agenda con DataTables</title>

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .container { max-width: 1100px; margin: 0 auto; }
        table { width: 100%; }
        form { margin-bottom: 20px; }
        input, button { padding: 6px; margin: 6px 0; }
        .acciones a { margin-right: 8px; }
    </style>
</head>
<body>
<div class="container">
    <h1>Agenda de Contactos</h1>

    <!-- Formulario agregar / editar -->
    <h2><?= $editar ? "Editar contacto" : "Nuevo contacto" ?></h2>
    <form method="post" id="formContacto">
        <input type="hidden" name="id" value="<?= htmlspecialchars($editar['id'] ?? '') ?>">
        <label>Nombre:</label><br>
        <input type="text" name="nombre" value="<?= htmlspecialchars($editar['nombre'] ?? '') ?>" required><br>

        <label>Teléfono:</label><br>
        <input type="text" name="telefono" value="<?= htmlspecialchars($editar['telefono'] ?? '') ?>" required><br>

        <label>Email:</label><br>
        <input type="email" name="email" value="<?= htmlspecialchars($editar['email'] ?? '') ?>"><br>

        <button type="submit" name="<?= $editar ? 'editar' : 'agregar' ?>">
            <?= $editar ? "Actualizar" : "Guardar" ?>
        </button>
        <?php if ($editar): ?>
            <a href="agenda2.php">Cancelar</a>
        <?php endif; ?>
    </form>

    <!-- Tabla de contactos (DataTables la hará interactiva) -->
    <table id="tablaContactos" class="display">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Teléfono</th>
                <th>Email</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($contactos as $c): ?>
            <tr>
                <td><?= htmlspecialchars($c['nombre']) ?></td>
                <td><?= htmlspecialchars($c['telefono']) ?></td>
                <td><?= htmlspecialchars($c['email']) ?></td>
                <td class="acciones">
                    <a href="agenda2.php?editar=<?= $c['id'] ?>">Editar</a>
                    <a href="agenda2.php?eliminar=<?= $c['id'] ?>" onclick="return confirm('¿Eliminar este contacto?')">Eliminar</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- jQuery (necesario para DataTables) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>

<script>
$(document).ready(function() {
    // Inicializamos DataTables sobre la tabla con id 'tablaContactos'
    $('#tablaContactos').DataTable({
        pageLength: 2   ,               // filas por página
        lengthMenu: [5,10,25,50,100], // opciones para filas por página
        columnDefs: [
            { orderable: false, targets: 3 } // la columna 3 (acciones) NO es ordenable
        ]
    });
});
</script>
</body>
</html>
