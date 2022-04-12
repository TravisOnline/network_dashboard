# network_dashboard

This dashboard was designed and created over a weekend with the intention to log and monitor all devices on my home network. The python script will ping every IP address on my subnet every 15min, feeding this information in 15min gaps to a chart on the dashboard. The dashboard also stores the hosts captured from the last scan, hosts that have been active today, and hosts that have been active this month. 

The specs on the server running it are really horrible, and I didn't want to store sensitive network information externally, so I setup a LAMP stack. This project assumes you have a lamp stack setup, and the correct dependancies installed for Python.

I used python 3.8 when programming the network scanner, and as such 'sudo python3.8 networkscan.py' was used to launch the program as it requires elevated privaleges.

## Architecture


The design of this dashboard can be broken down into 5 parts:

1. networkscan.py
	..* This python program will send out a broadcast to every device on the specified network every x minutes via the scheduler method.
	..* Upon doing so, with each reply it will acquire the hostname and mac address from each reply.
	..* After teach field has been acquired, python will write the host data to a table in mysql.
	..* This program will store DB login information seperately from the python script in attempt to be somewhat more secure.
2. MySQL table
	It's assumed that the table being written to will have the following columns:
	date - varchar(10),
	time - varchar(8),
	ip_address - varchar(15),
	device_name - varchar(50),
	id - int NOT NULL AUTO_INCREMENT PK

I'm sure it's a bit strange that i decided to store timestamps and dates this way, though it made more sense for me personally when running MySQL queries from my APIs.

3. index.php
	Draws the chart for the user tracking, also hosts our JS scripts that query APIs and output to our 4 different fields

4. js files
	Query JSON objects provided by our php APIs

5. php files
	Queries our MYSQL database and encodes the results as json objects


## KNOWN ISSUES:
	- The dashboard is unavailable for remote hosts - i need to implement access to it without enabling XSS attacks
	- Pythonscript hugely susceptible for SQL injection attacks
	- No JSON auth for APIs
	- Inefficient and dangerous DB login measures in the php files
