<?php
	//connection details here

	$conn = mysqli_connect($servername, $username, $password, $dbname);

	if ($conn->connect_error){
		die("Conenction Failed: " . $conn->connect_error);
	}

	$output = array();

	$check_date = "SELECT @this_month := max(date) FROM device_monitor";
	$conn->query($check_date);

	$sql = "SELECT DISTINCT device_name FROM device_monitor WHERE SUBSTRING(date,0,6) = SUBSTRING(@this_month,0,6)";
	$result = $conn->query($sql);
	if($result->num_rows > 0){
			foreach($result as $row){
			$output[] = $row;
		}
	}else{
		echo "0 results";
	}

	print json_encode($output);
	mysqli_close($conn);
?>
