<?php
/*
 *
 * Obtained from: http://css-tricks.com/snippets/php/sanitize-database-inputs/
 *
 */


/*
 * Function for stripping out malicious bits
 */
function cleanInput($input) {

  $search = array(
    '@<script[^>]*?>.*?</script>@si',   // Strip out javascript
    '@<[\/\!]*?[^<>]*?>@si',            // Strip out HTML tags
    '@<style[^>]*?>.*?</style>@siU',    // Strip style tags properly
    '@<![\s\S]*?--[ \t\n\r]*>@'         // Strip multi-line comments
  );
    $output = preg_replace($search, '', $input);
    return $output;
}

/*
* Sanitization function.
* Uses the function above, as well as adds slashes as to not screw up database functions.
*/
function sanitize($input) {
    if (is_array($input)) {
        foreach($input as $var=>$val) {
            $output[$var] = sanitize($val);
        }
    }
    else {
        if (get_magic_quotes_gpc()) {
            $input = stripslashes($input);
        }
        $input  = cleanInput($input);
        $output = mysql_real_escape_string($input);
    }
    return $output;
}

    /*
    // Usage:

      $bad_string = "Hi! <script src='http://www.evilsite.com/bad_script.js'></script> It's a good day!";
      $good_string = sanitize($bad_string);
      // $good_string returns "Hi! It\'s a good day!"

      // Also use for getting POST/GET variables
      $_POST = sanitize($_POST);
      $_GET  = sanitize($_GET);

    */
?>
