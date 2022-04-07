$(document).ready(function(){
	$.ajax({
		url:"http://localhost/data.php",
		method: "GET",
		async:true,
		crossDomain:true,
		success: function(data){
			console.log(data);
			var payload = JSON.parse(data);
			var timestamp = [];
			var connections = [];

			for(var i = 0; i < payload.length; i++){
				timestamp.push(payload[i].timestamp);
				connections.push(payload[i].number_of_users);
			}
		
			var chartdata = {
				labels: timestamp,
				datasets : [
					{
						label: 'Users',
						//backgroundColor: 'rgba(200, 200, 200, 0.75)',
						borderColor: 'rgba(255, 153, 51, 0.75)',
						fill: false,
		                hoverbackgroundColor: '#334756',
	            		hoverborderColor: 'rgba(200, 200, 200, 1)',
	            		pointRadius: 0,
	            		tension: 0.1,
	            		data:connections
					}
				]
			};

			var ctx = $("#chart");

			var barGraph = new Chart(ctx,{
				type: 'line',
				data: chartdata
			});
		},
		error: function(data){
			console.log("error");
			console.log(data);
		}
	});
});
