<?php
include("conexion.php");


$libroElegido = sanitize(trim($_POST["book"]));
$usuarioReservaExterna = sanitize(trim($_POST["usuario_externo"]));

$sentencia_update = "UPDATE `libros` SET  `disponibilidad` = '2', `fecha_prestamo` = CURRENT_DATE, `usuario` = '-1', `nombre_usuario_externo` = '" . $usuarioReservaExterna . "' WHERE `id` = " . $libroElegido;
mysql_query($sentencia_update);
// echo $sentencia_update . "\n";
// Actualizar el historico.
$sentenciaHistorico = "INSERT INTO  `historico` (`descripcion` , `id_libro` , `id_usuario`, `nombre_usuario_externo`) VALUES ('Libro solicitado',  '" . $libroElegido . "',  '-1', '" . $usuarioReservaExterna . "')";
mysql_query($sentenciaHistorico);
// echo $sentenciaHistorico . "\n";
echo "Libro reservado correctamente. ";

?> 
