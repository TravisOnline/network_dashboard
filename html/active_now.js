$(document).ready(function(){
	$.ajax({
		url:"http://localhost/active_now.php",
		method: "GET",
		async:true,
		crossDomain:true,
		success: function(data){
			console.log(data);
			var payload = JSON.parse(data);
			var users = [];
			var addresses =[];

			for(var i = 0; i < payload.length; i++){
				users.push(payload[i].device_name);
				addresses.push(payload[i].ip_address);
			}

			active_now_div = document.getElementById('active_now');

			for(var i = 0; i < users.length; i++){
				active_now_div.innerHTML += users[i] + ' - ' + addresses[i] + '<br>';
			}
		
		},
		error: function(data){
			console.log("error");
			console.log(data);
		}
	});
});