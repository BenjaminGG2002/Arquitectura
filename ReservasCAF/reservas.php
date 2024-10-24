<?php
session_start();
include 'conexion.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['run']) || !isset($_SESSION['tipo'])) {
    header("Location: login.php");
    exit();
}

$run_usuario = $_SESSION['run'];
$tipo_usuario = $_SESSION['tipo'];

// Verificar si es un usuario regular, y no un administrador
if ($tipo_usuario == 'administrador') {
    header("Location: gestion_reservas.php");
    exit();
}

// Cargar la información del usuario si es un usuario regular
$sql_usuario = "SELECT * FROM usuario_duoc WHERE Run_Usuario = ?";
$stmt_usuario = $enlace->prepare($sql_usuario);
$stmt_usuario->bind_param("s", $run_usuario);
$stmt_usuario->execute();
$usuario_result = $stmt_usuario->get_result();

if ($usuario_result->num_rows > 0) {
    $usuario = $usuario_result->fetch_assoc();
} else {
    echo "<script>alert('Error: No se encontró el usuario en la base de datos.'); window.location.href = 'login.php';</script>";
    exit();
}

// Obtener los horarios disponibles
$sql_horarios = "SELECT * FROM horario";
$horarios_result = $enlace->query($sql_horarios);
$horarios = [];
while ($horario = $horarios_result->fetch_assoc()) {
    $horarios[$horario['Id_Horario']] = $horario;
}

// Procesar una nueva reserva
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['fecha']) && isset($_POST['id_horario'])) {
    $fecha_reserva = $_POST['fecha'];
    $id_horario = $_POST['id_horario'];

    // Obtener la fecha actual
    $fecha_actual = date('Y-m-d');

    // Verificar que la fecha no sea anterior a la actual
    if ($fecha_reserva < $fecha_actual) {
        echo "<script>alert('No se puede reservar en una fecha anterior a la actual.');</script>";
    } else {
        // Verificar que la fecha sea un día entre lunes (1) y viernes (5)
        $dia_semana = date('N', strtotime($fecha_reserva));
        if ($dia_semana < 1 || $dia_semana > 5) {
            echo "<script>alert('Solo se pueden hacer reservas de lunes a viernes.');</script>";
        } else {
            // Verificar si el usuario ya tiene una reserva en la misma fecha
            $sql_check = "SELECT * FROM reserva WHERE Run_Usuario = ? AND Fecha_Reserva = ?";
            $stmt_check = $enlace->prepare($sql_check);
            $stmt_check->bind_param("ss", $run_usuario, $fecha_reserva);
            $stmt_check->execute();
            $check_result = $stmt_check->get_result();

            if ($check_result->num_rows > 0) {
                echo "<script>alert('Ya tienes una reserva para esta fecha. Solo se permite una reserva por día.');</script>";
            } else {
                // Insertar la nueva reserva
                $sql_insert = "INSERT INTO reserva (Fecha_Reserva, Run_Usuario, Id_Horario, Estado, Fecha_Actividad, Limite_Usuario) 
                               VALUES (?, ?, ?, 'Pendiente', CURDATE(), 20)";
                $stmt_insert = $enlace->prepare($sql_insert);
                $stmt_insert->bind_param("ssi", $fecha_reserva, $run_usuario, $id_horario);

                if ($stmt_insert->execute()) {
                    echo "<script>alert('Reserva realizada con éxito.'); window.location.href = 'reservas.php';</script>";
                } else {
                    echo "<script>alert('Error al realizar la reserva: " . $enlace->error . "');</script>";
                }
            }
        }
    }
}

// Obtener las reservas del usuario
$sql_reservas = "SELECT r.*, h.Hora_Inicio, h.Hora_Fin FROM reserva r 
                 JOIN horario h ON r.Id_Horario = h.Id_Horario 
                 WHERE r.Run_Usuario = ?";
$stmt_reservas = $enlace->prepare($sql_reservas);
$stmt_reservas->bind_param("s", $run_usuario);
$stmt_reservas->execute();
$reservas_result = $stmt_reservas->get_result();
$reservas = [];
while ($reserva = $reservas_result->fetch_assoc()) {
    $reservas[] = $reserva;
}
?>

<?php include('header.php'); ?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservas</title>
    <link rel="stylesheet" href="reservas.css">
</head>
<body>
    <div class="container">
        <div class="main-content">
            <h2>Selecciona un día y horario para tu reserva</h2>
            <form method="POST" action="reservas.php">
                <label for="fecha">Fecha:</label>
                <input type="date" id="fecha" name="fecha" required>

                <div class="label-horario">
                    <label for="id_horario">Horario:</label>
                    <select name="id_horario" required>
                        <?php foreach ($horarios as $id_horario => $horario): ?>
                            <option value="<?php echo $id_horario; ?>">
                                <?php echo $horario['Hora_Inicio'] . ' - ' . $horario['Hora_Fin']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button type="submit">Reservar</button>
            </form>

            <h3>Mis Reservas</h3>
            <?php if (count($reservas) > 0): ?>
                <ul>
                <?php foreach ($reservas as $reserva): ?>
                    <li>
                        Fecha: <?php echo $reserva['Fecha_Reserva']; ?> | 
                        Horario: <?php echo $reserva['Hora_Inicio'] . ' - ' . $reserva['Hora_Fin']; ?> | 
                        Estado: <?php echo $reserva['Estado']; ?>
                        <form method="POST" action="acciones_reserva.php">
                            <input type="hidden" name="id_reserva" value="<?php echo $reserva['Id_Reserva']; ?>">
                            <button type="submit" name="accion" value="confirmar" class="button">Confirmar</button>
                            <button type="submit" name="accion" value="eliminar" class="button">Eliminar</button>
                        </form>
                        <form method="GET" action="modificar_reserva.php">
                            <input type="hidden" name="id_reserva" value="<?php echo $reserva['Id_Reserva']; ?>">
                            <button type="submit" class="button">Modificar</button>
                        </form>
                    </li>
                <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>No tienes reservas registradas.</p>
            <?php endif; ?>
        </div>

        <div class="user-info">
            <h3>Información del Usuario</h3>
            <?php if ($usuario): ?>
                <p><strong>Tipo de Usuario:</strong> <?php echo $usuario['Tipo_Usuario']; ?></p>
                <p><strong>Nombre Completo:</strong> <?php echo $usuario['PNombre_Usuario'] . ' ' . $usuario['SNombre_Usuario'] . ' ' . $usuario['PApellido_Usuario'] . ' ' . $usuario['SApellido_Usuario']; ?></p>
                <p><strong>RUT:</strong> <?php echo $usuario['Run_Usuario']; ?></p>
                <p><strong>Correo:</strong> <?php echo $usuario['Mail_Usuario']; ?></p>
                <p><strong>Fecha de Ingreso:</strong> <?php echo $usuario['Fecha_Registro']; ?></p>
            <?php else: ?>
                <p>Error: No se pudo obtener la información del usuario.</p>
            <?php endif; ?>
            <form action="login.php" method="POST">
                <button type="submit" class="button">Salir</button>
            </form>
        </div>
    </div>
</body>
</html>
