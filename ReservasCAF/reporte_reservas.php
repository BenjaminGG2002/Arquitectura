<?php
session_start();
include 'conexion.php';

// Verificar si el usuario es administrador
if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] != 'administrador') {
    header("Location: login.php");
    exit();
}

// Obtener todas las reservas
$sql_reservas = "SELECT r.*, u.PNombre_Usuario, u.PApellido_Usuario, u.Run_Usuario, h.Hora_Inicio, h.Hora_Fin 
                 FROM reserva r
                 JOIN usuario_duoc u ON r.Run_Usuario = u.Run_Usuario
                 JOIN horario h ON r.Id_Horario = h.Id_Horario
                 ORDER BY r.Fecha_Reserva DESC";
$reservas_result = $enlace->query($sql_reservas);
?>
<?php include('header.php'); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Reservas</title>
    <link rel="stylesheet" href="reporte.css">
</head>
<body>
    <div class="container">
        <h2>Reporte de Reservas</h2>
        <table>
            <thead>
                <tr>
                    <th>Usuario</th>
                    <th>RUT</th>
                    <th>Fecha</th>
                    <th>Horario</th>
                    <th>Estado</th>
                    <th>Asistido</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($reserva = $reservas_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $reserva['PNombre_Usuario'] . ' ' . $reserva['PApellido_Usuario']; ?></td>
                        <td><?php echo $reserva['Run_Usuario']; ?></td>
                        <td><?php echo $reserva['Fecha_Reserva']; ?></td>
                        <td><?php echo $reserva['Hora_Inicio'] . ' - ' . $reserva['Hora_Fin']; ?></td>
                        <td><?php echo $reserva['Estado']; ?></td>
                        <td><?php echo $reserva['Asistido']; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <!-- Botón para descargar el archivo CSV -->
        <form action="descargar_reporte.php" method="POST">
            <button type="submit">Descargar CSV</button>
        </form>
        <!-- Botón para regresar a la gestión de reservas -->
        <form action="gestion_reservas.php" method="GET" style="margin-top: 20px;">
            <button type="submit">Volver</button>
        </form>
    </div>
</body>
</html>
