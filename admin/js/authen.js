var admin = {};


admin.login = function(){

	var redirect = "main.html";

	var endpoint = "../service/authen.php";
	var method = "post";
	
	window.location = redirect;

}

admin.verify_input = function(){

	var result = true;

	if($('#inputUser').val()=="" || $('#inputPassword').val() == "" ) {
		alert('please enter username or password');
		result = false;
	}

	return result;

}