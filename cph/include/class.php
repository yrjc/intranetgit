<?php


class MyClass  
{  

public $fileName;
public $importcsv;
public $dt;
public $dtdelete;
public $send_data;


public function __construct()
{
    $this->fileName = 'Bacher_KL.csv'; 
    $this->importcsv = "LOAD DATA INFILE $this->fileName INSERT INTO TABLE kunder.kempcsv FIELDS TERMINATED BY ';' LINES TERMINATED BY '\n' (salnr,fornavn,efternavn,afd,stilling,jobrolle);";
    $this->dtdelete = "/var/www/kempcsv/kempdeleteuser" . date("Y-m-d_His") . ".csv";
    $this->dt = "/var/www/kempcsv/kempuser" . date("Y-m-d_His") . ".csv"; 
    $this->dtdate = date("Y-m-d"); 
    //Initialize all tables
    $this->tables();
    $this->send_data = "";   
}


public $tablelogTable = "CREATE  TABLE `kunder`.`logTable` (
`ID` INT NOT NULL AUTO_INCREMENT ,
`datetimestamp` DATETIME NOT NULL ,
`type` VARCHAR(20) NULL ,
`hvad` VARCHAR(255) NULL ,
PRIMARY KEY (`ID`) ,
UNIQUE INDEX `ID_UNIQUE` (`ID` ASC) )
CHARACTER SET UTF8  
;";


public $tablejobroleerrors = "CREATE  TABLE `kunder`.`jobroleerrors` (
`ID` INT NOT NULL AUTO_INCREMENT ,
`salnr` VARCHAR(45) NULL ,
`jobrolleKL` VARCHAR(45) NULL ,
`jobrolleWEBSHOP` VARCHAR(45) NULL ,
`debitornr` VARCHAR(45) NULL ,
PRIMARY KEY (`ID`) ,
UNIQUE INDEX `ID_UNIQUE` (`ID` ASC) )
CHARACTER SET UTF8  
;";



public $table = "CREATE  TABLE `kunder`.`kempcsv` (
`ID` INT NOT NULL AUTO_INCREMENT ,
`debitornr` VARCHAR(45) NULL ,
`salnr` VARCHAR(45) NULL ,
`fornavn` VARCHAR(45) NULL ,
`efternavn` VARCHAR(45) NULL ,
`stilling` VARCHAR(45) NULL ,
`jobrolle` VARCHAR(45) NULL ,
`afd` VARCHAR(45) NULL ,
PRIMARY KEY (`ID`) ,
UNIQUE INDEX `ID_UNIQUE` (`ID` ASC) )
CHARACTER SET UTF8;
";


public $tableWebshop = "CREATE  TABLE `kunder`.`kempwebshop` (
`ID` INT NOT NULL AUTO_INCREMENT ,
`salnr` VARCHAR(45) NULL ,
`navn` VARCHAR(45) NULL ,
`jobrolle` VARCHAR(45) NULL ,
`debitornr` VARCHAR(45) NULL ,
PRIMARY KEY (`ID`) ,
UNIQUE INDEX `ID_UNIQUE` (`ID` ASC) )
CHARACTER SET UTF8  
;";


public $tableWebshopInactive = "CREATE  TABLE `kunder`.`kempwebshopInactive` (
`ID` INT NOT NULL AUTO_INCREMENT ,
`salnr` VARCHAR(45) NULL ,
`navn` VARCHAR(45) NULL ,
`jobrolle` VARCHAR(45) NULL ,
`debitornr` VARCHAR(45) NULL ,
PRIMARY KEY (`ID`) ,
UNIQUE INDEX `ID_UNIQUE` (`ID` ASC) )
CHARACTER SET UTF8  
;";


public $kempCreateUser = "CREATE  TABLE `kunder`.`kempCreateUser` (
`ID` INT NOT NULL AUTO_INCREMENT ,
`type` VARCHAR(45) NULL ,
`debnr` VARCHAR(45) NULL ,
`salnr` VARCHAR(45) NULL ,
`passw` VARCHAR(45) NULL ,
`fornavn` VARCHAR(45) NULL ,
`efternavn` VARCHAR(45) NULL ,
`email` VARCHAR(45) NULL ,
`levadr` VARCHAR(45) NULL ,
`sex` VARCHAR(45) NULL ,
`status` VARCHAR(45) NULL ,
`startdate` VARCHAR(45) NULL ,
`jobrolle` VARCHAR(45) NULL ,
`point` VARCHAR(45) NULL ,
`gruppeadmin` VARCHAR(45) NULL ,
`fakref` VARCHAR(45) NULL ,
PRIMARY KEY (`ID`) ,
UNIQUE INDEX `ID_UNIQUE` (`ID` ASC) )
CHARACTER SET UTF8  
;";


public $tableDeleteUser = "CREATE  TABLE `kunder`.`kempDeleteUser` (
`ID` INT NOT NULL AUTO_INCREMENT ,
`debnr` VARCHAR(45) NULL ,
`salnr` VARCHAR(45) NULL ,
`fullname` VARCHAR(45) NULL ,

PRIMARY KEY (`ID`) ,
UNIQUE INDEX `ID_UNIQUE` (`ID` ASC) )
CHARACTER SET UTF8  
;";



public $jobroleerrors = "
SELECT kempcsv.debitornr, kempcsv.salnr, kempcsv.jobrolle AS jobrolleKL, kempwebshop.jobrolle AS jobrolleWEBSHOP FROM kunder.kempwebshop
inner join kunder.kempcsv
on
kempcsv.salnr = kempwebshop.salnr
where kempcsv.jobrolle != kempwebshop.jobrolle;
";


public $importNewUsers = "
SELECT kempcsv.debitornr, kempcsv.salnr AS salnr_, kempcsv.fornavn, kempcsv.efternavn, kempcsv.jobrolle FROM kunder.kempcsv
left join kunder.kempwebshop
on
kempwebshop.salnr = kempcsv.salnr
where kempwebshop.salnr is NULL;
";
 
    
public $importDeletedUsers = "
SELECT kempwebshop.debitornr, kempwebshop.salnr, kempwebshop.navn, kempcsv.salnr AS salnrX  FROM kunder.kempwebshop
left join kunder.kempcsv
on
kempwebshop.salnr = kempcsv.salnr
where kempcsv.salnr is NULL; 
";


public function send_mail()
{

  //$this->send_data = "<html><body>";

  $this->send_data .= "Forkerte jobroller :" . "\n"; 
  $conn = mysql_connect("192.168.0.190", "arduino", "Mo11yX3q");
  mysql_select_db("kunder",$conn);

  $result = mysql_query($this->jobroleerrors, $conn);

  while ($newarray = mysql_fetch_array($result)) {
    $salnr = $newarray['salnr'];
    $debitornr = $newarray['debitornr'];

    $this->send_data .= $salnr . " " . $debitornr . "\n";	
  }




  $this->send_data .= "Skal slettes :" . "\n";

  $result = mysql_query($this->importDeletedUsers, $conn);

  while ($newarray = mysql_fetch_array($result)) {
    $salnr = $newarray['salnr'];
    $debitornr = $newarray['debitornr'];
    $navn = $newarray['navn'];
   
    $this->send_data .= " " . strip_tags($salnr) . " " . strip_tags($debitornr) . " " . strip_tags($navn) . "\r\n";	

    
  }




  $this->send_data .= "\r\nSkal oprettes :\r\n";

  $result = mysql_query($this->importNewUsers, $conn);

  while ($newarray = mysql_fetch_array($result)) {
    $salnr = $newarray['salnr_'];
    $debitornr = $newarray['debitornr'];
    $fornavn = $newarray['fornavn'];
    $efternavn = $newarray['efternavn'];
   
    $this->send_data .= $salnr . " " . $debitornr . " " . $fornavn . " " . $efternavn . "\r\n";	
  }






$modtager = "jc@jc-net.dk"; //Hvem skal have mailen?
$emne = "kemp"; //Emnefeltet
$besked = "Besked fra : \n
                    Navn:
                    Besked: ";
$header = "from:jc@bacher.dk" . "\r\n";
//$header .= "MIME-Version: 1.0\r\n";
//$header .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

mail($modtager, $emne, $this->send_data, $header); //Send!!
}



    
private function tables()  
{  
  $conn = mysql_connect("192.168.0.190", "arduino", "Mo11yX3q");
  mysql_select_db("kunder",$conn);
  //$result = mysql_query($obj->tableWebshop, $conn) or die(mysql_error());
  $result = mysql_query($this->tableWebshop, $conn);
  $result = mysql_query($this->tablelogTable, $conn);
}  
  

}  




?>
