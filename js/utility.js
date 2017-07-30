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

utility.modal_default = function(title,content){
	if(title!=null)
		$('#modal_title').html(title);

	$('#modal_content').html(content);
	$('#modal_default').modal('show');
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
	
	var dates = null;
	if(date.indexOf('-')!=0){
		dates = moment(date,'YYYY-MM-DD').format('DD-MM-YYYY').split("-");
		//moment(date,'YYYY-MM-DD').format('DD/MM/YYYY')
	}
	else if(date.indexOf('/')!=0){
		dates = moment(date,'YYYY/MM/DD').format('DD-MM-YYYY').split("-")//date.split("/");
	}
	
	//var dates = date.split("/");
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

utility.datetime_format = function(date,lang){

	var result = "";
	var time = "";

	result = utility.date_format(date,lang);
	
	if(date.indexOf('-')!=0){
		time = moment(date,'YYYY-MM-DD HH:mm:ss').format('HH:mm:ss');
		//moment(date,'YYYY-MM-DD').format('DD/MM/YYYY')
	}
	else if(date.indexOf('/')!=0){
		time = moment(date,'YYYY/MM/DD HH:mm:ss').format('HH:mm:ss');//date.split("/");
	}

	result += " " + time;

	return result;
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

utility.load_prefix = function(obj){

	$.getJSON('js/prefix_mobile.json',function(resp){
		
		$.each(resp,function(name,prefix){
			
			
			var lang = (navigator.language) ? navigator.language : navigator.userLanguage
			var list = $('#'+obj);
			
			list.append($('<option>', { 
        		value : prefix,
        		text : "( "+name+" ) "+prefix
    		}));
			
		});
		
	});
}
//require moment.js  !!!
utility.print_log = function(msg){
	
	if(window.console){
		window.console.log(moment(new Date()).format("YYYY-MM-DD HH:mm:ss")+ " > " + JSON.stringify(msg));
	}
}

utility.print_error = function(msg){
	if(window.console){
		window.console.error( moment(new Date()).format("YYYY-MM-DD HH:mm:ss")+ " > " + JSON.stringify(msg));
	}
}

utility.print_warn = function(msg){
	if(window.console){
		window.console.warn(moment(new Date()).format("YYYY-MM-DD HH:mm:ss")+ " > " + msg);
	}
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

function sortResults(obj,prop, asc) {
    var result = obj.sort(function(a, b) {
        if (asc) {
            return (a[prop] > b[prop]) ? 1 : ((a[prop] < b[prop]) ? -1 : 0);
        } else {
            return (b[prop] > a[prop]) ? 1 : ((b[prop] < a[prop]) ? -1 : 0);
        }
    });

    return result;
    //showResults();
}



