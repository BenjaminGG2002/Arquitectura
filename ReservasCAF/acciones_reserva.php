<?php
session_start();
include 'conexion.php';

if (!isset($_SESSION['run'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_reserva']) && isset($_POST['accion'])) {
    $id_reserva = $_POST['id_reserva'];
    $accion = $_POST['accion'];

    switch ($accion) {
        case 'confirmar':
            $sql = "UPDATE reserva SET Estado = 'Confirmada' WHERE Id_Reserva = '$id_reserva'";
            break;
        case 'eliminar':
            $sql = "DELETE FROM reserva WHERE Id_Reserva = '$id_reserva'";
            break;
        case 'modificar':
            // Verificar que los nuevos valores de fecha y horario estén presentes
            if (isset($_POST['fecha']) && isset($_POST['id_horario'])) {
                $nueva_fecha = $_POST['fecha'];
                $nuevo_horario = $_POST['id_horario'];
                
                // Obtener la fecha actual
                $fecha_actual = date('Y-m-d');

                // Verificar que la fecha no sea anterior a la actual
                if ($nueva_fecha < $fecha_actual) {
                    echo "<script>alert('No se puede modificar la reserva a una fecha anterior a la actual.'); window.history.back();</script>";
                    exit();
                }

                // Verificar que la fecha sea un día entre lunes (1) y viernes (5)
                $dia_semana = date('N', strtotime($nueva_fecha));
                if ($dia_semana < 1 || $dia_semana > 5) {
                    echo "<script>alert('Solo se pueden modificar reservas a días entre lunes y viernes.'); window.history.back();</script>";
                    exit();
                }

                // Si todo está bien, actualizar la reserva
                $sql = "UPDATE reserva SET Fecha_Reserva = '$nueva_fecha', Id_Horario = '$nuevo_horario', Estado = 'Pendiente' WHERE Id_Reserva = '$id_reserva'";
            } else {
                echo "<script>alert('Debe proporcionar una nueva fecha y horario para modificar la reserva.'); window.history.back();</script>";
                exit();
            }
            break;
    }

    // Ejecutar la consulta si está definida
    if (isset($sql) && $enlace->query($sql) === TRUE) {
        echo "<script>alert('Acción realizada con éxito.'); window.location.href = 'reservas.php';</script>";
    } else {
        echo "<script>alert('Error al procesar la acción: " . $enlace->error . "');</script>";
    }
}
?>
