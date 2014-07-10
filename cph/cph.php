<?php
    
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
date_default_timezone_set('Europe/London');

define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

/** Include PHPExcel */
require_once dirname(__FILE__) . '/Classes/PHPExcel.php';
require_once dirname(__FILE__) . '/Classes/PHPMailer/class.phpmailer.php';


//ini_set('display_errors',1);
//ini_set('display_startup_errors',1);
//error_reporting(-1);

require 'include/include.php';
//require 'include/class.php';

//$dt = "/var/www/kemp/kempuser" . date("Y-m-d_His") . ".csv";
//$dtdelete = "/var/www/kemp/kempdeleteuser" . date("Y-m-d_His") . ".csv";


$jobroletable = "CREATE  TABLE `kunder`.`cphjobroles` (
  `ID` INT NOT NULL AUTO_INCREMENT ,
  `jobrole` VARCHAR(45) NULL ,
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





// Create new PHPExcel object
echo date('H:i:s') , " Create new PHPExcel object" , EOL;
$objPHPExcel = new PHPExcel();

// Set document properties
echo date('H:i:s') , " Set document properties" , EOL;
$objPHPExcel->getProperties()->setCreator("Johnny Christensen")
->setLastModifiedBy("Maarten Balliauw")
->setTitle("PHPExcel Test Document")
->setSubject("PHPExcel Test Document")
->setDescription("Test document for PHPExcel, generated using PHP classes.")
->setKeywords("office PHPExcel php")
->setCategory("Test result file");





function save_to_excel($object, $sheetnumber, $item) {

  include 'include/include.php';

  $newsheet = $object->createSheet($sheetnumber);

  $item_ = str_replace("/","-",$item);
  //$item = str_replace("-"," ",$item);
     
  $newsheet->setTitle(utf8_encode($item_));
     

  //echo "item:" . $item . "\n\r";

  $sqlitemlist = "select SUM(quantity) as quantity, item_no, job_role FROM eshopbacher_bi.order__line__role where name like '%lufthavn%' AND job_role = '".$item."' GROUP BY item_no;";

  $result = mysql_query($sqlitemlist, $connWebshop) or die(mysql_error());
  $row = 1;
  $newsheet->setCellValue('A'.$row, utf8_encode($item));
  $row += 1;                            

  //echo $item . "\n\r";

  while ($newarray = mysql_fetch_array($result)) {
    $job_role = $newarray['job_role'];
    $item_no = $newarray['item_no'];
    $quantity = $newarray['quantity'];

    //$object->getActiveSheet()->setCellValue('A'.$row, $item_no);
    //$object->getActiveSheet()->setCellValue('B'.$row, $quantity);

    $newsheet->setCellValue('A'.$row, utf8_encode($item_no));
    $newsheet->setCellValue('B'.$row, $quantity);

    $row += 1;                            

    //echo $job_role . "\n\r";
                        
  }    

}




function save_to_excel_w1($object) {

  include 'include/include.php';

  $object->SetActiveSheetIndex(0);

  $object->getActiveSheet()->setTitle(utf8_encode("Alle"));

  $sqlitemlist = "select SUM(quantity) as quantity, item_no, job_role FROM eshopbacher_bi.order__line__role where name like '%lufthavn%' GROUP BY item_no;";

  $result = mysql_query($sqlitemlist, $connWebshop) or die(mysql_error());
  $row = 1;
  //$newsheet->setCellValue('A'.$row, utf8_encode($item));

  //echo $item . "\n\r";

  while ($newarray = mysql_fetch_array($result)) {
    $job_role = $newarray['job_role'];
    $item_no = $newarray['item_no'];
    $quantity = $newarray['quantity'];

    //$object->getActiveSheet()->setCellValue('A'.$row, $item_no);
    //$object->getActiveSheet()->setCellValue('B'.$row, $quantity);

    $object->getActiveSheet()->setCellValue('A'.$row, utf8_encode($item_no));
    $object->getActiveSheet()->setCellValue('B'.$row, $quantity);

    $row += 1;                            

    //echo $job_role . "\n\r";
                        
  }    

}



function save_item($item) {
  include 'include/include.php';

  //echo $item . "\n\r";

  $sqlitemlist = "select SUM(quantity) as quantity, item_no, job_role FROM eshopbacher_bi.order__line__role where name like '%lufthavn%' AND job_role = '".$item."' GROUP BY item_no;";

  $result = mysql_query($sqlitemlist, $connWebshop) or die(mysql_error());

  while ($newarray = mysql_fetch_array($result)) {
    $job_role = $newarray['job_role'];
    $item_no = $newarray['item_no'];
    $quantity = $newarray['quantity'];
                        
    $sqlinsert = "insert into kunder.cphitemlist values('','".$job_role."','".$item_no."','".$quantity."');";
    $resultinsert = mysql_query($sqlinsert, $conn) or die(mysql_error());
    //echo $sqlinsert . "\n\r";
                      
  }    

}








$sql  = "drop table if exists cphjobroles";
$result = mysql_query($sql, $conn) or die(mysql_error());

$result = mysql_query($jobroletable, $conn) or die(mysql_error());



$sql = "SELECT job_role FROM eshopbacher_bi.order__line__role where name like '%lufthavn%' group by job_role;";
    
$result = mysql_query($sql, $connWebshop) or die(mysql_error());

  while ($newarray = mysql_fetch_array($result)) {
    $job_role = $newarray['job_role'];
                        
    $sqlinsert = "insert into kunder.cphjobroles values('','".$job_role."');";
    $resultinsert = mysql_query($sqlinsert, $conn) or die(mysql_error());
    echo $sqlinsert . "\n\r";
                            
                        
  }    








$sql  = "drop table if exists cphitemlist";
$result = mysql_query($sql, $conn) or die(mysql_error());

$result = mysql_query($itemlisttable, $conn) or die(mysql_error());


//need to remove illegal characters like / in worksheet name. Replace with space
$sheetnumber = 0;
$sql = "SELECT jobrole FROM cphjobroles;";
    
$resultjobrole = mysql_query($sql, $conn) or die(mysql_error());

  while ($newarray = mysql_fetch_array($resultjobrole)) {
	$job_role = $newarray['jobrole'];
	echo $job_role . "\n\r";
	//save_item($job_role);

	save_to_excel($objPHPExcel, $sheetnumber, $job_role);
        $sheetnumber += 1; 
               
  }    



save_to_excel_w1($objPHPExcel);




// Save Excel 2007 file
echo date('H:i:s') , " Write to Excel2007 format" , EOL;
$callStartTime = microtime(true);

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save(str_replace('.php', '.xlsx', __FILE__));
$callEndTime = microtime(true);
$callTime = $callEndTime - $callStartTime;

echo date('H:i:s') , " File written to " , str_replace('.php', '.xlsx', pathinfo(__FILE__, PATHINFO_BASENAME)) , EOL;
echo 'Call time to write Workbook was ' , sprintf('%.4f',$callTime) , " seconds" , EOL;
// Echo memory usage
echo date('H:i:s') , ' Current memory usage: ' , (memory_get_usage(true) / 1024 / 1024) , " MB" , EOL;



// Echo memory peak usage
echo date('H:i:s') , " Peak memory usage: " , (memory_get_peak_usage(true) / 1024 / 1024) , " MB" , EOL;

// Echo done
echo date('H:i:s') , " Done writing files" , EOL;
echo 'Files have been created in ' , getcwd() , EOL;





$bodytext = "test";



$email = new PHPMailer();
$email->From      = 'jc@bacher.dk';
$email->FromName  = 'JC';
$email->Subject   = 'XLS file';
$email->Body      = $bodytext;
$email->AddAddress( 'jc@jc-net.dk' );

$file_to_attach = '/script';

$email->AddAttachment( "cph.xlsx" );

$email->Send();

echo "Mail sent?";


            
?>
