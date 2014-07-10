<?php
    
//error_reporting(E_ALL);
//ini_set('display_errors', TRUE);
//ini_set('display_startup_errors', TRUE);


require 'include/include.php';


$jobroletable = "CREATE  TABLE `kunder`.`cphjobrolespoint` (
  `ID` INT NOT NULL AUTO_INCREMENT ,
  `jobrole` VARCHAR(45) NULL ,
  `point` VARCHAR(10) NULL ,
  PRIMARY KEY (`ID`) ,
  UNIQUE INDEX `ID_UNIQUE` (`ID` ASC) )
  CHARACTER SET UTF8  
;";


$itemlisttable = "CREATE  TABLE `kunder`.`cphitemlist` (
  `ID` INT NOT NULL AUTO_INCREMENT ,
  `jobrole` VARCHAR(45) NULL ,
  `item_no` VARCHAR(45) NULL ,
  `quantity` INT NULL ,
  PRIMARY KEY (`ID`) ,
  UNIQUE INDEX `ID_UNIQUE` (`ID` ASC) )
  CHARACTER SET UTF8  
;";



$cphpointfile = "CREATE  TABLE `kunder`.`cphpointfile` (
  `ID` INT NOT NULL AUTO_INCREMENT ,
  `debnr` VARCHAR(45) NULL ,
  `afd` VARCHAR(45) NULL ,
  `salnr` VARCHAR(45) NULL ,
  `point` VARCHAR(45) NULL ,
  `name` VARCHAR(45) NULL ,
  `text` VARCHAR(45) NULL ,
  `overwrite` VARCHAR(45) NULL , 
  PRIMARY KEY (`ID`) ,
  UNIQUE INDEX `ID_UNIQUE` (`ID` ASC) )
  CHARACTER SET UTF8  
;";


$cphpointfound = "CREATE  TABLE `kunder`.`cphpointfound` (
  `ID` INT NOT NULL AUTO_INCREMENT ,
  `jobrole` VARCHAR(45) NULL ,
  `point` VARCHAR(45) NULL ,
  PRIMARY KEY (`ID`) ,
  UNIQUE INDEX `ID_UNIQUE` (`ID` ASC) )
  CHARACTER SET UTF8  
;";




$sql  = "drop table if exists cphpointfound";
$result = mysql_query($sql, $conn) or die(mysql_error());

$sql  = "drop table if exists cphpointfile";
$result = mysql_query($sql, $conn) or die(mysql_error());

$sql  = "drop table if exists cphjobrolespoint";
$result = mysql_query($sql, $conn) or die(mysql_error());




//create tables
$result = mysql_query($jobroletable, $conn) or die(mysql_error());

$result = mysql_query($cphpointfile, $conn) or die(mysql_error());

$result = mysql_query($cphpointfound, $conn) or die(mysql_error());



$sql = "SELECT job_roles FROM eshopbacher_bi.user where company_name like '%lufthavn%' group by job_roles;";
    
$result = mysql_query($sql, $connWebshop) or die(mysql_error());

  while ($newarray = mysql_fetch_array($result)) {
    $job_role = $newarray['job_roles'];
                        
    $sqlinsert = "insert into kunder.cphjobrolespoint values('','".$job_role."','');";
    //echo $sqlinsert . "\n";

    $resultinsert = mysql_query($sqlinsert, $conn) or die(mysql_error());
    //echo $sqlinsert . "\n\r";                                                   
  }    

//ready to insert points in table
	$sql = "SELECT * from kunder.cphjobrolespoint;";	    
	$result = mysql_query($sql, $conn) or die(mysql_error());
	
        $numofrows = 0;

        echo "<form action='cphsavepoint.php?save=1' name='savepoint' method='post'>";				   
        echo "<table border='0'>";
	

        while ($newarray = mysql_fetch_array($result)) {
            $id = $newarray['ID'];
            $jobrole = $newarray['jobrole'];
            $point = $newarray['point'];
                
            echo "<tr>";
            echo "<td valign='top'>$jobrole</td>";
            echo "<td><input name=$jobrole style='width: 40px' type='text' value=''/>&nbsp;</td>";

            echo "</tr>";
        }
        
        
        echo "<tr><td><input name='Submit1' type='submit' value='Gem' />&nbsp;</td></tr>";
        echo "</table>";
        echo "</form>";



exit();



?>
