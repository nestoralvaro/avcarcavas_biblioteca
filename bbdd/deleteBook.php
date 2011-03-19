<?php
include_once("../validar_user.php");
include("conexion.php");
$validado = validar_admin();
if ($validado == 0 || $validado == 1) {

	$libroElegido = sanitize(trim($_POST["book"]));

	$sentencia_delete = "DELETE FROM `libros` WHERE `id` = " . $libroElegido;
    mysql_query($sentencia_delete);

    // Actualizar el historico. User "0" = admin
    $sentenciaHistorico = "INSERT INTO  `historico` (`descripcion`, `id_libro` , `id_usuario`) VALUES ('Libro borrado de la biblioteca', '" . $libroElegido . "', '0')";
    mysql_query($sentenciaHistorico);


	echo "Libro borrado correctamente.";
} else {
	echo "Proceso no permitido. El usuario no esta validado";
}

?> 
