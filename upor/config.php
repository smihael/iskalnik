<?php

// Podatki o bazi
$info = array(
	"description" => "Katalog uporniškega pesništva",
	"title" => "Pesmi slovenskega narodnoosvobodilnega boja",
	"intro" => "Iskalnik po zbirki digitaliziranih pesmi slovenskega narodnoosvobodilnega boja (v nastajanju)",
	"about" => "Projekt digitalizacije pesmi slovenskega narodnoosvobodilnega boja združuje raziskovalce z ZRC SAZU (Inštituta za slovensko literaturo in literarne vede) in Filozofske fakultete UL (Oddelka za slovenistiko). 12.000 pesemskih enot 2437 avtorjev hrani Oddelek za slovenistiko Filozofske fakultete Univerze v Ljubljani. Izbor 2300 pesmi je med letoma 1987 in 1997 izšel v štirih knjigah z naslovom Slovensko pesništvo upora; uredil ga je Boris Paternu, ki je zbiranje v 70. letih zasnoval in vodil, pomagali sta mu Marija Stanonik in Irena Novak Popov, angažiral pa je več kot sto študentov in sodelavcev. Večji del zbranih pesmi je ostal zgolj v papirnatem arhivu, ki ga je čas občutno načel. Projekt, ki pesmi, nastale v najtežjih pogojih, rešuje gotovega propada, zajema izdelavo kataloga, fotografiranje oz. skeniranje pesmi, optično prepoznavanje besedila in urejanje, naknadno pa tudi spremni znanstveni aparat. Gre za eno najobsežnejših zbirk slovenske poezije; z nekaterimi vrhunskimi avtorji, kot so Edvard Kocbek, Matej Bor in Jože Udovič, ki kliče po novih literarnovednih raziskavah, s širino in razplastenostjo odporniške literarne dejavnosti med prebivalstvom je zanimiva tudi za sociolingviste, kot integralen del evropskega antifašističnega gibanja pa imajo tudi trajno kulturnozgodovinsko veljavo.",
	"searchhint" => "Filtriraj po naslovu, datumu, ...",
	"last_update" => "19. september 2021",
	"n_entries" => "",
	"base_url" => "http://www.slov.si/iskalniki/upor/",
	"credits" => "Katalog pripravljajo <a href=\"http://lit.ijs.si/hladnik.html\">Miran Hladnik</a>, <a href=\"mailto:neza <pika> kocnik <pri> gmail <pika> com \" >Neža Kočnik</a> ter <a href=\"https://isllv.zrc-sazu.si/sl/sodelavci/andraz-jez-sl\">Andraž Jež</a>, spletno postavitev pa <a href=\"http://www.simonic.si\">Mihael Simonič</a>."
);

// Povezava do baze
$config = array(
            "db_name" => "mihael_jezik",
            "db_user" => "mihael",
            "db_password" => "REDACTED",
            "db_host" => "localhost",
            "db_table" => "upor"
        );                

// Vsa polja
$fields = array(
            // kljuc v podatkovni zbirki =>  (ime polja, tip polja, ikona polja)
            'id' => array('Zaporedna številka', 'integer'),   //stari zs                                
            'av' => array('Avtor ','clickable', 'glyphicon-user'),                                                
            'ps' => array('Psevdonim ','string'),  
            'verz' => array('Prvi verz','string'),                                                
            'ti' => array('Naslov pesmi','string', 'glyphicon-book'),                                         
            'vir' => array('Vir ','string'),                                         
            'si' => array('Signatura v knjižnici', 'string'),
            'datum' => array('Datum nastanka','clickable', 'glyphicon-calendar'),                                      
            'kraj' => array('Kraj nastanka','string'),                                              
            'variante' => array('Variante','string'),                                                   
            'objava' => array('Objava','string'),                                                   
            'jezik' => array('Jezik','string'),                                          
            'uglasbitev' => array('Uglasbitev','string'),                                 
            'redakcije' => array('Redakcije','string'),                                             
            'komentar_redaktorja' => array('Komentar redaktorja','string'),                                        
            'vpis' => array('Datum vpisa','string'),                                           
            'vpisovalec' => array('Vpisovalec','clickable'),                                         
            'komentar_vpisovalca' => array('Komentar vpisovalca','string'),
            'povezani_vnosi' => array('Povezani vnosi','clickable')
        );
        
// Polja, ki so prikazana v tabeli
$summary = array('av', 'ti', 'verz');

// Privzeti ključ za razvrščanje
$order_key = "id";


// Tehnikalije
// error_reporting(E_ALL);
// ini_set('display_errors', '1');

?>
