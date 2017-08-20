<?php

function scopedInclude($file, $params = array())
{
    extract($params);
    include $file;
}

function selfURL() {
	$s = empty($_SERVER["HTTPS"]) ? ''
		: ($_SERVER["HTTPS"] == "on") ? "s"
		: "";
	$protocol = strleft(strtolower($_SERVER["SERVER_PROTOCOL"]), "/").$s;
	$port = ($_SERVER["SERVER_PORT"] == "80") ? ""
		: (":".$_SERVER["SERVER_PORT"]);
	return $protocol."://".$_SERVER['SERVER_NAME'].$port.$_SERVER['REQUEST_URI'];
}
function strleft($s1, $s2) {
	return substr($s1, 0, strpos($s1, $s2));
}

function printMessage($type,$text) {
	echo "<div class=\"alert $type\">\n";
	echo "\t $text\n";
	echo "</div>\n";
}

// za podrobno iskanje (full site)
function searchCriterion($row) {
        // https://stackoverflow.com/questions/14071587/php-pass-array-through-post
	if ($row['icon'] != null) {
	echo "	<div class=\"input-group\">\n";
	echo "		<span class=\"input-group-addon\"><span class=\"glyphicon ".$row['icon']."\"></span></span>\n";
	} else {
	echo "	<div class=\"input-group additional\" style=\"display: none\">\n";
	}
	echo "		<input type=\"text\" class=\"form-control\" placeholder=\"".$row['label']."\" name=\"".$row['name']."[]\" id=\"".$row['name']."\">\n";
	echo "		<span class=\"input-group-addon\">\n";
	echo "			<select name=\"".$row['name']."[]\">\n";
	echo "				<option value=\"like\">vsebuje</option>\n";
	echo "				<option value=\"not like\">ne vsebuje</option>\n";
	echo "				<option value=\"=\">se ujema popolnoma</option>\n";
	echo "				<option value=\"<>\">se ne ujema popolnoma</option>\n";
	echo "				<option value=\">\">je večje kot</option>\n";
	echo "				<option value=\"<\">je manjše kot</option>\n";
	echo "				<option value=\">=\">je večje ali enako kot</option>\n";
	echo "				<option value=\"<=\">je manjše ali enako kot</option>\n";
	echo "			</select>\n";
	echo "		</span>\n";
	echo "	</div>\n";
}

// v pojavnem okencu
function searchCriterion2($row) {
        // https://stackoverflow.com/questions/14071587/php-pass-array-through-post
	echo "	<div class=\"input-group\">\n";
	echo "		<span class=\"input-group-addon\"><span class=\"glyphicon ".$row['icon']."\"></span></span>\n";
	echo "		<input type=\"text\" class=\"form-control\" placeholder=\"".$row['label']."\" name=\"".$row['name']."[]\" id=\"".$row['name']."\">\n";
	echo "		<span class=\"input-group-addon\">\n";
	echo "			<select name=\"".$row['name']."[]\">\n";
	echo "				<option value=\"like\">vsebuje</option>\n";
	echo "				<option value=\"not like\">ne vsebuje</option>\n";
	echo "				<option value=\"=\">se ujema popolnoma</option>\n";
	echo "				<option value=\"<>\">se ne ujema popolnoma</option>\n";
	echo "				<option value=\">\">je večje kot</option>\n";
	echo "				<option value=\"<\">je manjše kot</option>\n";
	echo "				<option value=\">=\">je večje ali enako kot</option>\n";
	echo "				<option value=\"<=\">je manjše ali enako kot</option>\n";
	echo "			</select>\n";
	echo "		</span>\n";
	echo "	</div>\n";
}

function humanReadableOperator($operator) {
    switch($operator) {
        case "like": return " vsebuje niz "; break;
        case "not like": return " ne vsebuje niza "; break;
        case "=": return " se ujema popolnoma z nizom "; break;
        case "<>": return "se popolnoma ne ujema z nizom "; break;
        case ">": return " je večje kot "; break;
	case "<": return " je manjše kot "; break;
	case ">=": return " je večje ali enako kot "; break;
	case "<=": return " je manjše ali enako kot "; break;        
        }
}

// function indices_of_summary_fields($fields, $summary) {
//     foreach($summary as $key)
//         echo array_search($key, array_keys($fields));
// }


?>
