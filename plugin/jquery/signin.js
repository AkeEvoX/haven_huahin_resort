
$('#inpuUser').keypress(function(e){
	
	if(e.which==13)
	{
		 login();
	}
});

$('#inputPassword').keypress(function(e){
	
	if(e.which==13)
	{
		 login();
	}
});



$('#btnSignin').click(function(){
	
	//validate val user/pass
	 login();
});


function login()
{
	
	
	var info = {};
	info["user"] = $('#inpuUser').val();
	info["pass"] = $('#inputPassword').val();
	
	if(info["user"]=="" || info["pass"] =="")
	{
		alert('กรุณากรอก username / password !!!');
		return;
	}
	
	
	
	$.ajax({
		url:'controller/authentication.php?rdm='+new Date().getTime(),
		type:'post',
		data:JSON.stringify(info),
		dataType:'JSON',
		contentType: "application/json; charset=utf-8",
		success : function(data){
			
		
			if(data.code=="1")
			{
				window.location='home.php';
			}
			else{
				alert(data.desc);
			}
			//redirect to next page
			//console.log(data);
		},
		error : function(xhr,status,error){
			alert(xhr.responseText);
			console.log(xhr.responseText);
		}
	});


	
}

