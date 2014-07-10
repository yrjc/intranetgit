<?php
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);

require 'include/include.php';
require_once '/var/www/cph/Classes/PHPMailer/class.phpmailer.php';







function saveitem($item1, $item2) {
    //jobrole, point
    include 'include/include.php';

  //echo $item . "\n\r";

  $sqlinsert = "SELECT * FROM eshopbacher_bi.user where job_roles = '$item1' and company_name like '%lufthavn%';";
  $result = mysql_query($sqlinsert, $connWebshop) or die(mysql_error());
  
    
 
  echo $sqlinsert;
  echo "<br>";
 
    while ($newarray = mysql_fetch_array($result)) {
            $salnr = $newarray['salary_number'];
            $name = $newarray['full_name'];

            //$jobrole = $newarray['jobrole'];
            //echo "salnr: ".$salnr."point: ".$item2;
            
            
            $sql = "insert into cphpointfile values('','12733','-','$salnr','$item2','$name','Point tildeling','1')";
            $result2 = mysql_query($sql, $conn) or die(mysql_error());

            echo $sql;
            echo "<br>";
            
        }
 
  
  //$resultinsert = mysql_query($sqlinsert, $conn) or die(mysql_error());
  
  
}





        echo "Result:" . "<br>";
        $sql = "SELECT * from kunder.cphjobrolespoint;";	    
	$result = mysql_query($sql, $conn) or die(mysql_error());

        
        while ($newarray = mysql_fetch_array($result)) {
            $id = $newarray['ID'];
            $jobrole = $newarray['jobrole'];
            $point = $newarray['point'];

            if ($_REQUEST[$jobrole] <> '') 
                { 
                    echo $jobrole . " " . $_REQUEST[$jobrole] . "<br>"; 
                    //saveitem($jobrole, $_REQUEST[$jobrole]);
                    $sqlinsert = "insert into kunder.cphpointfound values('','".$jobrole."','".$_REQUEST[$jobrole]."');";
                    $resultinsert = mysql_query($sqlinsert, $conn) or die(mysql_error());

                    echo $sqlinsert;
                    echo "<br>";
                    
                }
            
            //echo $jobrole . " " . $_REQUEST['$jobrole'] . " " . $point . "\n\r";
        }

//ready to find users identified by jobrole in table cphpointfound

        
        $sql = "SELECT * from kunder.cphpointfound;";	    
	$result = mysql_query($sql, $conn) or die(mysql_error());

        
        while ($newarray = mysql_fetch_array($result)) {
            $id = $newarray['ID'];
            $jobrole = $newarray['jobrole'];
            $point = $newarray['point'];
            
            saveitem($jobrole, $point);

        }

        

//ready to create outfile
//ready to create outfile
$dtfile = date("Y-m-d_His") . ".csv";
$dt = "/var/www/cph/log/" . $dtfile; 
echo $dt . "<br>";        
$sql = "select debnr, afd, salnr, point, name, text, overwrite INTO OUTFILE '$dt' FIELDS TERMINATED BY ',' enclosed by '\"' LINES TERMINATED BY '\n' FROM kunder.cphpointfile;";
echo $sql . "<br>";        

$result3 = mysql_query($sql, $conn) or die(mysql_error());

if (mysql_errno()) {
        echo "MySQL error ".mysql_errno().": ".mysql_error()."\n";       
} 




$bodytext = "test";

$email = new PHPMailer();
$email->From      = 'jc@bacher.dk';
$email->FromName  = 'JC';
$email->Subject   = 'CPH import file';
$email->Body      = $bodytext;
$email->AddAddress( 'jc@jc-net.dk' );

$file_to_attach = '/var/www/cph/log';

$email->AddAttachment( $dt );

$email->Send();



            
        
?>
