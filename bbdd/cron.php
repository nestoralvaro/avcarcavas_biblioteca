<?php
include("conexion.php");

    // Se marca el comienzo del CRON
    $sentenciaInicioCron = "INSERT INTO  `historico` (`descripcion`) VALUES ('CRON: Inicio de la ejecucion')";
    mysql_query($sentenciaInicioCron);

    /**
    *
    * Libros solicitados No recogidos
    *
    */
    // Primero se comprueban todos los libros que hayan estado solicitados más de 3 días
    $listadoPrestadosPasados = mysql_query("SELECT * FROM  `libros` WHERE `libros`.`disponibilidad` =2 AND (CURRENT_DATE > DATE_ADD(`libros`.`fecha_prestamo`, INTERVAL 3 DAY))");

    $modificacionLibros = "";
    $modificacionHistorico = "";
    while ($rowPrestadoPasado = mysql_fetch_array($listadoPrestadosPasados)) {
        // Se cogen todos los IDs de esos libros para ponerlos como disponibles
    	$modificacionLibros .= $rowPrestadoPasado['id'] . ",";
        // Se actualizan los datos en el histórico. User "0" = admin
        $modificacionHistorico .= "('Libro no recogido. Reserva anulada',  '" . $rowPrestadoPasado['id'] . "',  '0', CURRENT_DATE),";
    }

    // Se prepara el update para modificar el estado de los libros
    if ($modificacionLibros != "") {
        // Se actualizan los datos en el histórico.
    	$modificacionLibros = substr($modificacionLibros, 0, -1); 
    	$sentencia_update_prestados_pasados = "UPDATE `libros` SET  `disponibilidad` = '1', `fecha_prestamo` = NULL, `fecha_devolucion` = NULL, `usuario` = NULL WHERE `id` in (" . $modificacionLibros . ")";
//        echo "sentencia_update " . $sentencia_update_prestados_pasados;
        mysql_query($sentencia_update_prestados_pasados);
    }

    // Se prepara el insert para actualizar el histórico
    if ($modificacionHistorico != "") {
        // Se actualizan los datos en el histórico.
    	$modificacionHistorico = substr($modificacionHistorico, 0, -1); 
        $sentenciaHistoricoPrestadosPasados = "INSERT INTO  `historico` (`descripcion` , `id_libro` , `id_usuario`, `fecha_devolucion`) VALUES " . $modificacionHistorico;
//        echo "sentenciaHistoricoPrestadosPasados " . $sentenciaHistoricoPrestadosPasados;
        mysql_query($sentenciaHistoricoPrestadosPasados);
    }


    /**
    *
    * Libros que deberían haber sido devueltos
    *
    */


    // Primero se comprueban todos los libros prestados que tengan fecha_devolucion < hoy
    $listadoPrestadosMultados = mysql_query("SELECT * FROM  `libros` WHERE `disponibilidad` = 3 AND CURRENT_DATE > `fecha_devolucion`");

    $modificacionLibros = "";
    $modificacionUsuarios = "";
    $modificacionHistorico = "";
    $listaDNI = "";
    while ($rowPrestadoMultado = mysql_fetch_array($listadoPrestadosMultados)) {
        // Se cogen todos los IDs de esos libros para marcarlos como "Con retraso"
    	$modificacionLibros .= $rowPrestadoMultado['id'] . ",";
        // Se cogen todos los IDs de los usuarios de esos libros para marcarlos como "Multado"
    	$modificacionUsuarios .= $rowPrestadoMultado['usuario'] . ",";
        // Se actualizan los datos en el histórico. User "0" = admin
        $modificacionHistorico .= "('Libro no devuelto. Entrega con Retraso.',  '" . $rowPrestadoMultado['id'] . "',  '0'),";
        // Se almacenan los DNI
        $listaDNI .= "<li> " . $rowPrestadoMultado['DNI'] . "</li>";
    }

    // Se prepara el update para actualizar el estado del libro
    if ($modificacionLibros != "") {
        // Se actualizan los datos en el histórico.
    	$modificacionLibros = substr($modificacionLibros, 0, -1); 
    	$sentencia_update_prestados_multados = "UPDATE `libros` SET  `disponibilidad` = '4' WHERE `id` in (" . $modificacionLibros . ")";
//        echo "sentencia_update " . $sentencia_update_prestados_multados;
        mysql_query($sentencia_update_prestados_multados);
    }


    $cabeceras = "MIME-Version: 1.0 \n";
    $cabeceras .= "Content-type: text/html; charset=UTF-8 \n";
    $cabeceras .= "From: Biblioteca Carcavas-San Antonio <biblioteca_carcavas@monogatari.es> \n";

    // Se manda el mail con los DNIs al administrador
    if ($listaDNI != "") {
            $mailAdmin = "avcarcavas@hotmail.com";
//        $mailAdmin = "nestoralvaro@gmail.com";
        $textoAdmin = "Hola, <b>Administrador</b><br/>";
        $textoAdmin .= "Acaba de expirar el prestamo de los libros de los usuarios con DNI: <b><ul>" . $listaDNI . "</ul></b>";
        $mail_body_admin = wordwrap($textoAdmin, 70);
        mail($mailAdmin,  "Listado de DNI con prestamo del libro Vencido", $mail_body_admin, $cabeceras);
    }

    // Se le apunta al usuario como multado
    if ($modificacionUsuarios != "") {
        // Se actualizan los datos en el histórico.
    	$modificacionUsuarios = substr($modificacionUsuarios, 0, -1); 
    	$sentencia_update_usuario_multado = "UPDATE `usuarios` SET  `multas` = `multas` + 1, `fecha_ultima_multa` = CURRENT_DATE WHERE `id` in (" . $modificacionUsuarios . ") AND id != -1";
//        echo "sentencia_update " . $sentencia_update_usuario_multado;
        mysql_query($sentencia_update_usuario_multado);

        $listadoMailMultados = mysql_query("SELECT * FROM  `usuarios` WHERE `id` in (" . $modificacionUsuarios . ")  AND id != -1");
        while ($rowPrestadoMultado = mysql_fetch_array($listadoMailMultados)) {
        
            $texto = "Hola, <b>" . $rowPrestadoMultado['nombre'] . "</b><br/>";
            $texto .= "Acaba de expirar el prestamo de un libro que tienes de la <a href=\"http://monogatari.es/avcarcavas/biblioteca\">biblioteca online de las Carcavas-San Antonio</a>. Por favor ponte en contacto con nosotros cuanto antes para renovar el prestamo.<br/><br/>";
            $texto .= "Muchas gracias por usar este servicio de la <a href=\"http://avcarcavas.wordpress.com\">Asociacion de Vecinos Carcavas-San Antonio</a><br/>\n";
            $mail_body = wordwrap($texto, 70);
            mail($rowPrestadoMultado['mail'],  "Prestamo del libro Vencido", $mail_body, $cabeceras);
        }
    }

    // Se actualizan los datos en el histórico. User "0" = admin
    if ($modificacionHistorico != "") {
        // Se actualizan los datos en el histórico.
    	$modificacionHistorico = substr($modificacionHistorico, 0, -1); 
        $sentenciaHistoricoPrestadosMultados = "INSERT INTO  `historico` (`descripcion` , `id_libro` , `id_usuario`) VALUES " . $modificacionHistorico;
//        echo "sentenciaHistoricoPrestadosMultados " . $sentenciaHistoricoPrestadosMultados;
        mysql_query($sentenciaHistoricoPrestadosMultados);
    }

    // Se marca el comienzo del CRON
    $sentenciaFinCron = "INSERT INTO  `historico` (`descripcion`) VALUES ('CRON: Fin de la ejecucion')";
    mysql_query($sentenciaFinCron);

?> 
