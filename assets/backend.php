<?php

	$path_components = explode('/', $_SERVER['PATH_INFO']);

	if ($_SERVER['REQUEST_METHOD'] == "GET" && isset($_REQUEST['action']) && $_REQUEST['action'] == "quizData") {
		
		$mysqli = new mysqli($_ENV['OPENSHIFT_MYSQL_DB_HOST'] . ':' . $_ENV['OPENSHIFT_MYSQL_DB_PORT'],
			$_ENV['OPENSHIFT_MYSQL_DB_USERNAME'],
			$_ENV['OPENSHIFT_MYSQL_DB_PASSWORD'], "caterpillars");
			
			
		if($mysqli->connect_errno) die("Error: " . mysqli_error($mysqlCon));

		$json_array = array();
		$required = array("Araneae", "Auchenorrhyncha", "Coleoptera", "Heteroptera", "Opiliones");		

			$result = $mysqli->query("SELECT * FROM quiz_results WHERE userID = " . urldecode($_GET['userData']));
			
			
			if ($result->num_rows == 0) {
				// Give user the static first quiz 
				$photos = array("http://static.inaturalist.org/photos/3297131/medium.jpg?1459878547","http://static.inaturalist.org/photos/3285686/medium.jpg?1459740754","http://static.inaturalist.org/photos/2254750/medium.JPG?1439339664","http://static.inaturalist.org/photos/3245329/medium.JPG?1459225141","http://static.inaturalist.org/photos/3216215/medium.jpg?1458852346","http://static.inaturalist.org/photos/2950963/medium.jpg?1454227930","http://static.inaturalist.org/photos/2617917/medium.JPG?1446837568","http://static.inaturalist.org/photos/1590335/medium.jpg?1425330543","http://static.inaturalist.org/photos/2104173/medium.jpg?1436381342","http://static.inaturalist.org/photos/2985908/medium.jpg?1454950250");
				$answers = array("Coleoptera","Heteroptera","Auchenorrhyncha","Opiliones","Coleoptera","Araneae","Lepidoptera larvae","Orthoptera","Auchenorrhyncha","Heteroptera");
				
				for ($i = 0; $i < 10; $i++){
					$json_obj = array('imageURL' => $photos[$i], 'correctAnswer' => $answers[$i]);
					array_push($json_array, $json_obj);
				}
				
			} else {
				
				$url_array = array();
				
				foreach ($required as $x) {
					$result = $mysqli->query("SELECT * FROM quiz_photos WHERE classification = '" . $x . "' order by RAND() LIMIT 1");
					$row = $result->fetch_assoc();
					$json_obj = array('imageURL' => $row['url'], 'correctAnswer' => $row['classification']);
					array_push($json_array, $json_obj);
					array_push($url_array, $row['url']);
				}
				
				
				while (count($json_array) < 10) {
					
					$shouldBeUniformlyRandom = rand(1,3);

					if($shouldBeUniformlyRandom > 2){
						$result = $mysqli->query("SELECT * FROM quiz_photos order by RAND() LIMIT 1");
					}else{
						$result = $mysqli->query("SELECT * FROM quiz_photos WHERE classification NOT IN ('".implode("','",$required)."') order by RAND() LIMIT 1");
					}

					
					$row = $result->fetch_assoc();

					if (!in_array($row['url'], $url_array)) {
						$json_obj = array('imageURL' => $row['url'], 'correctAnswer' => $row['classification']);
						array_push($json_array, $json_obj);
						array_push($url_array, $row['url']);
					}
		
				}
				

				shuffle($json_array);
			}
		
		
		header("Content-type: application/json");
		print(json_encode($json_array));
		
		
		//echo $_GET['username'];
		
		exit();

	} else if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_REQUEST['action']) && $_REQUEST['action'] == "submit") {
		
		$userID = $_REQUEST['userData'];
		$points = $_REQUEST['currentQuizScoreData'];
		$tpp = $_REQUEST['totalPointsPossible'];
		$score = tallyPoints($points);
		
		$mysqli = new mysqli($_ENV['OPENSHIFT_MYSQL_DB_HOST'] . ':' . $_ENV['OPENSHIFT_MYSQL_DB_PORT'],
			$_ENV['OPENSHIFT_MYSQL_DB_USERNAME'],
			$_ENV['OPENSHIFT_MYSQL_DB_PASSWORD'], "caterpillars");
			
		if($mysqli->connect_errno) die("Error: " . mysqli_error($mysqlCon));
		$result = $mysqli->query("INSERT INTO quiz_results VALUES (0, '" . $userID . "', NOW(), " . $score . ", " . $tpp . ")");		
		$response = "";
		
		if ($result) {
			$lastID = $mysqli->insert_id;
			$response = round(percentile($lastID));
		} else {
			$response = "Failed to record quiz.";
		}
		
		header('Content-Type:text/plain');
		print_r($response);
		exit();

	} elseif ($_SERVER['REQUEST_METHOD'] == "GET" && isset($_REQUEST['action']) && $_REQUEST['action'] == "statData") {
		$userID = urldecode($_GET['userData']);
		
		$mysqli = mysqli_connect($_ENV['OPENSHIFT_MYSQL_DB_HOST'] . ':' . $_ENV['OPENSHIFT_MYSQL_DB_PORT'],
			$_ENV['OPENSHIFT_MYSQL_DB_USERNAME'],
			$_ENV['OPENSHIFT_MYSQL_DB_PASSWORD'], "caterpillars") or die("Error " . mysqli_error($connection));

		$sql = "select * from quiz_results where userID = " . $userID;
    	$result = mysqli_query($mysqli, $sql) or die("Error in Selecting " . mysqli_error($connection));
		
		$response = array();
    	while($row = mysqli_fetch_assoc($result))
    	{
    		$row["percentile"] = round(percentile($row["id"]));
        	$response[] = $row;
    	}
		
		header("Content-type: application/json");
		print(json_encode($response));
		exit();
	} elseif ($_SERVER['REQUEST_METHOD'] == "GET" && isset($_REQUEST['action']) && $_REQUEST['action'] == "guestPercentile") {
		header('Content-Type:text/plain');
		print_r(round(guestPercentile($_GET['tpe'], $_GET['tpp'])));
		exit();
	}
	
	function percentile($id){
		if(!is_numeric($id) || $id < 0) { return 0; }
		
		$mysqli = new mysqli($_ENV['OPENSHIFT_MYSQL_DB_HOST'] . ':' . $_ENV['OPENSHIFT_MYSQL_DB_PORT'],
			$_ENV['OPENSHIFT_MYSQL_DB_USERNAME'],
			$_ENV['OPENSHIFT_MYSQL_DB_PASSWORD'], "caterpillars");
			
		if($mysqli->connect_errno) die("Error: " . mysqli_error($mysqlCon));
		
		$result = $mysqli->query("SELECT COUNT(*) as C FROM quiz_results WHERE (score / tpp) < (SELECT (score / tpp) FROM quiz_results WHERE id = " . $id . ")");
		$row = $result->fetch_assoc();
		$lessThan = $row['C'];
		
		$result = $mysqli->query("SELECT COUNT(*) as C FROM quiz_results WHERE (score / tpp) = (SELECT (score / tpp) FROM quiz_results WHERE id = " . $id . ")");
		$row = $result->fetch_assoc();
		$equalTo = $row['C'];
		
		$result = $mysqli->query("SELECT COUNT(*) as C FROM quiz_results");
		$row = $result->fetch_assoc();
		$totalEntries = $row['C'];
		
		$percentile = ($lessThan + (0.5 * $equalTo)) / $totalEntries;
		
		return 100 * $percentile; 		
	}
	
	function guestPercentile($tpe, $tpp){
		if(!is_numeric($tpe) || !is_numeric($tpp)) { return 0; }
	
		$mysqli = new mysqli($_ENV['OPENSHIFT_MYSQL_DB_HOST'] . ':' . $_ENV['OPENSHIFT_MYSQL_DB_PORT'],
			$_ENV['OPENSHIFT_MYSQL_DB_USERNAME'],
			$_ENV['OPENSHIFT_MYSQL_DB_PASSWORD'], "caterpillars");
			
		if($mysqli->connect_errno) die("Error: " . mysqli_error($mysqlCon));
		
		$result = $mysqli->query("SELECT COUNT(*) as C FROM quiz_results WHERE (score / tpp) < (" . ($tpe/$tpp) . ")");
		$row = $result->fetch_assoc();
		$lessThan = $row['C'];
		
		$result = $mysqli->query("SELECT COUNT(*) as C FROM quiz_results WHERE (score / tpp) = (" . ($tpe/$tpp) . ")");
		$row = $result->fetch_assoc();
		$equalTo = $row['C'];
		
		$result = $mysqli->query("SELECT COUNT(*) as C FROM quiz_results");
		$row = $result->fetch_assoc();
		$totalEntries = $row['C'];
		
		$percentile = ($lessThan + (0.5 * ($equalTo + 1))) / ($totalEntries + 1); //Adding 1 to the equal portion and total entries here to simulate the guest entry actually being in the db
		
		return 100 * $percentile;
	}
	
	function tallyPoints($points){
		$score = 0;
		foreach ($points as &$value) {
			if(is_numeric($value)){
				$score = $score + $value;
			} else {
				return 0; //Bad data, dump this score 
			}
		}
		return $score; 
	}
?>