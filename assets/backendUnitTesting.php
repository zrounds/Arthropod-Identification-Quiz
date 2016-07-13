<?php
	/*include "backend.php";
	
	//DO NOT RUN THIS SCRIPT ON THE LIVE SERVER!!!! IT IS COMMENTED OUT TO PREVENT IT FROM BEING RUN ACCIDENTALLY.
	//THE TESTS THAT ARE CHECKING PERCENTILE REQUIRE ME TO KNOW THE STATE OF THE DB TO EXPECT A VALUE
	//AND THEREFORE THEY CLEAR THE QUIZ_RESULTS TABLE AND POPULATE IT IN A SPECIFIC WAY. IF YOU EVER
	//NEED TO RUN THESE TESTS, ONLY RUN THEM IN A DEVELOPMENT ENVIRONMENT. IF NOT, BE SURE TO SAVE A COPY
	//OF QUIZ_RESULTS USING PHPMYADMIN, THEN RESTORE THAT STATE AFTERWARDS. THAT WAY, ONLY ANY QUIZZES TAKEN
	//DURING YOUR TESTING WILL BE LOST. 
	
	class backendUnitTesting extends PHPUnit_Framework_TestCase 
	{
		public function testTallyPoints_1()
		{
			$points = array(1,2,3,4);
			$score = tallyPoints($points);
			
			$this->assertEquals($score, 10);
		}
		
		public function testTallyPoints_2()
		{
			$points = array(1,2,3,4,5,6,7,8,9,10);
			$score = tallyPoints($points);
			
			$this->assertEquals($score, 55);
		}
		
		public function testTallyPoints_3()
		{
			$points = array(1,2,3,4,"bogus stuff");
			$score = tallyPoints($points);
			
			$this->assertEquals($score, 0);
		}
		
		public function testGuestPercentileVersusPercentile(){ 
		//this checks that guest percentile returns the same value which percentile would if the entry were actually stored in the DB
		//It is run before remove results and the other percentile checks because it is mosst useful to test
		//these against each other on an unknown and as complicated state as possible
			$guestPercentile = (guestPercentile(10,15));
			$mysqli = mysqli_connect("localhost", "root", "sWOksgEOeL","caterpillars");
			
			if($mysqli->connect_errno) die("Error: " . mysqli_error($mysqlCon));
		
			$result = $mysqli->query("INSERT INTO quiz_results VALUES (0, 69, NOW(), 10, 15)");		//Allen is user 69, expect him to always exist if anyone
			$lastID = $mysqli->insert_id;
			
			$percentile = percentile($lastID);
			
			$this->AssertEquals($guestPercentile, $percentile);
			
		}
		
		public function testRemoveResults(){
			$mysqli = mysqli_connect("localhost", "root", "sWOksgEOeL","caterpillars");
			
			if($mysqli->connect_errno) die("Error: " . mysqli_error($mysqlCon));
		
			$result = $mysqli->query("DELETE from quiz_results");
			$result = $mysqli->query("SELECT * from quiz_results");
		
			$this->assertEquals($result->num_rows, 0);
		}
		
		public function testPercentileAll(){
		//SQL dump to get table into known state after the last test which removed everything
		//20 records
		$sql = "INSERT INTO `quiz_results` (`id`, `userID`, `quiz_time`, `score`, `tpp`) VALUES
(112, 153, '2016-04-25 19:27:06', 20, 22),
(113, 153, '2016-04-25 19:34:31', 18, 22),
(114, 152, '2016-04-25 19:36:38', 16, 22),
(115, 152, '2016-04-25 19:37:49', 19, 20),
(116, 152, '2016-04-25 19:41:38', 16, 19),
(117, 153, '2016-04-25 20:00:40', 10, 20),
(118, 152, '2016-04-26 10:43:43', 5, 20),
(119, 152, '2016-04-26 12:36:21', 3, 21),
(120, 69, '2016-04-26 12:37:09', 22, 22),
(121, 152, '2016-04-26 12:38:54', 17, 22),
(122, 158, '2016-04-26 12:47:27', 3, 22),
(123, 158, '2016-04-26 12:47:54', 3, 19),
(124, 158, '2016-04-26 12:49:43', 2, 21), 
(125, 158, '2016-04-26 12:50:08', 2, 17), 
(126, 158, '2016-04-26 12:50:31', 4, 20),
(127, 158, '2016-04-26 12:50:56', 2, 17), 
(128, 158, '2016-04-26 12:51:18', 2, 19), 
(129, 158, '2016-04-26 13:04:53', 2, 17), 
(130, 158, '2016-04-26 13:05:43', 2, 19), 
(131, 158, '2016-04-26 13:07:58', 0, 17); 
";
			$mysqli = mysqli_connect("localhost", "root", "sWOksgEOeL","caterpillars");
			
			if($mysqli->connect_errno) die("Error: " . mysqli_error($mysqlCon));
		
			$result = $mysqli->query("DELETE from quiz_results");
			$result = $mysqli->query($sql);
			
			$this->assertEquals(percentile(131), 2.5);
			$this->assertEquals(percentile(120), 97.5);
			$this->assertEquals(percentile(117), 62.5);
			$this->assertEquals(percentile(130), 15);
		}
		
		public function testBogusPercentileInput(){
			$this->assertEquals(percentile("Not a number"), 0);
			$this->assertEquals(percentile(-1), 0); //userID cannot be negative
		}
		
		public function testBogusGuestPercentileInput(){
			$this->assertEquals(guestPercentile("Not a number", 10), 0);
			$this->assertEquals(guestPercentile(10, "Not a number"), 0);
			$this->assertEquals(guestPercentile("Not a number", "Not a number"), 0);
		}
	}*/
?>