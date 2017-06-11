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
	utility.service(endpoint,method,args,view_list_room);


};

rooms.call_view_options = function(){
	
	var endpoint = "services/rooms.php";
	var method="get";
	var args = {
		"_":new Date().getMilliseconds()
		,"service":"options"
	};
	utility.service(endpoint,method,args,set_view_options);

}

function set_view_options(resp){
	
	var view = $('#list_options');
	
	view.html('');
	
	if(resp.data!=null){
		
		var items = "";
		console.warn(resp.data);
		$.each(resp.data,function(i,val){
			items+="<div class='row' >";
			items+="<div class='col-md-3'><img src='"+val.image+"' class='img-responsive' /></div>";
			items+="<div class='col-md-7'>";
			items+="<p><h4>"+val.title+"</h4></p>";
			items+= "<p><a href='javascript:void(0);' data-toggle='collapse' data-target='#detail_"+val.id+"'>รายละเอียด ></a></p>";
			items+= "<div id='detail_"+val.id+"' class='collapse' ><i>"+val.detail+"</i></div>";
			items+= val.remark+"</div>";
			items+="<div class='col-md-2 text-right'><span>เลือก</span> <input type='checkbox' name='option_"+val.id+"' id='option_"+val.id+"' title='"+val.title+"' key='"+val.id+"' price='"+val.price+"' value='"+val.id+"' onchange=room_option_change('"+val.id+"') /></div>";
			items+="</div><hr/>";
		});
		
		view.append(items);
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
	var desc = '<i><small>';
	desc+= '<div class=col-md-8 >adults = '+adult+' x '+adult_unit+ ' </div><div classcol-md-4> '+adult_total  + '</div>';
	desc+= '<div class=col-md-8 >children age 5-11 = '+child+' x '+child_unit+ ' </div><div classcol-md-4> '+child_total  +'</div>';
	desc+= '<div class=col-md-8 >children age 0-4 = 0 x '+baby_unit+ ' </div><div classcol-md-4> 0</div>';
	desc+= '</small></i>';

	var result = JSON.parse('{"key":"1","title":"Thai Set Diner","desc":"'+desc+'","price":"'+net+'","adults":"'+adult+'","children":"'+baby_unit+'","children_1":"'+child_total+'"}');

	return result;
}

function view_list_room(resp){
	var rooms = "";
	$('#list_room').html("");
	if(resp.data!=null){
		
		//console.log(resp.data);

		var templete_master = "";
		templete_master = utility.get_templete("templete_room.html");
		$.each(resp.data,function(index){
			//console.log(resp.data[index]);
			var room = resp.data[index];
			var templete = templete_master;
			templete = templete.replace("{room_name}",room.title);
			templete = templete.replace("{link_pop_detail}",set_room_detail(room.id));
			templete = templete.replace("{bed_list}",set_bed_list(room.beds,room.id));
			templete = templete.replace("{gallery_list}",set_gallery_list(room.gallerys,room.id));
			templete = templete.replace("{package_list}",set_package_list(room.packages,room.title,room.id));
			
			$('#list_room').append(templete);
			
			//apply gallery
			$('#gallery_'+room.id).unitegallery({
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
		$('#list_room').html("Sorry !!! Room Unavailable.");
	}
}

function set_room_detail(room_id){
	var result = "";
	
	result = 'onclick=rooms.modal_room_detail("'+room_id+'") ';
	
	return result;
}

rooms.modal_room_detail =function (id){
	
	if(id==='1'){
		call_room_detail_modal("Deluxe","deluxe","1");
	}
	else if(id==='2'){
		call_room_detail_modal("Superior","superior","2");
	}
	else if(id==='3'){
		call_room_detail_modal("Villa","villa","3");
	}
	else if(id==='4'){
		call_room_detail_modal("Villa @ Sea","villa_sea","4");
	}
	else if(id==='5'){
		call_room_detail_modal("Suite","Suite","5");
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
	console.warn(items);
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

	if(items!=null){
		
		$.each(items,function(index){
			var package = items[index];

			result+= "<span href='#' class='list-group-item'>";			
			result+= "<h4 class='list-group-item-heading'>"+package.title+"<span class='pull-right'>฿ "+package.price+"</span></h4>";		
			result+= "<small class='list-group-item-text'>";
			
			if(package.food_service==1){
				result+= "<div class='col-sm-3' onclick='reserve.modal_breakfast();' style='cursor:pointer;'><span class='glyphicon glyphicon-ok'></span> รวมอาหารเช้า</div>";
			}
			else{
				result+= "<div class='col-sm-3' ><span class='glyphicon glyphicon-remove'></span> ไม่รวมอาหารเช้า</div>";
			}
			
			if(package.cancel_room==1){
				result+= "<div class='col-sm-3' onclick='reserve.modal_change()'; style='cursor:pointer;'><span class='glyphicon glyphicon-ok'></span> สามารถเปลี่ยนแปลงหรือยกเลิกได้</div>";
			}
			else{
				result+= "<div class='col-sm-3' style='cursor:pointer;'><span class='glyphicon glyphicon-remove'></span> ไม่สามารถเปลี่ยนแปลงหรือยกเลิกได้</div>";
			}
			
			if(package.payment_online==1){
				result+= "<div class='col-sm-3' onclick='reserve.modal_internet()'; style='cursor:pointer;'><span class='glyphicon glyphicon-credit-card'></span> ชำระเงินทางอินเตอร์เน็ต</div>";
			}
			else{
				result+= "<div class='col-sm-3' style='cursor:pointer;'><span class='glyphicon glyphicon-remove'></span> ไม่สามารถชำระเงินทางอินเตอร์เน็ต</div>";
			}
			
			result+= "<button onclick=add_room('"+package.id+"','"+encodeURIComponent(room_name)+"','"+encodeURIComponent(package.title)+"','"+package.price+"','"+room_id+"'); type='button' class='btn btn-default brown btn-sm col-sm-3'>เลือก</button>&nbsp;";
			result+= "</small></span>";
			
		});
	}
	return result;
}
