var rooms = {};

rooms.filter_room = function(startdate,enddate){

	var start_date = "";
	var end_date = "";
	
	var endpoint = "services/rooms.php";
	var method="get";
	var args = {
		"_":new Date().getMilliseconds()
		,"service":"filter"
		,"startdate":startdate
		,"enddate":enddate
	};
	$('#list_room').html("<img src='images/loader.gif' /><br/>Loading....");
	utility.service(endpoint,method,args,view_list_room);


};

rooms.call_view_options = function(callback){
	
	var endpoint = "services/rooms.php";
	var method="get";
	var args = {
		"_":new Date().getMilliseconds()
		,"service":"options"
	};
	utility.service(endpoint,method,args,set_view_options,callback);

}

function set_view_options(resp){
	
	var view = $('#list_options');
	
	view.html('');
	
	if(resp.data!=null){
		
		var items = "";
		//console.warn(resp.data);
		$.each(resp.data,function(i,val){
			items+="<div class='row' >";
			items+="<div class='col-md-3'><img src='"+val.image+"' class='img-responsive' /></div>";
			items+="<div class='col-md-7'>";
			items+="<p><h4>"+val.title+"</h4></p>";
			items+= "<p><a href='javascript:void(0);' data-toggle='collapse' data-target='#detail_"+val.id+"'>"+pages.message.detail+" </a></p>";
			items+= "<div id='detail_"+val.id+"' class='collapse' ><i>"+val.detail+"</i></div>";
			items+= val.remark+"</div>";
			items+="<div class='col-md-2 text-right'>"+pages.message.choice +" <input type='checkbox' name='option_"+val.id+"' id='option_"+val.id+"' title='"+val.title+"' key='"+val.id+"' price='"+val.price+"' value='"+val.id+"' onchange=room_option_change('"+val.id+"') /></div>";
			items+="</div><hr/>";
		});
		
		view.append(items);
		console.log("load view option complete.");
	}
	
}

	
function room_option_change(type){
	console.log('option type = ' + type);
	var item = $('#option_'+type);
	var val = item.is(':checked');
	var data = {};
	
	data.title = item.attr('title');
	data.price = item.attr('price');
	data.key = item.attr('key');
	
	
	if(type==1){//diner
		var attr = calculate_diner();
		data.price = attr.price;
		data.desc = attr.desc;
	}
	else if(type==2){ //transfer
		data.desc = "&nbsp;";
	}
	
	if(val==true){
		reserve.add_option(data);
	}
	else{
		reserve.del_option(data.key,data.price);
	}
	
}

function calculate_diner(){
	var net = 0;
	try{

		var adult = 525;
		var child = 262;
		var adult_unit = parseFloat($('#adult_amount').val());
		var child_unit = parseFloat($('#child_2_amount').val()); //5-11
		var baby_unit =  parseFloat($('#child_amount').val()); //0-4

		var adult_total = (adult*adult_unit) ;
		var child_total = (child*child_unit);
		net = adult_total + child_total;

	}catch(err){
		console.error(err);
	}
	//Thai Set Diner
	var desc = "<i><small>";
	desc+= "<div class=col-md-8 >"+pages.message.adults+" = "+adult+" x "+adult_unit+ " </div><div classcol-md-4> "+adult_total  + "</div>";
	desc+= "<div class=col-md-8 >"+pages.message.children_2+" = "+child+" x "+child_unit+ " </div><div classcol-md-4> "+child_total  +	"</div>";
	desc+= "<div class=col-md-8 >"+pages.message.children+" = 0 x "+baby_unit+ "</div><div classcol-md-4> 0</div>";
	desc+= "</small></i>";

	var result = JSON.parse('{"key":"1","title":"Thai Set Diner","desc":"'+desc+'","price":"'+net+'","adults":"'+adult+'","children":"'+baby_unit+'","children_1":"'+child_total+'"}');

	return result;
}

function view_list_room(resp){
	var rooms = "";
	console.log("view list room");
	if(resp.data!=null){
		
		console.log(resp.data);
		
		$('#list_room').html("");
		var templete_master = "";
		templete_master = utility.get_templete("templete_room.html");
		$.each(resp.data.rooms,function(index,room){
			
			//var room =  item;//resp.data.rooms[index];
			var templete = templete_master;
			templete = templete.replace("{room_name}",room.room_name);
			templete = templete.replace("{link_pop_detail}",set_room_detail(room.room_id));
			templete = templete.replace("{message.detail}",pages.message.btn_room_detail);
			templete = templete.replace("{note_bed}",pages.message.note_bed);
			templete = templete.replace("{bed_list}",set_bed_list(room.beds,room.room_id));
			templete = templete.replace("{gallery_list}",set_gallery_list(room.gallerys,room.room_id));
			templete = templete.replace("{package_list}",set_package_list(room.packages,room.room_name,room.room_id));
			
			$('#list_room').append(templete);
			
			//apply gallery
			$('#gallery_'+room.room_id).unitegallery({
				theme_panel_position: "bottom"
				,gallery_height:600
				,gallery_theme: "grid"
				,slider_scale_mode: "fit"
				,thumb_fixed_size:false
				,thumb_loader_type:"light"
				,grid_num_cols:1
				,grid_num_rows:1
				,gridpanel_grid_align: "top"
				,theme_hide_panel_under_width: 1380
			});
		});
		
		

	}else{
		$('#list_room').html("<div class='well'><span class='glyphicon glyphicon-exclamation-sign'></span> Sorry !!! Room Unavailable.</div>");
	}
}

function set_room_detail(room_id){
	var result = "";
	
	result = 'onclick=rooms.modal_room_detail("'+room_id+'") ';
	
	return result;
}

rooms.modal_room_detail =function (id){
	

	if(id==='1'){
		call_room_detail_modal("Superior","superior","1");
	}
	else if(id==='2'){
		call_room_detail_modal("Deluxe","deluxe","2");
	}
	else if(id==='3'){
		call_room_detail_modal("Villa","villa","3");
	}
	else if(id==='4'){
		call_room_detail_modal("Villa @ Sea","villa_sea","4");
	}
	else if(id==='5'){
		call_room_detail_modal("Suite","suite","5");
	}
	else if(id==='6'){
		call_room_detail_modal("Suite @ Sea","suite_sea","6");
	}
	
}

function call_room_detail_modal(title,room_name,room_type){

	$('#modaltitle').text(title);

	//load page detail
	$('#modalcontent').load('view_detail_room.html',function(){
		//after load page success.

		//load data detail
		var lang = utility.readCookie('lang');
		var room = room_name+'.json';
		if(lang==null) lang="en";
		$.getJSON('js/rooms/'+lang+'/'+room,function(resp){
			$.each(resp,function(name,val){
				$('#'+name).html(val);
			});
		});

		var endpoint = "services/rooms.php";
		var method ="get";
		var data ={"service":"gallery","room_type":room_type};
		$('#gallery').html("");
		utility.service(endpoint,method,data,function(resp){
			console.debug(resp);
			//inital image
			if(resp.data!=undefined){
				var item = "";
				$.each(resp.data,function(i,val){
					
					item = "<img src='"+val.image+"' data-image='"+val.image+"' onerror=this.src='images/common/unavaliable.jpg' data-description=''  />";
					$('#gallery').append(item);

				});
			}

		},function(){

			//setting gallery
			$('#gallery').unitegallery({

				theme_panel_position: "bottom"
				,gallery_height:600
				,gallery_theme: "grid"
				,slider_scale_mode: "fit"
				,thumb_fixed_size:false
				,thumb_loader_type:"light"
				,grid_num_cols:1
				,grid_num_rows:1
				,gridpanel_grid_align: "top"
			});

		});

	});

	$('#view_condition').css('display','none');
	$('#modaldialog').modal();
}

function set_gallery_list(items,room_id){
	
	var obj_gallery = "gallery_"+room_id;
	//console.log(obj_gallery);
	var result = "<div id='"+obj_gallery+"' > ";
	
	$.each(items,function(index){
		var gallery = items[index];
		result +=  "<img src='"+gallery.image+"' data-image='"+gallery.image+"' onerror=this.src='images/common/unavaliable.jpg' data-description=''  />";
	});
	result += "</div>";
	
	return result;
}

function set_bed_list(items,room_id){
	
	var result = "<select class='form-control' id='type_bed_"+room_id+"' name='type_bed_"+room_id+"' >";
	$.each(items,function(index){
		var bed = items[index];
		result += "<option value='"+bed.id+"'>"+bed.title+"</option>";
	});

	result += "</select>";
	return result;
}

function set_package_list(items,room_name,room_id){
	var result = "";
	//console.log(items);
	if(items!=null){
		
		$.each(items,function(index,package){
			
			//console.log(package);
			
			if(package.id==null) return true; //skip item when item is null
			
			//var package = items[index];
			var price = package.price;
			var person = parseFloat($('#adult_amount').val()) +  parseFloat($('#child_2_amount').val()) ;
			var night = $('#night_unit').val();
			var extra_price_adults = package.extra_price_adults;
			var extra_price_children = package.extra_price_children;
			var limit_people = parseFloat(package.extra_bed)+ parseFloat(package.max_person);

			result+= "<span href='#' class='list-group-item'>";			
			result+= "<h4 class='list-group-item-heading'><a href='javascript:void(0);' data-toggle='collapse' data-target='#package_"+package.id+"' >"+package.title+"</a>";		
			result+= "<span class='pull-right'>฿ "+package.price+" / "+pages.message.night+"</span></h4>";
			result+= "<small class='list-group-item-text'>";
			
			//check package internet rate.
			//console.log("check package internet = "+ package.title + " is " +package.title.toLowerCase().indexOf("internet"));
			if(package.title.toLowerCase().indexOf("internet")!=-1){
				console.log("found internet rate. >" + package.title);
			}
			
			if(package.food_service==1){
				result+= "<div class='col-sm-3' onclick='reserve.modal_breakfast();' style='cursor:pointer;'><span class='glyphicon glyphicon-ok'></span> "+pages.message.breakfast +"</div>";
			}
			else{
				result+= "<div class='col-sm-3' ><span class='glyphicon glyphicon-remove'></span> "+pages.message.no_breakfast +"</div>";
			}
			
			if(package.cancel_room==1){
				result+= "<div class='col-sm-3' onclick='reserve.modal_change()'; style='cursor:pointer;'><span class='glyphicon glyphicon-ok'></span> "+pages.message.change_reserve +"</div>";
			}
			else{
				result+= "<div class='col-sm-3' style='cursor:pointer;'><span class='glyphicon glyphicon-remove'></span> "+pages.message.no_change_reserve +"</div>";
			}
			
			if(package.payment_online==1){
				//onclick='reserve.modal_internet()';
				result+= "<div class='col-sm-3'  style='cursor:pointer;'><span class='glyphicon glyphicon-credit-card'></span> "+pages.message.internet +"</div>";
			}
			else{
				result+= "<div class='col-sm-3' style='cursor:pointer;'><span class='glyphicon glyphicon-remove'></span> "+pages.message.no_internet +"</div>";
			}
			
			result+= "<button onclick=add_room('"+package.id+"','"+encodeURIComponent(room_name)+"','"+encodeURIComponent(package.title)+"','"+package.price+"','"+room_id+"',"+limit_people+","+extra_price_adults+","+extra_price_children+"); type='button' class='btn btn-default brown btn-sm col-sm-3'>"+pages.message.choice +"</button>&nbsp;";
			result+= "</small></span>";
			result+= "<div id='package_"+package.id+"' class='collapse' ><span class='list-group-item'>"+package.detail+"";
			result+= "<a href='javascript:void(0)' class='pull-right' onclick='pop_pack_condition("+package.id+")'>"+pages.message.condition_package_btn+"</a><br></span>";
			result+= "</div></span>";

			
		});
	}
	else{  //show room not available.
		result="<span href='#' class='list-group-item text-center'> <h4>"+pages.message.room_unavailabel+".</h4>";
		result+="</span>";
	}
	
	return result;
}

function pop_pack_condition(id){

	var endpoint = "services/rooms.php";
	var method ="get";
	var data ={"service":"package","id":id,"lang":pages.lang()};
	utility.service(endpoint,method,data,function(resp){
		utility.modal_default(pages.message.condition_package_btn,resp.data.conditions);
	});

}
