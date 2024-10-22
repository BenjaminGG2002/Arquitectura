<?php
session_start();
include 'conexion.php';

// Verificar si el usuario es administrador
if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] != 'administrador') {
    header("Location: login.php");
    exit();
}

// Obtener todas las reservas
$sql_reservas = "SELECT r.*, u.PNombre_Usuario, u.PApellido_Usuario, h.Hora_Inicio, h.Hora_Fin 
                 FROM reserva r
                 JOIN usuario_duoc u ON r.Run_Usuario = u.Run_Usuario
                 JOIN horario h ON r.Id_Horario = h.Id_Horario
                 ORDER BY r.Fecha_Reserva DESC";
$reservas_result = $enlace->query($sql_reservas);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_reserva']) && isset($_POST['accion'])) {
    $id_reserva = $_POST['id_reserva'];
    $accion = $_POST['accion'];

    // Procesar las acciones de confirmación, modificación y eliminación
    switch ($accion) {
        case 'confirmar':
            $sql_confirmar = "UPDATE reserva SET Estado = 'Confirmada' WHERE Id_Reserva = '$id_reserva'";
            $enlace->query($sql_confirmar);
            break;

        case 'eliminar':
            $sql_eliminar = "DELETE FROM reserva WHERE Id_Reserva = '$id_reserva'";
            $enlace->query($sql_eliminar);
            break;

        case 'modificar':
            if (isset($_POST['fecha']) && isset($_POST['id_horario'])) {
                $nueva_fecha = $_POST['fecha'];
                $nuevo_horario = $_POST['id_horario'];

                $fecha_actual = date('Y-m-d');
                $dia_semana = date('N', strtotime($nueva_fecha));

                if ($nueva_fecha < $fecha_actual) {
                    echo "<script>alert('No se puede modificar la reserva a una fecha anterior a la actual.');</script>";
                } elseif ($dia_semana < 1 || $dia_semana > 5) {
                    echo "<script>alert('Solo se pueden hacer reservas de lunes a viernes.');</script>";
                } else {
                    $sql_modificar = "UPDATE reserva SET Fecha_Reserva = '$nueva_fecha', Id_Horario = '$nuevo_horario', Estado = 'Pendiente' WHERE Id_Reserva = '$id_reserva'";
                    $enlace->query($sql_modificar);
                    echo "<script>alert('Reserva modificada con éxito.'); window.location.href = 'gestion_reservas.php';</script>";
                }
            } else {
                echo "<script>alert('Debe proporcionar una nueva fecha y horario para modificar la reserva.');</script>";
            }
            break;

        case 'asistido':
            $sql_asistido = "UPDATE reserva SET Asistido = 'Asistido' WHERE Id_Reserva = '$id_reserva'";
            $enlace->query($sql_asistido);
            break;

        case 'no_asistido':
            $sql_no_asistido = "UPDATE reserva SET Asistido = 'No asistido' WHERE Id_Reserva = '$id_reserva'";
            $enlace->query($sql_no_asistido);
            break;
    }

    header("Location: gestion_reservas.php");
    exit();
}
?>
<?php include('header.php'); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Reservas</title>
    <link rel="stylesheet" href="gestion.css">
</head>
<body>
    <div class="container">
        <h2>Gestión de Reservas</h2>
        <table>
            <thead>
                <tr>
                    <th>Usuario</th>
                    <th>Fecha</th>
                    <th>Horario</th>
                    <th>Estado</th>
                    <th>Asistencia</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($reserva = $reservas_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $reserva['PNombre_Usuario'] . ' ' . $reserva['PApellido_Usuario']; ?></td>
                        <td><?php echo $reserva['Fecha_Reserva']; ?></td>
                        <td><?php echo $reserva['Hora_Inicio'] . ' - ' . $reserva['Hora_Fin']; ?></td>
                        <td><?php echo $reserva['Estado']; ?></td>
                        <td><?php echo $reserva['Asistido']; ?></td>
                        <td>
                            <form method="POST" action="gestion_reservas.php">
                                <input type="hidden" name="id_reserva" value="<?php echo $reserva['Id_Reserva']; ?>">
                                <button type="submit" name="accion" value="confirmar">Confirmar</button>
                                <button type="submit" name="accion" value="modificar">Modificar</button>
                                <button type="submit" name="accion" value="eliminar">Eliminar</button>
                                <button type="submit" name="accion" value="asistido">Marcar como Asistido</button>
                                <button type="submit" name="accion" value="no_asistido">Marcar como No Asistido</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <!-- Botón para redirigir a la página de reporte -->
        <a href="reporte_reservas.php" class="button">Ir a Reporte de Reservas</a>

        <!-- Botón de cerrar sesión -->
        <form action="login.php" method="POST" style="margin-top: 20px;">
            <button type="submit">Salir</button>
        </form>
    </div>
</body>
</html>
