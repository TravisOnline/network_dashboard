# Network Monitor Dashboard

This dashboard was designed and created over a weekend with the intention to log and monitor all devices on my home network. The python script will ping every IP address on my subnet every 15min, feeding this information in 15min gaps to a chart on the dashboard. The dashboard also stores the hosts captured from the last scan, hosts that have been active today, and hosts that have been active this month. 

The specs on the server running it are really horrible, and I didn't want to store sensitive network information externally, so I setup a LAMP stack running on a stripped back Linux Lite installation. This project assumes you have a lamp stack setup and the correct dependancies installed for Python.

I used python 3.8 when programming the network scanner, and as such 'sudo python3.8 networkscan.py' was used to launch the program as it requires elevated privaleges.

## Architecture


The design of this dashboard can be broken down into 5 parts:

1. networkscan.py
	* Uses a combination of scapy and socket to collect host information and write to the MySQL database.
	* This python program will send out a broadcast to every device on the specified network every x minutes via the scheduler method 
	```python
	schedule.every(15).minutes.at(":00").do(collect_hosts)
	```
	* Upon doing so, with each reply collected in collecthosts(), it will acquire the hostname and mac address from each reply's IP Address.
	* After each field has been acquired, python will treat the data, and then write the host data to a table in mysql.
	* This program will store DB login information seperately from the python script in attempt to be somewhat more secure than having it present in the script outright. It's assumed that this file is difficult to access as the server is running a relatively strict firewall, and RDP requires a parallel SSH connection, as well as permissions being tweaked on the sever in an attempt to defend against lateral moving attacks, or accessing the /etc/ directory outright.
	```python
	conn = pymysql.connect(read_default_file="/etc/my.cnf")
	```
2. MySQL table
	* It's assumed that the table being written to will have the following columns:
	1. date - varchar(10),
	2. time - varchar(8),
	3. ip_address - varchar(15),
	4. device_name - varchar(50),
	5. id - int NOT NULL AUTO_INCREMENT PK


	* In retrospect, I should have stored my dates as a proper date timestamp in mysqli to avoid convoluted mysql queries such as data.php's query to capture the last 96 entries for counted devices, sorting by latest date and then timestamp: 
	```mysqli
	SELECT t.* 
		 FROM (SELECT date, time as 'timestamp', COUNT(*) AS 'number_of_users' 
		 FROM device_monitor GROUP BY date, timestamp ORDER BY date DESC, time DESC LIMIT 96) AS t
		 ORDER BY t.date ASC, t.timestamp ASC
	```

3. index.php
	* Draws the chart for the user tracking, also hosts our JS scripts that query APIs and output to our 4 different fields. (Ideally, I will change this to a simple html file in the future).

4. js files
	* Query JSON objects provided by our php APIs. In this snippet, app.js stores information from data.php (timestamps and user counts) before feeding the information to the chart on the dashboard.
	```javascript
	for(var i = 0; i < payload.length; i++){
		timestamp.push(payload[i].timestamp);
		connections.push(payload[i].number_of_users);
	}
	``` 

5. php files
	* Queries our MYSQL database and encodes the results as json objects. This snippet is from active_now.php, which determines which hostnames and their associated IP addresses were present in the last scan. This is then encoded as an array of JSON objects to be digested by the active_now.js file.
	```php
	$get_active_time = "SELECT @active_time := (SELECT time FROM device_monitor ORDER BY ID DESC LIMIT 1)";
	$conn->query($get_active_time);
	$get_active_month = "SELECT @active_month := (SELECT date FROM device_monitor ORDER BY ID DESC LIMIT 1)";
	$conn->query($get_active_month);
	$sql2 = "SELECT * FROM device_monitor WHERE time = @active_time AND date = @active_month";
	if($result2->num_rows > 0){
		foreach($result2 as $row){
		$output[] = $row;
	}
	print json_encode($output);
	``` 


## KNOWN ISSUES:
	- The dashboard is unavailable for remote hosts - i need to implement access to it without enabling XSS attacks
	- The networkscan python script hugely susceptible to SQL injection attacks. Needs more data treatment before writing to the SQL table, and error logging.
	- No JSON auth for APIs
	- Inefficient and dangerous DB login measures in the php files
