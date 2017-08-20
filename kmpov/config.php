<?php

// Podatki o bazi
$info = array(
	"title" => "Kmečka povest do 1945",
	"description" => "Iskalnik po podatkovni zbirki",
	"intro" => "Zbirka s podatki o 235 delih 88 slovenskih avtorjev",
	"about" => "",
	"searchhint" => "Filtriraj po naslovu, avtorju, letnici , ...",
	"last_update" => "",
	"n_entries" => "",
	"base_url" => "http://www.slov.si/iskalniki/kmpov/"
);


// Povezava do baze
$config = array(
            "db_name" => "mihael_jezik",
            "db_user" => "mihael",
            "db_password" => "REDACTED",
            "db_host" => "localhost",
            "db_table" => "kmpov"
        );                

// Vsa polja
$fields = array(
            // kljuc v podatkovni zbirki =>  (ime polja, tip polja, ikona polja)
            'id' => array('zaporedna številka','string'),        //stari zs                                
            'av' => array('avtor ','clickable'),                                                   
            'ps' => array('psevdonim ','string'),                                               
            'ti' => array('naslov dela','string'),                                              
            'pn' => array('podnaslov','string'),                                                
            'ca' => array('časopis, zbirka ','clickable'),                                         
            'sb' => array('dolžina v besedah','string'),                                        
            'kr' => array('kraj izdaje','string'),                                              
            'za' => array('založba','string'),                                                  
            'ye' => array('letnica izdaje','clickable'),                                           
            're' => array('ponatisi, prevodi','string'),                                        
            'oz' => array('oznaka','string'),                                                   
            'mo' => array('motivi','string'),                                                   
            'm2' => array('stranski motivi','string'),                                          
            'c1' => array('čas dogajanja','clickable'),                                 
            'so' => array('število oseb','string'),                                             
            'ko' => array('konstelacija oseb','string'),                                        
            'mt' => array('motivacija','string'),                                               
            'pp' => array('pripovedovalec','string'),                                           
            'kc' => array('konec','string'),                                                    
            'pm' => array('pripovedni način','string'),                                         
            'sp' => array('prostor','clickable'),                                                  
            'zg' => array('zgodba','string'),                           
            'op' => array('opombe ob delu','string'),                   
            'ide' => array('ideja','string'),                            
            'km' => array('kompozicija','string'),                      
            'si' => array('signatura v knjižnici','string'),            
            'oc' => array('ocene, kritike','string')                    
            //'zt' => array('žanrski tip, npr. idila','string'),
        );
        
// Polja, ki so prikazana v tabeli
$summary = array('av', 'ti', 'ye');

// Privzeti ključ za razvrščanje
$order_key = "id";


// Tehnikalije
// error_reporting(E_ALL);
// ini_set('display_errors', '1');

?>
