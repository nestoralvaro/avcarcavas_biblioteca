<?php
    include("common/header.php");
    include_once("validar_user.php");
    include_once("bbdd/conexion.php");
?>

    <div id="admin_content">
    <?php
    $validado = validar_admin();
    if (!($validado == 0 || $validado == 1)) {
    ?>
	    Usuario no validado
	    <form action="#" method="post">

		    <fieldset>
			    Usuario: <input name="user_login" type="text" id="user_login" class="textfield"  />
			    <br />
			    Password: <input name="password_login" type="password" id="password_login" class="textfield" />
			    <br />
			    <input name="submit" type="submit" id="submit_login" class="submit" value="Continuar" />
		    </fieldset>
	    </form>
	    <p><a href="./index.php">Volver</a></p>
    <?
    } else {

        // Lo primero es guardar en un array los pares idUser-NombreUser
        $listadoUsuariosAplicacion = mysql_query("select * from usuarios");
        $arrayUsuariosAplicacion = array();
        while ($rowUser = mysql_fetch_array($listadoUsuariosAplicacion)) {
            $arrayUsuariosAplicacion[$rowUser['id']] = $rowUser['nombre'];
        }
    ?>
	    <p><a href="./log_out.php">Desconectar <img src="images/logout.png" alt="log out" title="log out" style="border:0;"/></a></p>
        <div id="inserciones" style="display:none; font-weight:bold;"><!-- INICIO inserciones -->
    	    <p style="color:blue;" onclick="$('#inserciones').hide(500);$('#ediciones').show(500);">Click para gestionar libros</p>
            <p>Insertar un libro:</p>
            <form action="#" method="post">
                <table>
                    <tr>
                        <td>T&iacute;tulo</td>
                        <td><input name="titulo" type="text" id="titulo" /></td>
                    </tr>
                    <tr>
                        <td>Autor</td>
                        <td><input name="autor" type="text" id="autor"/></td>
                    </tr>
                    <tr>
                        <td>Cantidad</td>
                        <td><input name="cantidad" type="text" id="cantidad" onkeypress='return soloNumeros(event)' /></td>
                    </tr>
                    <tr>
                        <td><input name="button_1" type="button" id="book_button" onclick="submitBook()" value="Insertar Libro" /></td>
                    </tr>
                </table>
            </form>

            <p>Dar de alta a un usuario:</p>
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
        </div><!-- FIN inserciones-->
        <div id="ediciones"><!-- INICIO ediciones-->
    	    <p style="color:blue; font-weight:bold;" onclick="$('#ediciones').hide(500);$('#inserciones').show(500);">Click para insertar libros o crear usuarios</p>
            <p><b>Nota:</b> Las fechas se indican en A&ntilde;o-mes-dia. Pincha sobre los t&iacute;tulos de la tabla para ordenar los datos</p>
            <table>
                <tr id="cabecera">
                    <th id="shown_header_1">Borrar libro</th>
                    <th id="shown_header_2"><a href="?order=titulo">T&iacute;tulo</a></th>
                    <th id="shown_header_3"><a href="?order=autor">Autor</a></th>
                    <th id="shown_header_4"><a href="?order=fecha_alta">Fecha de alta</a></th>
                    <th id="shown_header_5"><a href="?order=disponibilidad">Disponibilidad</a></th>
                    <th id="shown_header_6"><a href="?order=fecha_prestamo">Fecha de pr&eacute;stamo</a></th>
                    <th id="shown_header_7"><a href="?order=fecha_devolucion">Fecha cuando se devolver&aacute;</a></th>
                    <th id="shown_header_8"><a href="?order=usuario">Usuario que tiene el libro</a></th>
                    <th id="shown_header_9">Poner libro disponible</th>
                    <th id="shown_header_10">Prestar</th>
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
                    while ($rowDisp = mysql_fetch_array($listadoDisponibilidades)) {
                        $arrayDisponibilidades[$rowDisp['id']] = $rowDisp['estado'];
                    }

                    $listadoLibros = mysql_query($queryBooks);
                    while ($row = mysql_fetch_array($listadoLibros)) {   
                            echo "<tr class='disponibilidad_" . $row['disponibilidad'] . "' id='book_" . $row['id'] . "' >";
                            echo "<td onclick=\"deleteBook('" . $row['id'] . "')\"><img src=\"images/remove.png\" alt=\"Borrar\" title=\"Borrar\" style=\"border:0;\"/></td>";
                            echo "<td>" . $row['titulo'] . "</td>";
                            echo "<td>" . $row['autor'] . "</td>";
                            echo "<td>" . $row['fecha_alta'] . "</td>";
                            echo "<td>" . $arrayDisponibilidades[$row['disponibilidad']] . "</td>";
                            echo "<td>" . $row['fecha_prestamo'] . "</td>";
                            echo "<td>" . $row['fecha_devolucion'] . "</td>";
                            if ($row['usuario'] != -1) {
                                echo "<td>" . $arrayUsuariosAplicacion[$row['usuario']] . "</td>";
                            } else {
                                echo "<td>" . $row['nombre_usuario_externo'] . "</td>";
                            }

                            // Solo si el libro esta "Prestado" (3) o con "Retraso" (4) se puede poner como disponible (Lo han devuelto)
                            if (($row['disponibilidad'] == 3) || ($row['disponibilidad'] == 4)) {
                                echo "<td onclick=\"changeBookState('" . $row['id'] . "','1')\">
                                <img src=\"images/book.png\" alt=\"Poner disponible\" title=\"Poner disponible\" style=\"border:0;\"/></td>";
                            } else {
                                echo "<td onclick=\"alert('No se puede modificar')\"><img src=\"images/delete.png\" alt=\"No se puede modificar\" title=\"No se puede modificar\" style=\"border:0;\"/></td>";
                            }
                            // Solo si el libro esta "Solicitado" (2) se puede poner como prestado (Se lo han llevado) 
                            if ($row['disponibilidad'] == 2) {
                                echo "<td onclick=\"changeBookState('" . $row['id'] . "','3')\">
                                <img src=\"images/lock.png\" alt=\"Prestar\" title=\"Prestar\" style=\"border:0;\"/></td>";
                            } else {
                                echo "<td onclick=\"alert('No se puede modificar')\"><img src=\"images/delete.png\" alt=\"No se puede modificar\" title=\"No se puede modificar\" style=\"border:0;\"/></td>";
                            }

                        echo "</tr>";
                    }
                ?>
            </table>

            <table id="hidden_header" style="display:none">
                <tr>
                    <th id="hidden_header_1">Borrar libro</th>
                    <th id="hidden_header_2"><a href="?order=titulo">T&iacute;tulo</a></th>
                    <th id="hidden_header_3"><a href="?order=autor">Autor</a></th>
                    <th id="hidden_header_4"><a href="?order=fecha_alta">Fecha de alta</a></th>
                    <th id="hidden_header_5"><a href="?order=disponibilidad">Disponibilidad</a></th>
                    <th id="hidden_header_6"><a href="?order=fecha_prestamo">Fecha de pr&eacute;stamo</a></th>
                    <th id="hidden_header_7"><a href="?order=fecha_devolucion">Fecha cuando se devolver&aacute;</a></th>
                    <th id="hidden_header_8"><a href="?order=usuario">Usuario que tiene el libro</a></th>
                    <th id="hidden_header_9">Poner libro disponible</th>
                    <th id="hidden_header_10">Prestar</th>
                </tr>
            </table>
        </div><!-- FIN ediciones-->

	    <p><a href="./log_out.php">Desconectar <img src="images/logout.png" alt="log out" title="log out" style="border:0;"/></a></p>


<script type="text/javascript">

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

        jQuery("#hidden_header_6").width(jQuery("#shown_header_6").width());    
        jQuery("#hidden_header_7").width(jQuery("#shown_header_7").width());    
        jQuery("#hidden_header_8").width(jQuery("#shown_header_8").width());    
        jQuery("#hidden_header_9").width(jQuery("#shown_header_9").width());    
        jQuery("#hidden_header_10").width(jQuery("#shown_header_10").width());        
        hidden_header.show();
    } else {
        jQuery("#hidden_header").hide();
    }
}
 
</script>

<?php
    } // FIN else
?>
    </div>
<?php
    include("common/footer.php");
?>

