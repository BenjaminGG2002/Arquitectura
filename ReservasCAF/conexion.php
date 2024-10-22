<?php
$enlace = mysqli_connect("127.0.0.1", "root", "", "reservas_caf");

if (!$enlace) {
    die("No se pudo conectar a la base de datos: " . mysqli_connect_error());
}
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
?>
