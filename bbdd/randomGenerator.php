<?php

    // Esta función recibe un número (1..36) y devuelve el caracter correspondiente    
    function assign_rand_value($num) {
        $cadena = "abcdefghijklmnopqrstuvwxyz0123456789";
        return $cadena[$num];
    }

    // Función para obtener una cadena de longitud "$length" con caracteres aleatorios
    function get_rand_pass($length) {
        if($length > 0) {
            $rand_id = "";
            for($i = 1; $i <= $length; $i++) {
                mt_srand((double)microtime() * 1000000);
                // Sólo necesito aleatorios entre 0 y 35
                $num = mt_rand(0,35);
                $rand_id .= assign_rand_value($num);
            }
        }
        return $rand_id;
    }

?>
