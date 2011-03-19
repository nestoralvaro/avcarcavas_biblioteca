<?php
include("bbdd/conexion.php");

session_start();
function validar_admin() {
	$resultado = 99;
	$storedUser = "USER_NAME"; // Use the one you want for admin. HARDCODED!!. For non-harcoded version check "validar_user()"
	$storedPass = "USER_PASS"; // Use the one you want for admin. HARDCODED!!. For non-harcoded version check "validar_user()"
	if (trim($_POST["user_login"]) != "" && trim($_POST["password_login"]) != "") {
		$nick = trim($_POST["user_login"]);
		$pass = trim($_POST["password_login"]);
		if ($nick == $storedUser && $pass == $storedPass) {
			$_SESSION["nick"] = $nick;
			$_SESSION["pass"] = $pass;
			$resultado = 1;
		} else {
			return 2;
		}
	} else if(($_SESSION["nick"] == $storedUser) && ($_SESSION["pass"] == $storedPass)) {
		$resultado = 0;
	} else { 
		$resultado = 3;
	}
//    return 1;
	return $resultado;
}


function validar_user() {
	$resultado = 99;

	if (trim($_POST["user_login"]) != "" && trim($_POST["password_login"]) != "") {
		$nick = sanitize(trim($_POST["user_login"]));
		$pass = sanitize(trim($_POST["password_login"]));

        $users_counter = "select * from `usuarios` where `mail` = '" . $nick . "' and `password` = '" . $pass . "'";

        $result = mysql_query($users_counter);

        $rowSize = mysql_num_rows($result); 

        if($rowSize > 0) {
			$_SESSION["nick"] = $nick;
			$_SESSION["pass"] = $pass;
			$resultado = 1;
		} else {
			return 2;
		}
	} else if(($_SESSION["nick"] != "") && ($_SESSION["pass"] != "")) {
		$nick = sanitize(trim($_SESSION["nick"]));
		$pass = sanitize(trim($_SESSION["pass"]));
        $users_counter = "select * from `usuarios` where `mail` = '" . $nick . "' and `password` = '" . $pass . "'";
        $result = mysql_query($users_counter);
        $rowSize = mysql_num_rows($result); 

        if($rowSize > 0) {
			$_SESSION["nick"] = $nick;
			$_SESSION["pass"] = $pass;
			$resultado = 0;
		} else {
			return 2;
		}
	} else { 
		$resultado = 3;
	}
//    return 1;
	return $resultado;
}

?>

