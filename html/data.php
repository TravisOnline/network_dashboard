<?php
	//connection details here

	$conn = mysqli_connect($servername, $username, $password, $dbname);

	if ($conn->connect_error){
		die("Conenction Failed: " . $conn->connect_error);
	}

	$output = array();

	$sql2 = "SELECT t.* 
		 FROM (SELECT date, time as 'timestamp', COUNT(*) AS 'number_of_users' 
		 FROM device_monitor GROUP BY date, timestamp ORDER BY date DESC, time DESC LIMIT 96) AS t
		 ORDER BY t.date ASC, t.timestamp ASC";
	$result2 = $conn->query($sql2);
	if($result2->num_rows > 0){
			foreach($result2 as $row){
			$output[] = $row;
		}
	}else{
		echo "0 results";
	}

	print json_encode($output);
	mysqli_close($conn);
?>
