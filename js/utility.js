$(document).ready(function(){
	
	utility.initial();
	$(".dropdown-menu .sub-menu").hide();
});

var utility = function(){};

utility.initial = function(){

}
/*for view data only*/
utility.service = function(url,method,args,success_callback,complete_callback){

	$.ajax({
		url:url,
		data:args,
		contentType: "application/x-www-form-urlencoded;charset=utf-8",
		type:method,
		dataType:'json',
		cache:false,
		//processData:false,
		success:success_callback,
		complete:complete_callback,
		error:function(xhr,status,error){

			var result = {'page':url
									,'args':args
								 ,'msg':xhr.responseText};

			console.error(result);
			alert("page="+result.page+"\nargs="+JSON.stringify(result.args)+"\nmsg="+result.msg);
		}
	});

}


//for insert/update/delete data
utility.data = function (endpoint,method,args,success_callback,complete_callback){
		$.ajax({
		url:endpoint,
		type:'post',
		// dataType:'json',
		data:args,
		contentType:false,
		cache:false,
		processData:false,
		success:success_callback,
		complete:complete_callback,
		error:function(xhr,status,error){

			var result = {'page':url
									,'args':args
								 ,'msg':xhr.responseText};

			console.error(result);
			alert("page="+result.page+"\nargs="+JSON.stringify(result.args)+"\nmsg="+result.msg);
		}
	});
}

utility.uploads = function(endpoint,files,success_callback){
	
	$.ajax({
		url:endpoint,
		type:'post',
		data:files,
		contentType:false,
		cache:false,
		processData:false,
		success:success_callback
	});
	
	/*https://www.formget.com/ajax-image-upload-php/*/
}

utility.log = function(type,message){

	var args = {'_':new Date().getMilliseconds(),'msg':message,'type':type} ;
	this.service("services/logger.php",'POST',args,null,null);
}


utility.querystr = function(name,url){

	if (!url) url = window.location.href;
    name = name.replace(/[\[\]]/g, "\\$&");
    var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
        results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, " "));
}

utility.showmodal = function(){
	
	$('#modaldialog').modal('show');
	
}

utility.modalpage = function(title,url,event){

	if(title!=null)
		$('#modaltitle').html(title);

	var d= new Date();
	$('#modalcontent').load(url,event);
	$('#modaldialog').modal('show');

}

utility.modalimage = function(title,url){

	if(title!=null)
		$('#modaltitle').html(title);

	//$('.modal').on('show.bs.modal', centerModal);

	$('#modalcontent').html("<img src='"+url+"' onerror=this.src='images/common/unavaliable.jpg'  class='img-responsive' /> ");
	//$('.modal').on('show.bs.modal', centerModal);
	$('#modaldialog').modal('show');

	//$('.modal:visible').each(centerModal);
	
	

}

utility.convertToObject = function(arrlist){

	var result = {};
	
	$.each(arrlist,function(i,val){
		result[val.name] = val.value;
	});

	return result;

}

utility.replace = function(str,find,replace){
	return str.replace(new RegExp(escapeRegExp(find), 'g'), replace);
}
/*function find string for replace*/
function escapeRegExp(str) {
    return str.replace(/([.*+?^=!:${}()|\[\]\/\\])/g, "\\$1");
}

utility.date_format_th = function (date){
	//dd/MM/yyyy

	var months = [" มกราคม "," กุมภาพันธ์ "," มีนาคม "," เมษายน "," พฤษภาคม "," มิถุนายน "," กรกฏาคม "," สิงหาคม "," กันยายน "," ตุลาคม "," พฤศจิกายน "," ธันวาคม "];
	var dates = date.split("/");
	var day = dates[0];
	var month = parseInt(dates[1].replace("0",""))-1;
	var year = parseInt(dates[2]) + 543; 

	return day + months[month] + year ; 

}

utility.date_format = function (date,lang){
	
	var dates = date.split("/");
	var months = Array();
	var day = dates[0];
	var month = parseInt(dates[1].replace("0",""))-1;
	var year =0;
	
	if(lang=="th"){
		months = [" มกราคม "," กุมภาพันธื "," มีนาคม "," เมษายน "," พฤษภาคม "," มิถุนายน "," กรกฏาคม "," สิงหาคม "," กันยายน "," ตุลาคม "," พฤศจิกายน "," ธันวาคม "];
		year = parseInt(dates[2])+543;
	}else  //default : english
	{
		months = [" January "," Febuary "," March "," April "," May "," June "," July "," August "," September "," October "," November "," December "];	
		year =parseInt(dates[2]);
	}
	
	return day + months[month] + year ; 

}

utility.createCookie = function (name,value,days) {
    var expires = "";
    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days*24*60*60*1000));
        expires = "; expires=" + date.toUTCString();
    }
    document.cookie = name + "=" + value + expires + "; path=/";
}


utility.readCookie = function (name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for(var i=0;i < ca.length;i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1,c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
    }
    return null;
}

utility.eraseCookie = function(name) {
    utility.createCookie(name,"",-1);
}

utility.get_templete = function(src){
	var result = "";
	result = $.ajax({
		url:src,
		type:'get',
		async: false
	}).responseText;
	return result;
}


function centerModal() {

    $(this).css('display', 'block');
    var $dialog = $(this).find(".modal-dialog");

	//var imgwidth = $(this).find('.modal-body img').width();
	//$dialog.css({width:imgwidth+35});

    var offset = ($(window).height() - $dialog.height()) / 2;
    // Center modal vertically in window
    $dialog.css("margin-top", offset);

}
//hello
