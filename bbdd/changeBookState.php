<?php
include_once("../validar_user.php");
include("conexion.php");
$validado = validar_admin();
if ($validado == 0 || $validado == 1) {

	$libroElegido = sanitize(trim($_POST["book"]));
	$estadoElegido = sanitize(trim($_POST["state"]));

    $sentenciaHistorico = "";
    // Poner disponible
    if ($estadoElegido == 1) {
    	$sentencia_update = "UPDATE `libros` SET  `disponibilidad` = '" . $estadoElegido . "', `fecha_prestamo` = NULL, `fecha_devolucion` = NULL, `usuario` = NULL WHERE `id` = " . $libroElegido;
        // Actualizar el historico. User "0" = admin
        $sentenciaHistorico = "INSERT INTO  `historico` (`descripcion` , `id_libro` , `id_usuario`, `fecha_devolucion`) VALUES ('Libro devuelto',  '" . $libroElegido . "',  '0', DATE_ADD(CURDATE(), INTERVAL 15 DAY))";

    // Prestar
    } else {
        $dniElegido = sanitize(trim($_POST["dni_reserva"]));
    	$sentencia_update = "UPDATE `libros` SET  `disponibilidad` = '" . $estadoElegido . "', `fecha_prestamo` = CURRENT_DATE, `fecha_devolucion` = DATE_ADD(CURDATE(), INTERVAL 15 DAY), `DNI` = '" . $dniElegido . "' WHERE `id` = " . $libroElegido;
        // Actualizar el historico. User "0" = admin
        $sentenciaHistorico = "INSERT INTO  `historico` (`descripcion` , `id_libro` , `id_usuario` , `fecha_entrega`, `DNI`) VALUES ('Libro prestado',  '" . $libroElegido . "',  '0', CURRENT_DATE, '" . $dniElegido . "')";
    }
//    echo $sentencia_update;
    mysql_query($sentencia_update);
    mysql_query($sentenciaHistorico);
	echo "Estado cambiado correctamente. ";
} else {
	echo "Proceso no permitido. El usuario no esta validado";
}

?> 
