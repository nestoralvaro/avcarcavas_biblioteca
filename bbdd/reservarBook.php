<?php
include_once("../validar_user.php");
include("conexion.php");
$validado = validar_user();
if ($validado == 0 || $validado == 1) {

	$libroElegido = sanitize(trim($_POST["book"]));
	$usuarioReserva = -1;

    // Get user ID
	$nick = sanitize(trim($_SESSION["nick"]));
	$pass = sanitize(trim($_SESSION["pass"]));
    $select_users = "select * from `usuarios` where `mail` = '" . $nick . "' and `password` = '" . $pass . "'";
    $resultUsers = mysql_query($select_users);
    while ($row = mysql_fetch_array($resultUsers)) {   
        $usuarioReserva = $row['id'];
    }

	$sentencia_update = "UPDATE `libros` SET  `disponibilidad` = '2', `fecha_prestamo` = CURRENT_DATE, `usuario` = " . $usuarioReserva . " WHERE `id` = " . $libroElegido;
    mysql_query($sentencia_update);

    // Actualizar el historico.
    $sentenciaHistorico = "INSERT INTO  `historico` (`descripcion` , `id_libro` , `id_usuario`) VALUES ('Libro solicitado',  '" . $libroElegido . "',  '" . $usuarioReserva . "')";
    mysql_query($sentenciaHistorico);
	echo "Libro reservado correctamente. ";
} else {
	echo "Proceso no permitido. El usuario no esta validado";
}

?> 
