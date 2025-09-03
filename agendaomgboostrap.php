<?php
// ------------------ CONFIG BD ------------------
$host = "localhost";
$dbname = "agenda_db";
$usuario = "root";
$contrasena = "";

// Conexión PDO
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $usuario, $contrasena);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

// ------------------ ACCIONES CRUD ------------------
// Agregar
if (isset($_POST['agregar'])) {
    $stmt = $pdo->prepare("INSERT INTO contactos (nombre, telefono, email) VALUES (?, ?, ?)");
    $stmt->execute([$_POST['nombre'], $_POST['telefono'], $_POST['email']]);
    header("Location: agendaomgboostrap.php");
    exit;
}
// Editar
if (isset($_POST['editar'])) {
    $stmt = $pdo->prepare("UPDATE contactos SET nombre=?, telefono=?, email=? WHERE id=?");
    $stmt->execute([$_POST['nombre'], $_POST['telefono'], $_POST['email'], $_POST['id']]);
    header("Location: agendaomgboostrap.php");
    exit;
}
// Eliminar
if (isset($_GET['eliminar'])) {
    $stmt = $pdo->prepare("DELETE FROM contactos WHERE id=?");
    $stmt->execute([$_GET['eliminar']]);
    header("Location: agendaomgboostrap.php");
    exit;
}

// ------------------ DATOS PARA MOSTRAR ------------------
$stmt = $pdo->query("SELECT * FROM contactos ORDER BY nombre");
$contactos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Si se va a editar, cargamos el contacto
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
    <meta charset="UTF-8" />
    <title>Agenda con Bootstrap + DataTables</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <!-- Bootstrap 5 (CSS) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- DataTables + Bootstrap 5 (CSS) -->
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <!-- Responsive (CSS) -->
    <link href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <style>
        body { background: #f8fafc; }
        .card { border-radius: 14px; }
        table.dataTable tbody td { vertical-align: middle; }
        /* Evitar que los botones de acción se rompan en varias líneas */
        .acciones a { margin-right: .25rem; white-space: nowrap; }
    </style>
</head>
<body>
<nav class="navbar navbar-dark bg-dark">
  <div class="container">
    <span class="navbar-brand">📒 Agenda</span>
  </div>
</nav>

<div class="container my-4">

  <!-- Formulario dentro de una card -->
  <div class="card mb-4">
    <div class="card-header">
      <strong><?= $editar ? "✏️ Editar contacto" : "➕ Nuevo contacto" ?></strong>
    </div>
    <div class="card-body">
      <form method="post" class="row g-3">
        <input type="hidden" name="id" value="<?= htmlspecialchars($editar['id'] ?? '') ?>">

        <div class="col-md-4">
          <label class="form-label">Nombre</label>
          <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($editar['nombre'] ?? '') ?>" required>
        </div>

        <div class="col-md-4">
          <label class="form-label">Teléfono</label>
          <input type="text" name="telefono" class="form-control" value="<?= htmlspecialchars($editar['telefono'] ?? '') ?>" required>
        </div>

        <div class="col-md-4">
          <label class="form-label">Email</label>
          <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($editar['email'] ?? '') ?>">
        </div>

        <div class="col-12">
          <button type="submit" name="<?= $editar ? 'editar' : 'agregar' ?>" class="btn btn-primary">
            <?= $editar ? '<i class="bi bi-heptagon"></i>' : '<i class="bi bi-upload"></i>' ?>
          </button>
          <?php if ($editar): ?>
            <a class="btn btn-outline-secondary" href="agenda.php"><i class="bi bi-power"></i></a>
          <?php endif; ?>
        </div>
      </form>
    </div>
  </div>

  <!-- Tabla dentro de una card -->
  <div class="card">
    <div class="card-header">
      <strong>Contactos</strong>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table id="tablaContactos" class="table table-striped table-bordered nowrap" style="width:100%">
          <thead class="table-light">
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
                <a class="btn btn-sm btn-outline-primary" href="agendaomgboostrap.php?editar=<?= $c['id'] ?>"><i class="bi bi-pencil"></i></a>
                <a class="btn btn-sm btn-outline-danger" href="agendaomgboostrap.php?eliminar=<?= $c['id'] ?>" onclick="return confirm('¿Eliminar este contacto?')"><i class="bi bi-trash3-fill"></i></a>
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

</div>

<!-- jQuery (requerido por DataTables) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Bootstrap 5 (JS bundle con Popper) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- DataTables + Bootstrap 5 (JS) -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<!-- Responsive (JS) -->
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
  $('#tablaContactos').DataTable({
    pageLength: 5,
    lengthMenu: [5,10,25,50,100],
    responsive: true,
    columnDefs: [
      { orderable: false, targets: 3 }
    ],
    language: {
      paginate: {
        previous: '<i class="bi bi-chevron-double-left"></i>',  // ícono anterior
        next: '<i class="bi bi-chevron-double-right"></i>'      // ícono siguiente
      }
    }
  });
});
</script>
</body>
</html>
