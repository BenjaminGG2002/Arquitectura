<?php
session_start();
include 'conexion.php';

if (!isset($_SESSION['run'])) {
    header("Location: login.php");
    exit();
}

// Obtener el ID de la reserva
$id_reserva = $_GET['id_reserva'] ?? null;

if ($id_reserva) {
    // Obtener los datos de la reserva específica
    $sql_reserva = "SELECT * FROM reserva WHERE Id_Reserva = '$id_reserva'";
    $reserva_result = $enlace->query($sql_reserva);
    $reserva = $reserva_result->fetch_assoc();

    // Obtener los horarios disponibles
    $sql_horarios = "SELECT * FROM horario";
    $horarios_result = $enlace->query($sql_horarios);
    $horarios = [];
    while ($horario = $horarios_result->fetch_assoc()) {
        $horarios[$horario['Id_Horario']] = $horario;
    }
}
?>
<?php include('header.php'); ?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificar Reserva</title>
    <link rel="stylesheet" href="modificar.css">
</head>
<body>
    <div class="container">
        <h2>Modificar Reserva</h2> <!-- Título centrado -->
        <div class="main-content">
            <form method="POST" action="acciones_reserva.php">
                <input type="hidden" name="id_reserva" value="<?php echo $id_reserva; ?>">

                <label for="fecha">Fecha:</label>
                <input type="date" id="fecha" name="fecha" value="<?php echo $reserva['Fecha_Reserva']; ?>" required>

                <label for="id_horario">Horario:</label>
                <select name="id_horario" required>
                    <?php foreach ($horarios as $id_horario => $horario): ?>
                        <option value="<?php echo $id_horario; ?>" <?php echo $id_horario == $reserva['Id_Horario'] ? 'selected' : ''; ?>>
                            <?php echo $horario['Hora_Inicio'] . ' - ' . $horario['Hora_Fin']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <button type="submit" name="accion" value="modificar">Guardar Cambios</button>
            </form>
        </div>
    </div>
</body>
</html>