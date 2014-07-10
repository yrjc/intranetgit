
<?php
       $conn = mysql_connect("192.168.0.190", "arduino", "Mo11yX3q");
	//mysql_set_charset('utf8', $conn);
	mysql_select_db("kunder",$conn);

       $connWebshop = mysql_connect("www.bacher.dk", "bacher_bi_u", "vodToutfuc");
	//mysql_set_charset('utf8', $conn);
	mysql_select_db("eshopbacher_bi",$connWebshop);

        
?>
