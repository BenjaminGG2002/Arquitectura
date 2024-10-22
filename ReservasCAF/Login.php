<?php
session_start();
include 'conexion.php'; // Incluir el archivo de conexión a la base de datos

// Verificar si el formulario ha sido enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificar si las claves existen en $_POST antes de acceder a ellas
    $run = isset($_POST['rut']) ? $_POST['rut'] : null;
    $email = isset($_POST['email']) ? $_POST['email'] : null;
    $password = isset($_POST['password']) ? $_POST['password'] : null;

    // Asegurarse de que todos los campos estén presentes
    if ($run && $email && $password) {
        // Preparar la consulta para verificar si es un administrador
        $sql_admin = "SELECT * FROM administrador WHERE Run_Administrador = ? AND Mail_Administrador = ? AND Contraseña_Administrador = ?";
        $stmt_admin = $enlace->prepare($sql_admin);
        $stmt_admin->bind_param("sss", $run, $email, $password);
        $stmt_admin->execute();
        $result_admin = $stmt_admin->get_result();

        // Si es administrador, redirigir a la gestión de reservas
        if ($result_admin->num_rows > 0) {
            // Guardar el Run_Administrador en la sesión
            $_SESSION['run'] = $run;
            $_SESSION['tipo'] = 'administrador';
            header("Location: gestion_reservas.php"); // Redirigir a la gestión de reservas
            exit();
        }
        $stmt_admin->close();

        // Si no es administrador, verificar si es un usuario regular
        $sql_user = "SELECT * FROM usuario_duoc WHERE Run_Usuario = ? AND Mail_Usuario = ? AND Contraseña_Usuario = ?";
        $stmt_user = $enlace->prepare($sql_user);
        $stmt_user->bind_param("sss", $run, $email, $password);
        $stmt_user->execute();
        $result_user = $stmt_user->get_result();

        // Si es un usuario regular, redirigir a la página de reservas
        if ($result_user->num_rows > 0) {
            // Guardar el Run_Usuario en la sesión
            $_SESSION['run'] = $run;
            $_SESSION['tipo'] = 'usuario';
            header("Location: reservas.php"); // Redirigir a la página de reservas
            exit();
        } else {
            $error = "Credenciales incorrectas";
        }
        $stmt_user->close();
    } else {
        $error = "Todos los campos son obligatorios.";
    }
}
?>

<?php include('header.php'); ?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de sesión</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>
    <div class="login-container">
        <h2>Inicio de sesión</h2>
        <?php if (isset($error)): ?>
            <p style="color: red;"><?php echo $error; ?></p>
        <?php endif; ?>
        <form action="login.php" method="POST">
            <input type="text" name="rut" placeholder="RUT" required>
            <input type="email" name="email" placeholder="Correo" required>
            <input type="password" name="password" placeholder="Contraseña" required>
            <button type="submit">Ingresar</button>
        </form>
    </div>
</body>
</html>
