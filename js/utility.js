$(document).ready(function(){
	
	utility.initial();
	$(".dropdown-menu .sub-menu").hide();
});

function viewlang(resp)
{
		switch(resp.result.lang)
		 {
			 case 'th':
			  $('#lang').html("Thai <span class='caret'></span>")
			 break;
			 case 'en':
			 $('#lang').html("English <span class='caret'></span>")
			 break;
		 }
}

function setfinddealer(){
  $('#find_dealer').click(function(){
    var name = $('#find_text').val();
     window.location.href = "dealer.html?name="+name+"&_=" + new Date().getMilliseconds();
  });
}

var utility = function(){};

utility.initial = function(){
	
	
	$('#view-navbar').load('navbar.html',{'_':new Date().getHours()},function(){

		setfinddealer();
		utility.loadmenu();
		var url = 'services/lang.php';
		var args = {'_':new Date().getMilliseconds()};
		utility.service(url,'GET',args ,viewlang);
		
	});

	$('#view-footer').load('navfooter.html',{'_':new Date().getHours()},function(){
		utility.loadbuttommenu();
	});
	
	
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

utility.loadmenu = function(){
 var endpoint ="services/menu.php";
	var args = {'_':new Date().getHours()};
	this.service(endpoint,'get',args ,getmenubar ,null);
}

utility.loadbuttommenu = function(){

 var endpoint = "services/attributes.php";
	var args = {'_':new Date().getHours(),'type':'menu'};
  this.service(endpoint,'get',args ,genbutton ,null);
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

utility.setpage = function(page,callback){
	
  var endpoint = "services/attributes.php";
  var args = {'_':new Date().getHours(),'type':page};
  utility.service(endpoint,'GET',args, bindpage,callback);
  
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

function centerModal() {
    $(this).css('display', 'block');
    var $dialog = $(this).find(".modal-dialog");

	//var imgwidth = $(this).find('.modal-body img').width();
	//$dialog.css({width:imgwidth+35});

    var offset = ($(window).height() - $dialog.height()) / 2;
    // Center modal vertically in window
    $dialog.css("margin-top", offset);
}

function bindpage(response)
{
	if(response!==undefined)
	{
		$.each(response.result,function(i,val){
			//console.log(val);
			$("span[id='"+val.name+"']").html(val.value);
		});
	}
	else { console.warn('attribute not found.'); }
}

//-----------------load globle menu-----------------

function loadchildmenu(id){
	var menu = $('#'+id);
	if(menu.val() ==1) return false;

	$.ajax({

		url:'services/menu.php?_=' + new Date().getMilliseconds(),
		type:'GET',
		data: {"id":id} ,
		dataType:'json' ,
		success:function(data){
			menu.val(1);
			getchildmenu(id,data);
		},
		error:function(xhr,status,err){
			menu.val(0);
			alert("generate child menu error :"+xhr.responseText);
		}

	});

}

function getmenubar(data){
		var menu = $('#menubar');
		menu.html("");

    var parent = data.result.filter(function(item){return item.parent=="0"});
    var child = data.result.filter(function(item){return item.parent!="0"});

		$.each(parent,function(id,val){

			var item = "";
		  var sub = child.filter(function(item){return item.parent==val.id });

		  if(sub.length==0)
		  {
			item = "<li id='"+val.id+"' > <a href='"+val.link+"'>"+val.name+"</a></li>";
		  }
      else {
			item = "<li id='"+val.id+"' class='dropdown' >"; 
			item += "<a  class='dropdown-toggle' data-toggle='dropdown'  href='#'  aria-haspopup='false' >"+val.name+"</a>";
			
			  item += "<ul class='dropdown-menu' >";
			  $.each(sub,function(subid,subval){
				  item += "<li><a href='"+subval.link+"'>"+subval.name+"</a></li>";
			  });
          item += "</ul>";

				item +="</li> ";

      }

      //console.warn(item);
			menu.append(item);
		});

    //var inter = data.result.filter(function(item){ return item.local=="0"; });

}

function getchildmenu(id,data){

	var menu = $('#'+id);

	var item = "";
	item = "<ul class='dropdown-menu'>";
	$.each(data.result,function(idx,val){
			item += "<li><a href='"+val.link+"'>"+val.name+"</a></li>";
	});
	item += "</ul>";
	menu.append(item);
}

function genbutton(data){
	$.each(data.result,function(idx,val){
			$("div[id='"+val.name+ "'] label").text(val.title);
			$("div[id='"+val.name+ "']").append(val.item);
	});
}



//-----------------load globle menu-----------------
