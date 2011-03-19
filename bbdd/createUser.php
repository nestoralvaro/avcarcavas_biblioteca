<?php
include_once("../validar_user.php");
include("conexion.php");
include("randomGenerator.php");
$validado = validar_admin();

$notAdminOK = false;
// Si viene de la página indicada es supone que es como si estuviera validado
$ref = explode("?",$_SERVER['HTTP_REFERER']);

if ((strcmp($ref[0], "http://www.monogatari.es/avcarcavas/biblioteca/index.php")==0)
    ||(strcmp($ref[0], "http://monogatari.es/avcarcavas/biblioteca/index.php")==0)
    ||(strcmp($ref[0], "http://www.monogatari.es/avcarcavas/biblioteca/log_out.php")==0)
    ||(strcmp($ref[0], "http://monogatari.es/avcarcavas/biblioteca/log_out.php")==0)) {
    $notAdminOK = true;
}

// return strpos($content,$str) ? true : false;

if ($validado == 0 || $validado == 1 || $notAdminOK) {

	$nombreElegido = sanitize(trim($_POST["nombreElegido"]));
	$mailElegido = sanitize(trim($_POST["mailElegido"]));
	$telfElegido = sanitize(trim($_POST["telfElegido"]));

    // El e-mail es el único dato que no se puede repetir
    $mail_counter = "select * from `usuarios` where `mail` = '" . $mailElegido . "'";
    $result = mysql_query($mail_counter, $link);
    $rowSize = mysql_num_rows($result); 

    if($rowSize > 0) {
        echo "ERROR. Ya existe un usuario con ese e-mail";
    } else {
        // Función para obtener el password del usuario
        $password = get_rand_pass(6);
        // 1- Se hace el insert
	    $sentencia_insert = "INSERT INTO `usuarios` (`nombre`, `mail`, `password`, `telf`, `fecha_alta`) VALUES ('" . $nombreElegido . "', '" . $mailElegido . "', '" . $password . "', '" . $telfElegido . "', CURRENT_DATE);";
        mysql_query($sentencia_insert);

            $cabeceras = "MIME-Version: 1.0 \n";
            $cabeceras .= "Content-type: text/html; charset=UTF-8 \n";
            $cabeceras .= "From: Biblioteca Carcavas-San Antonio <biblioteca_carcavas@monogatari.es> \n";

            $texto = "Hola, <b>" . $nombreElegido . "</b><br/>";
            $texto .= "Bienvenido la <a href=\"http://monogatari.es/avcarcavas/biblioteca\">biblioteca online de las Carcavas-San Antonio</a><br/>";
            $texto .= "Tu password es:<b>" . $password . "</b><br/><br/>";
            $texto .= "Muchas gracias por usar este servicio de la <a href=\"http://avcarcavas.wordpress.com\">Asociacion de Vecinos Carcavas-San Antonio</a><br/>\n";
	        $mail_body = wordwrap($texto, 70);
            mail($mailElegido,  "Biblioteca Carcavas-San Antonio", $mail_body, $cabeceras);

	    echo "Alta realizada correctamente.\n Comprueba tu e-mail para ver el password asignado para reservar libros";      
    }
} else {
	echo "Proceso no permitido. Se produjo un error.";
}

?> 
