<?php
	$servername = "localhost";
	$username = "root";
	$password = "_D_00_m_ii";
	$dbname = "system_monitor";

	$conn = mysqli_connect($servername, $username, $password, $dbname);

	if ($conn->connect_error){
		die("Conenction Failed: " . $conn->connect_error);
	}

	$output = array();

	$sql2 = "SELECT date, time as 'timestamp',
			 COUNT(*) as 'number_of_users'
			 FROM device_monitor
			 GROUP BY date, timestamp
			 LIMIT 96";
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