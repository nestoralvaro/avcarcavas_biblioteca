<?php
include_once("../validar_user.php");
include("conexion.php");
$validado = validar_admin();
if ($validado == 0 || $validado == 1) {

	$tituloElegido = sanitize(trim($_POST["tituloElegido"]));
	$autorElegido = sanitize(trim($_POST["autorElegido"]));
    // Se inserta un libro por cada unidad que exista
	$cantidadElegida = sanitize(trim($_POST["cantidadElegida"]));

    $model = "";
    $values = "('$tituloElegido', '$autorElegido', CURRENT_DATE, '1')";
    while($cantidadElegida-- > 1) {
        $model .= ",". $values;
    }
    $model = $values . $model;
	$sentencia_insert = "INSERT INTO `libros` (`titulo`, `autor`, `fecha_alta`, `disponibilidad`) VALUES " . $model . ";";
    mysql_query($sentencia_insert);

    // Se recupera el ID que se acaba de insertar
    $lastId = mysql_insert_id();
    // Actualizar el historico. User "0" = admin
    $sentenciaHistorico = "INSERT INTO  `historico` (`descripcion`, `id_libro` , `id_usuario`) VALUES ('Libro " . $tituloElegido . " del autor " . $autorElegido . " insertado en la biblioteca', '" . $lastId . "', '0')";

    mysql_query($sentenciaHistorico);
	echo "Alta realizada correctamente. ";
} else {
	echo "Proceso no permitido. El usuario no esta validado";
}

?> 
