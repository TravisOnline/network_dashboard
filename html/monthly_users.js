$(document).ready(function(){
	$.ajax({
		url:"http://localhost/monthly_users_data.php",
		method: "GET",
		async:true,
		crossDomain:true,
		success: function(data){
			console.log(data);
			var payload = JSON.parse(data);
			var users = [];

			for(var i = 0; i < payload.length; i++){
				users.push(payload[i].device_name);
			}

			daily_div = document.getElementById('monthly_users_div');

			for(var i = 0; i < users.length; i++){
				daily_div.innerHTML += users[i] + '<br>';
			}
		},
		error: function(data){
			console.log("error");
			console.log(data);
		}
	});
});