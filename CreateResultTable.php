<?php
	
	$mysqli = new mysqli($_ENV['OPENSHIFT_MYSQL_DB_HOST'] . ':' . $_ENV['OPENSHIFT_MYSQL_DB_PORT'],
			$_ENV['OPENSHIFT_MYSQL_DB_USERNAME'],
			$_ENV['OPENSHIFT_MYSQL_DB_PASSWORD'], "caterpillars");
	
	echo $_ENV['OPENSHIFT_MYSQL_DB_HOST'] . $_ENV['OPENSHIFT_MYSQL_DB_PORT'] . $_ENV['OPENSHIFT_MYSQL_DB_USERNAME'] . $_ENV['OPENSHIFT_MYSQL_DB_PASSWORD'];
		
	if($mysqli->connect_errno) die("Error: " . mysqli_error($mysqlCon));
	
	echo 'sucessfully connected<br>';
	
	$mysqli->query("drop table if exists quiz_results");
	$mysqli->query("create table quiz_results ( " .
       "id int primary key not null auto_increment, " .
       "username char(50) not null, " .
       "quiz_time datetime not null, " .
	   "score int not null, " . 
	   "med_diff int not null)");
	
	
	$mysqli->close();

?>