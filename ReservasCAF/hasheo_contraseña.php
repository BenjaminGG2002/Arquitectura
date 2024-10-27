<?php
// Incluye tu archivo de conexión
include 'conexion.php';

try {
    // Hasheamos contraseñas para la tabla administrador
    $query_admin = "SELECT Run_Administrador, Contraseña_Administrador FROM administrador";
    $result_admin = mysqli_query($enlace, $query_admin);

    while ($row = mysqli_fetch_assoc($result_admin)) {
        $run_admin = $row['Run_Administrador'];
        $plain_password_admin = $row['Contraseña_Administrador'];

        // Hashear la contraseña en texto plano
        $hashed_password_admin = password_hash($plain_password_admin, PASSWORD_BCRYPT);

        // Actualizar la contraseña hasheada en la base de datos
        $update_query_admin = "UPDATE administrador SET Contraseña_Administrador = '$hashed_password_admin' WHERE Run_Administrador = '$run_admin'";
        mysqli_query($enlace, $update_query_admin);
    }

    // Hasheamos contraseñas para la tabla usuario_duoc
    $query_user = "SELECT Run_Usuario, Contraseña_Usuario FROM usuario_duoc";
    $result_user = mysqli_query($enlace, $query_user);

    while ($row = mysqli_fetch_assoc($result_user)) {
        $run_user = $row['Run_Usuario'];
        $plain_password_user = $row['Contraseña_Usuario'];

        // Hashear la contraseña en texto plano
        $hashed_password_user = password_hash($plain_password_user, PASSWORD_BCRYPT);

        // Actualizar la contraseña hasheada en la base de datos
        $update_query_user = "UPDATE usuario_duoc SET Contraseña_Usuario = '$hashed_password_user' WHERE Run_Usuario = '$run_user'";
        mysqli_query($enlace, $update_query_user);
    }

    echo "Contraseñas de administradores y usuarios hasheadas con éxito.";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

// Cierra la conexión a la base de datos
mysqli_close($enlace);
?>
