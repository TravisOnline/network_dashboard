# network_dashboard

This dashboard was designed and created over a weekend with the intention to log and monitor all devices on my home network. The python script will ping every IP address on my subnet every 15min, feeding this information in 15min gaps to a chart on the dashboard. The dashboard also stores the hosts captured from the last scan, hosts that have been active today, and hosts that have been active this month. 

The specs on the server running it are really horrible, and I didn't want to store sensitive network information externally, so I setup a LAMP stack. This project assumes you have a lamp stack setup, and the correct dependancies installed for Python.

I used python 3.8 when programming the network scanner, and as such 'sudo python3.8 networkscan.py' was used to launch the program as it requires elevated privaleges.

## Architecture


The design of this dashboard can be broken down into 5 parts:

1. networkscan.py
	* This python program will send out a broadcast to every device on the specified network every x minutes via the scheduler method ```schedule.every(15).minutes.at(":00").do(collect_hosts)```
	* Upon doing so, with each reply it will acquire the hostname and mac address from each reply.
	* After each field has been acquired, python will treat the data, and then write the host data to a table in mysql.
	* This program will store DB login information seperately from the python script in attempt to be somewhat more secure ```conn = pymysql.connect(read_default_file="/etc/my.cnf")```. It's assumed this is relatively secure as my server is running a firewall and has been hardened. To even connect to it, all RDP protocols have been encapsulated in SSH.
2. MySQL table
	* It's assumed that the table being written to will have the following columns:
	1. date - varchar(10),
	2. time - varchar(8),
	3. ip_address - varchar(15),
	4. device_name - varchar(50),
	5. id - int NOT NULL AUTO_INCREMENT PK


	* In retrospect, I should have stored my dates as a proper date timestamp in mysqli to avoid convoluted mysql queries such as: ```SELECT t.* 
		 FROM (SELECT date, time as 'timestamp', COUNT(*) AS 'number_of_users' 
		 FROM device_monitor GROUP BY date, timestamp ORDER BY date DESC, time DESC LIMIT 96) AS t
		 ORDER BY t.date ASC, t.timestamp ASC``` from data.php which simply looks to capture the last 96 entries of devices counted sorted by latest date, and then timestamp.

3. index.php
	* Draws the chart for the user tracking, also hosts our JS scripts that query APIs and output to our 4 different fields. (Ideally, I will change this to a simple html file in the future).

4. js files
	* Query JSON objects provided by our php APIs
	* ```for(var i = 0; i < payload.length; i++){
				timestamp.push(payload[i].timestamp);
				connections.push(payload[i].number_of_users);
			}``` (Taken from app.js which stores information from data.php on timestamps, and user counts before writing to the chart in index.php)

5. php files
	* Queries our MYSQL database and encodes the results as json objects
	* ```
	$get_active_time = "SELECT @active_time := (SELECT time from device_monitor ORDER BY ID DESC LIMIT 1)";
	$conn->query($get_active_time);

	$get_active_month = "SELECT @active_month := (SELECT date from device_monitor ORDER BY ID DESC LIMIT 1)";
	$conn->query($get_active_month);

	$sql2 = "SELECT * FROM device_monitor where time = @active_time and date = @active_month";
	
	if($result2->num_rows > 0){
		foreach($result2 as $row){
		$output[] = $row;
	}
	
	print json_encode($output);
	``` (taken from active_now.php, which determines which hosts were present in the last scan. This is then encoded as an array of josn objects) 


## KNOWN ISSUES:
	- The dashboard is unavailable for remote hosts - i need to implement access to it without enabling XSS attacks
	- Pythonscript hugely susceptible for SQL injection attacks
	- No JSON auth for APIs
	- Inefficient and dangerous DB login measures in the php files
