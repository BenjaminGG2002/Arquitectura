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

// Preparar el archivo CSV
$filename = "reporte_reservas_" . date('Ymd') . ".csv";
header("Content-Type: text/csv; charset=UTF-8");
header("Content-Disposition: attachment; filename=$filename");

// Abrir el archivo en memoria
$output = fopen('php://output', 'w');

// Usar punto y coma como separador
fputcsv($output, array('Usuario', 'RUT', 'Fecha', 'Horario', 'Estado', 'Asistido'), ';');

// Verificar si la consulta tiene resultados
if ($reservas_result && $reservas_result->num_rows > 0) {
    // Añadir las filas
    while ($reserva = $reservas_result->fetch_assoc()) {
        fputcsv($output, array(
            $reserva['PNombre_Usuario'] . ' ' . $reserva['PApellido_Usuario'],
            $reserva['Run_Usuario'],
            date("d-m-Y", strtotime($reserva['Fecha_Reserva'])), // Cambiar formato de fecha
            $reserva['Hora_Inicio'] . ' - ' . $reserva['Hora_Fin'],
            $reserva['Estado'],
            $reserva['Asistido']
        ), ';');
    }
} else {
    // Mensaje en caso de que no haya resultados
    fputcsv($output, array('No hay datos disponibles'), ';');
}

// Cerrar el archivo en memoria
fclose($output);
exit();
