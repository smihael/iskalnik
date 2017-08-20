<?php
// https://stackoverflow.com/questions/45783958/use-php-file-from-other-directory-and-include-file-from-current-directory?noredirect=1#comment78525620_45783958
// https://stackoverflow.com/questions/10265216/get-calling-file-name-from-include

$baza = $_GET['baza'];

if(baza == null) {
    preg_match("/.*slov\.si\/iskalniki\/([a-z]*)\/.*/", $_SERVER['HTTP_REFERER'], $baza);
    //var_dump($baza[1]);
}  

switch ($baza) {
    case "diplomske":
    case "diplome":
        require_once("../diplomske/config.php");
        break;
    case "zgrom":
        require_once("../zgrom/config.php");
        break;
    case "upor":
        require_once("../upor/config.php");
        break;
    case "slovlit":
    case "literatura":
        require_once("../slovlit/config.php");
        break;    
    case "kmpov":
        require_once("../kmpov/config.php");
        break;    
    default:
        echo "Ne morem ugotoviti baze";
        break;
}

?>
