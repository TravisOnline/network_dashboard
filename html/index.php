<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
		<script type = "text/javascript" src="app.js"></script>
		<script type = "text/javascript" src="daily_users.js"></script>
		<script type = "text/javascript" src="monthly_users.js"></script>
		<script type = "text/javascript" src="active_now.js"></script>
		<title>Network Usage</title>

		<style type="text/css">
			body{
				font-family: Arial;
				margin:  120px 100px 10px 100px;
				padding:  0;
				color: white;
				text-align: center;
				background: #272121;
			}

			.container{
				color: #734046;
				padding: 10px;
			}

			.float-container{
				display: flex;
			}

			.float-child{
				float:  center;
				flex:  1;
				padding: 20px;
				border:  2px #59405C;
				width: 75%;
			}

			.daily-users-div{
				color: #E79E4F;
			}

			.monthly-users-div{
				color: #E79E4F
			}

			.active-now{
				color: #519872;
			}


		</style>
	</head>
	<body id="body">
		<div class="container">
			<canvas id="chart" style="width: 100%; height: 60vh; background: #222; border: 1px solid #443737; margin-top: 10px;"></canvas>
		</div>

		<div class="float-container">


			<div class="float-child">
				<div class="active-now" id="active_now"> <h2>Active Now</h2></div>
			</div>
			<div class="float-child">
				<div class="daily-users-div" id="daily_users_div"><h2>Daily Users</h2></div>
			</div>

			<div class="float-child">
				<div class="monthly-users-div" id="monthly_users_div"><h2>Monthly Users</h2></div>
			</div>

		</div>
		<!-- javascript -->
		

	</body>
</html>