<?php
    include("common/header.php");
    include("common/left_menu.php");
//    include("common/right_menu.php");
    include_once("validar_user.php");
    include_once("bbdd/conexion.php");
?>


<div id="content">

    <?php
    $validado = validar_user();
    if (!($validado == 0 || $validado == 1)) {
    ?>

	    <p><a href="./admin.php">Acceso a la administraci&oacute;n</a></p>
        <div id="login_user">
	        <p>Si ya tienes tu usuario val&iacute;date para reservar libros!. <a href="#"><span onclick="$('#login_user').hide(500);$('#new_user').show(500);" style="color:blue;">Si no tienes un usuario cr&eacute;atelo! (Click)</span></a> </p>
	        <form action="#" method="post">
                <table>
                    <tr>
    			        <td>E-mail:</td>
                        <td><input name="user_login" type="text" id="user_login" class="textfield"  /></td>
                    </tr>
                    <tr>
    			        <td>Password:</td> 
                        <td><input name="password_login" type="password" id="password_login" class="textfield" /></td>
                    </tr>
                    <tr>
    			        <td><input name="submit" type="submit" id="submit_login" class="submit" value="Entrar" /></td>
                    </tr>
                </table>
	        </form>
        </div>
        <div id="new_user" style="display:none">
            <p>Crea tu cuenta. <a href="#"><span onclick="$('#new_user').hide(500);$('#login_user').show(500);" style="color:blue;">Si ya tienes una cuenta val&iacute;date! (Click)</span></a></p>
            <form action="#" method="post">
                <table>
                    <tr>
                        <td>Nombre</td>
                        <td><input name="nombre" type="text" id="nombre"/></td>
                    </tr>
                    <tr>
                        <td>E-Mail</td>
                        <td><input name="mail" type="text" id="mail" /></td>
                    </tr>
                    <tr>
                        <td>Tel&eacute;fono</td>
                        <td><input name="telf" type="text" id="telf" onkeypress='return soloNumeros(event)' /></td>
                    </tr>
                    <tr>
                        <td><input name="button_2" type="button" id="user_button" onclick="submitUser()" value="Crear Usuario" /></td>
                    </tr>
                </table>
            </form>
        </div>
        <p>Libros disponibles:</p>
        <p><b>Nota:</b> Las fechas se indican en A&ntilde;o-mes-dia. Pincha sobre los t&iacute;tulos de la tabla para ordenar los datos</p>
        <table>
            <tr id="cabecera">
                <th id="shown_header_1"><a href="?order=titulo">T&iacute;tulo</a></th>
                <th id="shown_header_2"><a href="?order=autor">Autor</a></th>
                <th id="shown_header_3"><a href="?order=disponibilidad">Disponibilidad</a></th>
                <th id="shown_header_4"><a href="?order=fecha_devolucion">Fecha cuando se devolver&aacute;</a></th>
                <th id="shown_header_5">Realizar reserva</th>
            </tr>
            <?php
                // Select
                $queryBooks = "select * from libros";
                // Is there any "ALLOWED" order selected?
            	$chosenOrder = trim($_GET["order"]);
                if ($chosenOrder != "" 
                    && ($chosenOrder == "titulo"
                        || $chosenOrder == "autor"
                        || $chosenOrder == "fecha_alta"
                        || $chosenOrder == "disponibilidad"
                        || $chosenOrder == "fecha_prestamo"
                        || $chosenOrder == "fecha_devolucion"
                        || $chosenOrder == "usuario")
                ) {
                    $queryBooks .= " order by `".$chosenOrder . "`";
                }

                /*

                **Disponible	Libro presente en la biblioteca disponible para se...
                - Poner como disponible
                Solicitado	Libro solicitado por un usuario, pero aún presente...
                - Este estado lo solicita sólo el usuario??? (Reserva por el administrador=> 1-crear usuario/2-elegir usuario)
                **Prestado	Libro no presente en la biblioteca por estar en po...
                - Poner libro como prestado
                Con retraso	Libro que debería estar en la biblioteca pero aún ...
                - Cambio de estado automático (a las 9:00 todos los días + envío de mails de los que esten con retraso)

                */

                $listadoDisponibilidades = mysql_query("select * from disponibilidad");
                $arrayDisponibilidades = array();
                $arrayDisponibilidades[] = "";
                while ($rowDisp = mysql_fetch_array($listadoDisponibilidades)) {
                    $arrayDisponibilidades[] = $rowDisp['estado'];
                }

                $listadoLibros = mysql_query($queryBooks);
                while ($row = mysql_fetch_array($listadoLibros)) {   
                        echo "<tr class='disponibilidad_" . $row['disponibilidad'] . "' id='book_" . $row['id'] . "' >";
                        echo "<td>" . $row['titulo'] . "</td>";
                        echo "<td>" . $row['autor'] . "</td>";
                        echo "<td>" . $arrayDisponibilidades[$row['disponibilidad']] . "</td>";
                        if (($row['disponibilidad'] != '1')
                            && ($row['disponibilidad'] != '2')) {
                            echo "<td>" . $row['fecha_devolucion'] . "</td>";
                        } else {
                            echo "<td> -- </td>";
                        }
                        if ($row['disponibilidad'] != '1') {
                            echo "<td><img src=\"images/delete.png\" alt=\"No disponible\" title=\"No disponible\" style=\"border:0;\"/></td>";
                        } else {
                            echo "<td onclick=\"reservarBookExterno('" . $row['id'] . "', '" . $row['titulo'] . "')\"><a href=\"#\">Reservar
                            <img src=\"images/lock.png\" alt=\"Reservar\" title=\"Reservar\" style=\"border:0;\"/></a></td>";
                        }
                    echo "</tr>";
                }
            ?>
        </table>


        <table id="hidden_header" style="display:none">
            <tr>
                <th id="hidden_header_1"><a href="?order=titulo">T&iacute;tulo</a></th>
                <th id="hidden_header_2"><a href="?order=autor">Autor</a></th>
                <th id="hidden_header_3"><a href="?order=disponibilidad">Disponibilidad</a></th>
                <th id="hidden_header_4"><a href="?order=fecha_devolucion">Fecha cuando se devolver&aacute;</a></th>
                <th id="hidden_header_5">Realizar reserva</th>
            </tr>
        </table>

    <?php
    } else {


        // Recoger los datos del usuario actual
		$nick = sanitize(trim($_SESSION["nick"]));
		$pass = sanitize(trim($_SESSION["pass"]));
        $selectDatosUsuario = "select * from `usuarios` where `mail` = '" . $nick . "' and `password` = '" . $pass . "'";
        $datosUsuario = mysql_query($selectDatosUsuario);
        $arrayUsuarios = array();
        while ($rowUsuario = mysql_fetch_array($datosUsuario)) {
            $arrayUsuarios[] = $rowUsuario;
        }
        $usuario = $arrayUsuarios[0];
        echo "<p>Bienvenido " . $usuario['nombre'] . "</p>";

    ?>
	    <p><a href="./log_out.php">Desconectar <img src="images/logout.png" alt="log out" title="log out" style="border:0;"/></a></p>
        <p>Libros disponibles:</p>
        <p><b>Nota:</b> Las fechas se indican en A&ntilde;o-mes-dia. Pincha sobre los t&iacute;tulos de la tabla para ordenar los datos</p>
        <table>
            <tr id="cabecera">
                <th id="shown_header_1"><a href="?order=titulo">T&iacute;tulo</a></th>
                <th id="shown_header_2"><a href="?order=autor">Autor</a></th>
                <th id="shown_header_3"><a href="?order=disponibilidad">Disponibilidad</a></th>
                <th id="shown_header_4"><a href="?order=fecha_devolucion">Fecha cuando se devolver&aacute;</a></th>
                <th id="shown_header_5">Realizar reserva</th>
            </tr>

            <?php
                // Select
                $queryBooks = "select * from libros";
                // Is there any "ALLOWED" order selected?
            	$chosenOrder = trim($_GET["order"]);
                if ($chosenOrder != "" 
                    && ($chosenOrder == "titulo"
                        || $chosenOrder == "autor"
                        || $chosenOrder == "fecha_alta"
                        || $chosenOrder == "disponibilidad"
                        || $chosenOrder == "fecha_prestamo"
                        || $chosenOrder == "fecha_devolucion"
                        || $chosenOrder == "usuario")
                ) {
                    $queryBooks .= " order by `".$chosenOrder . "`";
                }

                /*

                **Disponible	Libro presente en la biblioteca disponible para se...
                - Poner como disponible
                Solicitado	Libro solicitado por un usuario, pero aún presente...
                - Este estado lo solicita sólo el usuario??? (Reserva por el administrador=> 1-crear usuario/2-elegir usuario)
                **Prestado	Libro no presente en la biblioteca por estar en po...
                - Poner libro como prestado
                Con retraso	Libro que debería estar en la biblioteca pero aún ...
                - Cambio de estado automático (a las 9:00 todos los días + envío de mails de los que esten con retraso)

                */

                $listadoDisponibilidades = mysql_query("select * from disponibilidad");
                $arrayDisponibilidades = array();
                $arrayDisponibilidades[] = "";
                while ($rowDisp = mysql_fetch_array($listadoDisponibilidades)) {
                    $arrayDisponibilidades[] = $rowDisp['estado'];
                }

                $listadoLibros = mysql_query($queryBooks);
                while ($row = mysql_fetch_array($listadoLibros)) {   
                        echo "<tr class='disponibilidad_" . $row['disponibilidad'] . "' id='book_" . $row['id'] . "' >";
                        echo "<td>" . $row['titulo'] . "</td>";
                        echo "<td>" . $row['autor'] . "</td>";
                        echo "<td>" . $arrayDisponibilidades[$row['disponibilidad']] . "</td>";
                        if (($row['disponibilidad'] != '1')
                            && ($row['disponibilidad'] != '2')) {
                            echo "<td>" . $row['fecha_devolucion'] . "</td>";
                        } else {
                            echo "<td> -- </td>";
                        }
                        if ($row['disponibilidad'] != '1') {
                            // Si es un libro "Solicitado" y lo ha solicitado el propio usuario, se puede anular la reserva
                            if ($row['disponibilidad'] == '2' && $row['usuario'] == $usuario['id']) {
                                echo "<td onclick=\"anularReservaBook('" . $row['id'] . "', '" . $row['titulo'] . "')\"><a href=\"#\">Anular reserva<img src=\"images/check.png\" alt=\"Anular reserva\" title=\"Anular reserva\" style=\"border:0;\"/></a></td>"; 
                            } else {
                                echo "<td><img src=\"images/delete.png\" alt=\"No disponible\" title=\"No disponible\" style=\"border:0;\"/></td>";                           
                            }
                        } else {
                            echo "<td onclick=\"reservarBook('" . $row['id'] . "', '" . $row['titulo'] . "')\"><a href=\"#\">Reservar
                            <img src=\"images/lock.png\" alt=\"Reservar\" title=\"Reservar\" style=\"border:0;\"/></a></td>";
                        }
                    echo "</tr>";
                }
            ?>
        </table>

        <table id="hidden_header" style="display:none">
            <tr>
                <th id="hidden_header_1"><a href="?order=titulo">T&iacute;tulo</a></th>
                <th id="hidden_header_2"><a href="?order=autor">Autor</a></th>
                <th id="hidden_header_3"><a href="?order=disponibilidad">Disponibilidad</a></th>
                <th id="hidden_header_4"><a href="?order=fecha_devolucion">Fecha cuando se devolver&aacute;</a></th>
                <th id="hidden_header_5">Realizar reserva</th>
            </tr>
        </table>

	    <p><a href="./log_out.php">Desconectar <img src="images/logout.png" alt="log out" title="log out" style="border:0;"/></a></p>
    <?php
    }
?>

</div>

<script type="text/javascript" >

// Attach scroll event
jQuery(window).scroll(function () { 
    check();
});

function check() {
    var tableHeaderYPos = getTableHeaderYPos();
    var tableHeaderXPos = getTableHeaderXPos();
    var yScroll = getYScroll();
//    alert("yScroll: " + yScroll + " --- tableHeaderPos::" + tableHeaderPos);
    if (yScroll > tableHeaderYPos) {
        var hidden_header = jQuery("#hidden_header");
        hidden_header.css({position: "absolute", top:yScroll, left:tableHeaderXPos, zIndex:10});
        // Set proper width
        jQuery("#hidden_header_1").width(jQuery("#shown_header_1").width());
        jQuery("#hidden_header_2").width(jQuery("#shown_header_2").width());
        jQuery("#hidden_header_3").width(jQuery("#shown_header_3").width());
        jQuery("#hidden_header_4").width(jQuery("#shown_header_4").width());
        jQuery("#hidden_header_5").width(jQuery("#shown_header_5").width());    
        hidden_header.show();
    } else {
        jQuery("#hidden_header").hide();
    }
}
 
</script>



<?php

    include("common/right_menu.php");
    include("common/footer.php");
?>

