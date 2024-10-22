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

// Preparar el archivo CSV
$filename = "reporte_reservas_" . date('Ymd') . ".csv";
header("Content-Type: text/csv");
header("Content-Disposition: attachment; filename=$filename");

$output = fopen('php://output', 'w');

// Encabezados del CSV
fputcsv($output, array('Usuario', 'RUT', 'Fecha', 'Horario', 'Estado', 'Asistido'));

// Añadir las filas
while ($reserva = $reservas_result->fetch_assoc()) {
    fputcsv($output, array(
        $reserva['PNombre_Usuario'] . ' ' . $reserva['PApellido_Usuario'],
        $reserva['Run_Usuario'],
        $reserva['Fecha_Reserva'],
        $reserva['Hora_Inicio'] . ' - ' . $reserva['Hora_Fin'],
        $reserva['Estado'],
        $reserva['Asistido']
    ));
}

fclose($output);
exit();
