// Fix para corregir el error del trim en IE
if(typeof String.prototype.trim !== 'function') {
  String.prototype.trim = function() {
    return this.replace(/^\s+|\s+$/g, ''); 
  }
}

// Permitir únicamente la entrada de números, enter y backspace
function soloNumeros(evt) {
    // NOTE: Backspace = 8, Enter = 13, '0' = 48, '9' = 57
    var key = evt.keyCode ? evt.keyCode : evt.which ;
    return (key <= 40 || (key >= 48 && key <= 57));
}

// Validates email format
function validateMail(email) {
    var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
    if(reg.test(email) == false) {
        return false;
    } else {
        return true;
    }
}

// Invocación ajax para almacenar un libro
function submitBook() {
	var tituloElegido = jQuery("#titulo").val();
	var autorElegido = jQuery("#autor").val();
	var cantidadElegida = jQuery("#cantidad").val();

    if (tituloElegido.trim() === "" || autorElegido.trim() === "") {
        alert("El titulo y el autor no pueden estar en blanco");
        return false;
    }
    if (!(cantidadElegida > 0)) {
        alert("Cantidad no valida");
        return false;
    }
	var datos = "tituloElegido=" + tituloElegido;
    datos += "&autorElegido=" + autorElegido;
    datos += "&cantidadElegida=" + cantidadElegida;
	$.ajax({
		type: "POST",
		async:true,
		url: "bbdd/createBook.php",
		data: datos,
		success: function(datosRetorno){
			alert(datosRetorno);
            window.location.reload();
		}
	});
}


// Invocación ajax para borrar un libro
function deleteBook(id) {
    highlightBook(id);
    if(!confirm('realmente desea borrar el libro?')) {
        removeHighlight(id);
        return false;
    }
	var datos = "book=" + id;
	$.ajax({
		type: "POST",
		async:true,
		url: "bbdd/deleteBook.php",
		data: datos,
		success: function(datosRetorno){
//			alert(datosRetorno);
            removeHighlight(id);
            window.location.reload();
		}
	});
}

// Invocación ajax para cambiar el estado de un libro
function changeBookState(id, state) {
    highlightBook(id);
	var datos = "book=" + id;
	datos += "&state=" + state;
    var dni_reserva;
    // Se va a poner disponible
    if (state == 1) {
        if(!confirm('Confirma que el libro se ha recibido y vuelve a estar disponible')) {
            removeHighlight(id);
            return false;
        }
    }
    // Se va a prestar un libro
    if (state == 3) {
        // Hay que pedir el DNI
        dni_reserva = prompt('Introduce el DNI del usuario que se lleva el libro:');
        if (!dni_reserva) {
            alert("El DNI no puede estar vacio. No se se realiza la reserva");
            removeHighlight(id);
            return false;
        } else {
            // Se valida el NIF / NIE
            var resultadoValidacion = validaNifCifNie(dni_reserva);
            if (!(resultadoValidacion == 1 || resultadoValidacion == 3)) {
                alert('formato del DNI incorrecto. No se se realiza la reserva');
                removeHighlight(id);
                return false;
            // Se añade el DNI a la request
            } else {
            	datos += "&dni_reserva=" + dni_reserva;
            }
        }
    }
	$.ajax({
		type: "POST",
		async:true,
		url: "bbdd/changeBookState.php",
		data: datos,
		success: function(datosRetorno){
//			alert(datosRetorno);
            removeHighlight(id);
            window.location.reload();
		}
	});
}

// Invocación ajax para almacenar un usuario
function submitUser() {
	var nombreElegido = jQuery("#nombre").val();
	var mailElegido = jQuery("#mail").val();
	var telfElegido = jQuery("#telf").val();

    if(nombreElegido.trim() === "" ) {
        alert("El nombre no puede estar vacio");
        return false;
    }

    if(!validateMail(mailElegido)) {
        alert("Direccion de e-mail no valida");
        return false;
    }

    if(telfElegido.length != 9 ) {
        alert("El telefono debe contener 9 numeros");
        return false;
    }

	var datos = "nombreElegido=" + nombreElegido;
    datos += "&mailElegido=" + mailElegido;
    datos += "&telfElegido=" + telfElegido;
	$.ajax({
		type: "POST",
		async:true,
		url: "bbdd/createUser.php",
		data: datos,
		success: function(datosRetorno){
			alert(datosRetorno);
		}
	});
}


// Invocación ajax para reservar un libro
function reservarBook(id, titulo) {
    highlightBook(id);
	var datos = "book=" + id;
    if(!confirm('realmente deseas reservar el libro '+ titulo+'?')){
        removeHighlight(id);
        return false;
    }
	$.ajax({
		type: "POST",
		async:true,
		url: "bbdd/reservarBook.php",
		data: datos,
		success: function(datosRetorno){
//			alert(datosRetorno);
            alert('Recuerda que dispones de 3 dias para recoger el libro, sino lo recoges estara disponible para otro usuario');
            removeHighlight(id);
            window.location.reload();
		}
	});
}


// Invocación ajax para anular la reserva de un libro
function anularReservaBook(id, titulo) {
    highlightBook(id);
	var datos = "book=" + id;
    if(!confirm('realmente deseas anular la reserva del libro '+ titulo+'?')) {
        removeHighlight(id);
        return false;
    }
	$.ajax({
		type: "POST",
		async:true,
		url: "bbdd/anularReservaBook.php",
		data: datos,
		success: function(datosRetorno){
			alert(datosRetorno);
            removeHighlight(id);
            window.location.reload();
		}
	});
}

// Invocación ajax para que un usuario no registrado pueda reservar un libro
function reservarBookExterno(id, titulo) {

    highlightBook(id);
	var datos = "book=" + id;
    var usuario_externo = prompt('Si realmente deseas reservar el libro '+ titulo + '\n introduce tu nombre');
    if (!usuario_externo) {
        removeHighlight(id);
        alert("Nombre no válido");
        return false;
    }
    
    if (usuario_externo != "" && usuario_externo.trim() != "") {
        datos += "&usuario_externo=" + usuario_externo;
	    $.ajax({
		    type: "POST",
		    async:true,
		    url: "bbdd/reservarBookExterno.php",
		    data: datos,
		    success: function(datosRetorno){
//    			alert(datosRetorno);
                alert('Recuerda que dispones de 3 dias para recoger el libro, sino lo recoges estara disponible para otro usuario');
                removeHighlight(id);
                window.location.reload();
		    }
	    });
    } else {
        removeHighlight(id);
        alert('El nombre introducido no es valido');
    }
}

// Resalta un libro para que sea sencillo identificarlo
function highlightBook(id){
    var chosenId = "#book_" + id;
    jQuery(chosenId).addClass("highlight");
    // Prepare the magic
    jQuery(chosenId).hide();
    // Ta-chan!!! (element shows updated without waiting to till the end of the main function)
    jQuery(chosenId).show();
}

// Quita el resaltado de un libro
function removeHighlight(id){
    var chosenId = "#book_" + id;
    jQuery(chosenId).removeClass("highlight");
}

/** Funciones para calcular la posicion del documento relativa a la ventana */
// Finds the Y position of the table header
function getTableHeaderYPos() {
    var curtop = 0;
    var obj = document.getElementById("cabecera");
    if (obj.offsetParent) {	
        do {
            curtop += obj.offsetTop;	
        } while (obj = obj.offsetParent);
    }
    return curtop;
}

// Finds the X position of the table header
function getTableHeaderXPos() {
    var curleft = 0;
    var obj = document.getElementById("cabecera");
    if (obj.offsetParent) {	
        do {
            curleft += obj.offsetLeft;
        } while (obj = obj.offsetParent);
    }
    return curleft;
}

// Finds the scroll position of a page
function getYScroll() {
    var yScroll = 0;
    if (self.pageYOffset) {
	    yScroll = self.pageYOffset;
    } else if (document.documentElement && document.documentElement.scrollTop) {
	    yScroll = document.documentElement.scrollTop;
    } else if (document.body) {// all other Explorers
	    yScroll = document.body.scrollTop;
    }
    return yScroll;
}

// Finds the scroll position of a page
function getYScroll() {
    var yScroll = 0;
    if (self.pageYOffset) {
	    yScroll = self.pageYOffset;
    } else if (document.documentElement && document.documentElement.scrollTop) {
	    yScroll = document.documentElement.scrollTop;
    } else if (document.body) {// all other Explorers
	    yScroll = document.body.scrollTop;
    }
    return yScroll;
}

/** FUnciones auxiliares **/
// Valida NIF, CIF y NIE
// Retorna: 1 = NIF ok, 2 = CIF ok, 3 = NIE ok, -1 = NIF error, -2 = CIF error, -3 = NIE error, 0 = ??? error
function validaNifCifNie(a) {
	var temp=a.toUpperCase();
	var cadenadni="TRWAGMYFPDXBNJZSQVHLCKE";
	if(temp!=='') {
		if((!/^[A-Z]{1}[0-9]{7}[A-Z0-9]{1}$/.test(temp) && !/^[T]{1}[A-Z0-9]{8}$/.test(temp)) && !/^[0-9]{8}[A-Z]{1}$/.test(temp)) {
			return 0;
		}
		// NIF
		if(/^[0-9]{8}[A-Z]{1}$/.test(temp)) {
			posicion = a.substring(8,0) % 23;
			letra = cadenadni.charAt(posicion);
			var letradni=temp.charAt(8);
			if(letra == letradni) {
				return 1;
			} else {
				return -1;
			}
		}
 		// CIF
		suma = parseInt(a[2])+parseInt(a[4])+parseInt(a[6]);
		for(i = 1; i < 8; i += 2) {
			temp1 = 2 * parseInt(a[i]);
			temp1 += '';
			temp1 = temp1.substring(0,1);
			temp2 = 2 * parseInt(a[i]);
			temp2 += '';
			temp2 = temp2.substring(1,2);
			if(temp2 == '') {
				temp2 = '0';
			}
			suma += (parseInt(temp1) + parseInt(temp2));
		}
		suma += '';
		n = 10 - parseInt(suma.substring(suma.length-1, suma.length));
 		// Comprobación de NIFs especiales (se calculan como CIFs)
		if(/^[KLM]{1}/.test(temp)) {
			if(a[8] == String.fromCharCode(64 + n)) {
				return 1;
			} else {
				return -1;
			}
		}
		// CIF
		if(/^[ABCDEFGHJNPQRSUVW]{1}/.test(temp)) {
			temp = n + '';
			if(a[8] == String.fromCharCode(64 + n) || a[8] == parseInt(temp.substring(temp.length-1, temp.length))) {
				return 2;
			} else {
				return -2;
			}
		}
		// NIE
		if(/^[T]{1}/.test(temp)) {
			if(a[8] == /^[T]{1}[A-Z0-9]{8}$/.test(temp)) {
				return 3;
			} else {
				return -3;
			}
		}
		if(/^[XYZ]{1}/.test(temp)) {
			pos = str_replace(['X', 'Y', 'Z'], ['0','1','2'], temp).substring(0, 8) % 23;
			if(a[8] == cadenadni.substring(pos, pos + 1)) {
				return 3;
			} else {
				return -3;
			}
		}
	}
	return 0;
}

// Función auxiliar para reemplazar cadenas
function str_replace(search, replace, subject) {
	var f = search, r = replace, s = subject;
	var ra = r instanceof Array, sa = s instanceof Array, f = [].concat(f), r = [].concat(r), i = (s = [].concat(s)).length;
	while (j = 0, i--) {
		if(s[i]) {
			while (s[i] = s[i].split(f[j]).join(ra ? r[j] || "" : r[0]), ++j in f){};
		}
	}
	return sa ? s : s[0];
}


