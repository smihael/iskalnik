<?php
       
class Table {

	protected $rows;

	function __construct()
	{
		$this->rows = array();
	}
	
	public function addRow($name, $label, $type, $icon = null)
	{
		$this->rows[$name] = array("name" => $name, "label" => $label, "type" => $type, "icon" => $icon);
	}
	
	public function getRows() {
		return $this->rows;
	}
	
}

class DetailsTable extends Table {
	
	public $tablename;
	//private $matches_counter = 0;
	
	//TODO: tablename can be extracted from config
	function __construct($config, $podatki, $id, $summary = null) {
		parent::__construct();

		$this->tablename = $config['db_table'];
		$this->config = $config;
		$this->id = $id;
		
		
                // get_indices_of_summary_fields
                $indices = array();
                foreach($summary as $key)
                    $indices[] = array_search($key, array_keys($podatki));
		$this->summary = $indices;
		
		//add rows
		
		foreach($podatki as $tip => $podatek)
                    count($podatek) > 2 ? $this->addRow($tip, $podatek[0],$podatek[1],$podatek[2]) :  $this->addRow($tip,$podatek[0],$podatek[1]);
                    
                    
		//$this->addRow('signatura', 'Signatura', 'string');
		/*$this->addRow('avtor', 'Avtor', 'clickable', 'glyphicon-user');
		$this->addRow('naslov', 'Naslov', 'string', 'glyphicon-book');
		$this->addRow('kraj', 'Kraj', 'string');
		$this->addRow('leto', 'Letnica izdaje', 'clickable', 'glyphicon-calendar');
		$this->addRow('strani', 'Strani', 'string');
		$this->addRow('vrsta', 'Vrsta','string');
		$this->addRow('podrocje', 'Področje', 'string');
		$this->addRow('mentor', 'Mentor', 'clickable');
		$this->addRow('osebe', 'Obravnavani avtorji', 'list');
		$this->addRow('dela', 'Obravnavana dela', 'list');
		$this->addRow('kljucne', 'Kljucne besede', 'list');
		$this->addRow('priloge', 'Priloge', 'string');		
		$this->addRow('cobiss', 'COBISS', 'cobiss');
		$this->addRow('zbirka', 'Zbirka iz katere je vnos prenesen', 'string');
		$this->addRow('celotno_besedilo', 'Celotno besedilo', 'string');*/

	}
	
	function __destruct() {
		$this->tablename = null;
		$this->config = null;
		$this->id = null;
	}
	
	private function fetchData($config, $id){
		//establish mysql connection
		$mysqli = mysqli_init();
		$mysqli->options(MYSQLI_OPT_CONNECT_TIMEOUT, 5);
		$mysqli->real_connect($config['db_host'],$config['db_user'],$config['db_password'],$config['db_name']);
		$mysqli->query("SET NAMES 'utf8' COLLATE 'utf8_slovenian_ci'");
		
		//prevent sql injection attacs
		$id =  $mysqli->real_escape_string(strip_tags($id));
		
		//get relavant entry
		$sql="SELECT * FROM  `$this->tablename` WHERE  `id` = '$id';";
		$rs=$mysqli->query($sql);
		
		if($rs === false) {
			trigger_error('Wrong SQL: ' . $sql . ' Error: ' . $conn->error, E_USER_ERROR);
		} else {
			$rows_returned = $rs->num_rows;
			return $rs;
		}
	}
	
	
	private function hotWord($field,$value,$type) {
	
            //stran kamor gremo
            $link = "results.php";
               
            //unset $params; // ni treba znotraj funkcije
            //$params = $_GET; //kadar zelimo pripeti k obstojecim iskalnim parametrom
            $params[$field] = array($value,$type); //eventuelno potrebno: urlencode(utf8_encode($value));
            
            //the trick https://stackoverflow.com/questions/14071587/php-pass-array-through-post
            $paramString = http_build_query($params);
            
            //vrni gumbek
            return "<a class=\"label label-default\" href=\"$link?$paramString\" target=\"_parent\">$value</a> ";
        }
	
	public function renderHTML(){
		$rs=$this->fetchData($this->config,$this->id);
		$rs->data_seek(0);
		while($row = $rs->fetch_row()){
			$rows=$this->getRows();
			$keys = array_keys($rows);
			$html = "<table>";
			for ($i = 0; $i< count($rows); $i++) {
			
                                //name
                                $name = $rows[$keys[$i]]["name"];
                                
				//label
				$label = $rows[$keys[$i]]["label"];
				
				//type
				$type = $rows[$keys[$i]]["type"];
		
				if ( $row[$i] != null) {
					$html .= "<tr><th>$label</th><td>";
					switch ($type) {
						case "url":
							// direct url
							$value = $row[$i];
							$html .= "<a href=\"$value\">Povezava</a>";
							break;
						case "cobiss":
							$value = $row[$i];
							$html .= "<a href=\"http://www.cobiss.si/scripts/cobiss?command=DISPLAY&base=cobib&rid=$value\">COBISS</a>";
							break;
						case "clickable":
							//link to show related items
							$html .= $this->hotWord($name,$row[$i],"like");
							break;
						case "list":
							// a list of keywords
							$tags = str_getcsv($row[$i], ',', '\"'); // split at commas but not in double quotes
							
							$values = array();
							foreach ($tags as &$tag)
								$values = array_merge($values,explode(" | ", //additional delimiter
										      trim($tag,'".'))); //cut " and . 

							foreach ($values as &$value)
								if($value != "") // show only non empty tokens
                                                                    $html .= $this->hotWord($name,$value,"like");
							break;
						default:
							// plain string
							$html .= $row[$i];
							break;
					}
					$html .= "</td></tr>";
				}
			}
			$html .= "</table>";
		}
		return $html;
	}

	public function renderNiceHTML(){
		$rs=$this->fetchData($this->config,$this->id);
		$rs->data_seek(0);
		
		$id = $this->id;
		
		while($row = $rs->fetch_row()){
			$rows=$this->getRows();
			$keys = array_keys($rows);
			                            
			//var_dump($this->summary[0]);
			//var_dump($row[$this->summary[0]]);
			
                        $html ="<div class='panel panel-info'>";
                        $html.="        <div class='panel-heading'>";
                        $html.="                <span class='pull-right clickable' data-toggle='collapse' data-target='#zadetek-$id'><i class='glyphicon glyphicon-chevron-down'></i></span>";
                        $html.="                <h3 class='panel-title panel-clickable' data-toggle='collapse' data-target='#zadetek-$id'>";
                        $av = $row[$this->summary[0]];
                        $ti = $row[$this->summary[1]];
                        $ye = $row[$this->summary[2]];
                        $html.="                        <b>$ti</b><br /><small>$av ($ye)</small>";
                        $html.="                </h3>";
                        $html.="        </div>";
                        
                        $html.="<div class='panel-collapse collapse' id='zadetek-$id'>";
                        $html.="        <div class='panel-body' >";
                        $html.="                <table class='table table-hover'>";
                        $html.="                        <thead>";
                        $html.="                                <tr>";
                        $html.="                                        <th>Polje</th>";
                        $html.="                                        <th>Podrobnosti</th>";
                        $html.="                                </tr>";
                        $html.="                        </thead>";
                        $html.="                        <tbody>";
                        // dodatne vrstice
                        for ($i = 0; $i< count($rows); $i++) {
                                //label
                                $label = $rows[$keys[$i]]["label"];
                                
                                //type
                                $type = $rows[$keys[$i]]["type"];

                                //name
                                $name = $rows[$keys[$i]]["name"];
                                
                                if ( $row[$i] != null) {
                                        $html .= "<tr><td>$label</td><td>";
                                        switch ($type) {
                                                case "url":
                                                        // direct url
                                                        $value = $row[$i];
                                                        $html .= "<a href=\"$value\">Povezava</a>";
                                                        break;
                                                case "clickable":
                                                        //link to show related items
                                                        $value = $row[$i];
                                                        $html .= $this->hotWord($name,$value,"like");
                                                        break;
                                                case "list":
                                                        // a list of keywords
                                                        $tags = str_getcsv($row[$i], ',', '\"'); // split at commas but not in double quotes
                                                        
                                                        $values = array();
                                                        foreach ($tags as &$tag)
                                                                $values = array_merge($values,explode(";", //additional delimiter
                                                                                        trim($tag,'".'))); //cut " and . 
                                                        foreach ($values as &$value) {
                                                                // show only non empty tokens
                                                                if($value != "")
                                                                        $html .= $this->hotWord($name,$value,"like");
                                                        }
                                                        break;
                                                default:
                                                        // plain string
                                                        $html .= $row[$i];
                                                        break;
                                        }
                                        $html .= "</td></tr>";
                                }
                        }
                        $html.="                        </tbody>";
                        $html.="                </table>";
                        $html.="        </div>"; //panel body
                        $html.="        <div class='panel-footer'>";
                        $html.="        </div>";
                        $html.="        </div>"; //panel colapse
                        $html.="</div>";
		}
		return $html;
	}

}

class Rezultat {

	function __construct()
	{
		$this->rows = array();
	}

}


?>
