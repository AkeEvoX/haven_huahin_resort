var admin = {};


admin.login = function(){

	var redirect = "main.html";
	var endpoint = "services/authen.php";
	var method='post';
	var data = new FormData($('#form_authen')[0]);
	//data.append("inputUser","12345");
	
	//getJSON
 	utility.data(endpoint,method,data,function(resp){
		
		var data = JSON.parse(resp);
		console.log(data.code);
		
		 if(data.code!="-1"){
			window.location = redirect;
			console.log('login success');
		}
		else{
			alert(data.result);
		}
		
		
	}); 
	

}

admin.verify_input = function(){

	var result = true;

	if($('#inputUser').val()=="" || $('#inputPassword').val() == "" ){
		alert('please enter username or password');
		result = false;
	}

	return result;

}

admin.logout = function(){
	var endpoint='services/logout.php'
	$.post(endpoint,function(resp){
		console.warn(resp);
	});
	
}