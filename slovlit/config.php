<?php

// Podatki o bazi
$info = array(
	"title" => "Slovensko leposlovje na spletu",
	"description" => "Iskalnik po podatkovni zbirki",
	"intro" => "",
	"about" => "<p>Podatkovna zbirka slovenskih leposlovnih besedil na spletu zajema z <a href='http://sl.wikisource.org/wiki/Glavna_stran'>Wikivira</a>, iz <a href='http://bos.zrc-sazu.si/nova_beseda.html'>Nove besede</a> Primoža Jakopina, iz dLiba (<a href='http://www.dlib.si'>Digitalne knjižnice Slovenije</a>), iz zbirke <a href='http://www.omnibus.se/beseda/'>Beseda</a> Franka Luina, <a href='http://lit.ijs.si/leposl.html'>Zbirke slovenskih leposlovnih besedil</a> Mirana Hladnika, iz zbirke <a href='http://nl.ijs.si/e-zrc/'>eZISS</a> na ZRC SAZU, <a href='http://nl.ijs.si/ahlib/dl/'>AHLib</a> in <a href='http://nl.ijs.si/imp/dl/index-date.html'>Digitalne knjižnice IMP</a> pri IJS in iz drugih manj obsežnih lokacij (osebnih strani slovenskih pesnikov in pisateljev in digitalizacijskih projektov). Vpisovanje del v bazo od leta 2008 podpira resorno ministrstvo, izvajajo pa ga sodelavci pri projektu digitalizacije slovenske leposlovne klasike na Filozofski fakulteti Univerze v Ljubljani in pri Slavističnem društvu Slovenije.</p>  <p>Klik na naslov odpre polno besedilo na njegovi izvirni lokaciji, klik na avtorja pa njegovo osebno stran oz. bibliografijo. Kjer povezave na besedilo še ni, pomeni, da tekst, ki ga imamo sicer v elektronski obliki, (še) ni dostopno &ndash; na seznamu je zato, da ne bi po nepotrebnem prišlo do ponovnega pretipkavanja ali skeniranja.</p>  <p>Vabljeni k postavljanju del slovenske književnosti na splet oziroma k popravljanju napak v strojno prebranih besedilih pri projektu <a href='http://sl.wikisource.org/wiki/Wikivir:Slovenska_leposlovna_klasika'>Slovenska leposlovna klasika</a> (gl. tudi <a href='http://sl.wikisource.org/wiki/Pogovor_o_Wikiviru:Slovenska_leposlovna_klasika'>pogovorno stran projekta</a>). Vse, ki postavljate na splet literarna besedila, svoja ali drugih slovenskih avtorjev, prosimo, da sporočite njihovo spletno lokacijo in tako omogočite njihov vpis v podatkovno zbirko.<br /></p>",
	"searchhint" => "Išči po naslovu, avtorju, letnici , ...",
	"last_update" => "",
	"n_entries" => "",
	"base_url" => "http://www.slov.si/iskalniki/slovlit/"
);


// Povezava do baze
$config = array(
            "db_name" => "mihael_jezik",
            "db_user" => "mihael",
            "db_password" => "REDACTED",
            "db_host" => "localhost",
            "db_table" => "literatura"
        );                

// Vsa polja
$fields = array(
            // kljuc v podatkovni zbirki =>  (ime polja, tip polja, ikona polja)
            'id' => array('ID','string'),                                       
            'tip' => array('Kategorija ','string'),                                                   
            'naslov' => array('Naslov dela','string'),    
            'vir' => array('Spletni vir','string'),
            'ime' => array('Ime avtorja','string'),                                                    
            'priimek' => array('Priimek avtorja','string'),                                         
            'psevdonim' => array('Psevdonim','string'),                                                  
            'LinkAvtor' => array('Povezava  do strani o avtorju','string'),                           
            'LinkDelo' => array('Povezava do teksa','string')
        );
        
// Polja, ki so prikazana v tabeli
$summary = array('tip', 'naslov', 'priimek');

// Privzeti ključ za razvrščanje
$order_key = "id";


// Tehnikalije
// error_reporting(E_ALL);
// ini_set('display_errors', '1');

?>
