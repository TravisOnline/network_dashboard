<?php
	//connection details here

	$conn = mysqli_connect($servername, $username, $password, $dbname);

	if ($conn->connect_error){
		die("Conenction Failed: " . $conn->connect_error);
	}

	$output = array();

	$get_active_time = "SELECT @active_time := (SELECT time from device_monitor ORDER BY ID DESC LIMIT 1)";
	$conn->query($get_active_time);

	$get_active_month = "SELECT @active_month := (SELECT date from device_monitor ORDER BY ID DESC LIMIT 1)";
	$conn->query($get_active_month);

	$sql2 = "SELECT * FROM device_monitor where time = @active_time and date = @active_month";
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
