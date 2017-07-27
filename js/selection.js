var reserve = {};
reserve.rooms = [];
//reserve.options =  [];
//reserve.summary = [];
reserve.customer = [];
reserve.payment = [];

function add_room(id,name,type,price,room_id){
	
	var list = $('#list_reserve');
	var item = "";
	var key = moment().format("YYYYMMDDHHMMSS");
	var bed = $('#type_bed_'+room_id).val();
	var adults = $('#adult_amount').val();
	var older_children = $('#child_2_amount').val();
	var young_children = $('#child_amount').val();
	var person = parseFloat(adults) + parseFloat(older_children);
	var night = $("#night_unit").val();
	name = decodeURIComponent(name);
	type = decodeURIComponent(type);
	//console.warn("add room >  " + key +"|"+ id + "|" + name + "|"+type+"|"+price+"|"+bed+"|"+adults+"|"+older_children+"|"+young_children);
	var room = { "key" : key ,"package":id, "room":name , "type" : type , "price" : price 
	,"bed":bed,"adults":adults,"older_children":older_children,"young_children":young_children ,"person":person
	}; 	

	reserve.rooms.push(room); 

	price = price * parseFloat(night);

	var money_pattern = /(\d)(?=(\d\d\d)+(?!\d))/g;   //formoney
	var money = parseFloat(price).toFixed(2).replace(money_pattern,"$1,");
	
	item += "<span id='"+key+"'  href='#' class='list-group-item'>";
	item += "<h4 class='list-group-item-heading'>"+ name +" <span class='pull-right'>฿ "+money+"</span></h4>";
	item += "<p><span class='pull-right'>"+person+" "+ pages.message.person+"</span></p><br/>";
	item += "<p class='list-group-item-text'>" + type;
	item += "<span class='pull-right' style='cursor:pointer;' onclick=del_room("+key+")>"+pages.message.remove+"</span></p>";
	item += "</span>";
	list.append(item);
	reserve.calculate();
}

function del_room(key){
	
	$('#'+key).remove();
	reserve.rooms = jQuery.grep(reserve.rooms,function(item,index){ return item.key != key });
	reserve.calculate();
}

reserve.reset_room = function(){

	if(reserve.rooms!=undefined){
		$.each(reserve.rooms,function(i,val){
			del_room(val.key);
		});
	}

	//reserve.rooms = [];
}

reserve.get_info = function(){
	
	var endpoint = "services/info.php";
	var method = "get";
	var args = {"_":new Date().getMilliseconds()};
	
	//clear object
	$('#data_reserve').val('');

	utility.service(endpoint,method,args,function(result){
		
		console.log(result);
		var money_pattern = /(\d)(?=(\d\d\d)+(?!\d))/g;   //format money
		
		var info = result.info;
		reserve.info = info;
		var items = result.data;
		if(info==null) { 
			alert(pages.message.reserve_not_found);//'Sorry!! Not Found Information Reserve.'
			window.location="quick_reservation.html";
		}
		
		$('#data_reserve').val(JSON.stringify(result.data.reserve));
		//console.warn($('#data_reserve').val());
		
		//set rooms info;
		if(info!==null){
			$('#checkpoint_date').val(info.start_date);
			$('#travel_date').val(info.end_date);
			$('#night_unit').val(info.night);
			$('#adult_amount').val(info.adults);
			$('#child_amount').val(info.children);
			$('#child_2_amount').val(info.children_2);
			$('#promo_code').val(info.code);
			$('#total_night').html(info.night);
			$('#comment').html(info.comment);
		}
		
		if(items.reserve !==null && items.reserve.summary !==null){
			//assign summery
			reserve.summary = items.reserve.summary;
			var total =parseFloat(reserve.summary.total).toFixed(2).replace(money_pattern,"$1,");
			$('#total_price').html("฿ " + total);
		}
		//set item room
		if(items.reserve !==null && items.reserve.rooms !== null ){
			//assign rooms to object
			reserve.rooms = items.reserve.rooms;
			$('#total_room').html(reserve.rooms.length); 
			$.each(reserve.rooms,function(i,val){
				
				var money = parseFloat(val.price).toFixed(2).replace(money_pattern,"$1,");
				var item = "<span id='"+val.key+"' href='#' class='list-group-item'>";
				item += "<h4 class='list-group-item-heading'>"+pages.message.room+" "+(i+1)+"<span class='pull-right'>฿ "+money+"</span></h4>";
				item += "<h4 class='list-group-item-text '>";
				item += val.room +" <small class='pull-right' style='cursor:pointer;' onclick=del_room("+val.key+") >"+pages.message.remove+"</small> ";
				item += val.type + "</h4></span>";
				$('#list_reserve').append(item);
				
			});
		}
		//set item options
		if(items.reserve!=null && items.reserve.options!=null){
			
			$.each(items.reserve.options,function(i,val){

				var item_option = 'option_'+val.key;
				$('input[name="'+item_option+'"]').prop('checked',true);
				reserve.add_option(val);
				
			});
			
		}
		
		console.log("load information complete.");
	});
}

reserve.get_roomofweek = function(){
	var endpoint = "services/daysofweek.php";
	var method="get";
	//var args = {"_":new Date().getMilliseconds()};
	var args = {"_":new Date().getMilliseconds()};
	
	utility.service(endpoint,method,args,view_dayofweek);
}

reserve.calculate = function(){

	var money_pattern = /(\d)(?=(\d\d\d)+(?!\d))/g;  
	var total = 0.0;
	if(reserve.summary == undefined) {
		reserve.summary = {"room":0,"option":0,"total":0};
	}
	//room
	if(reserve.rooms!=undefined){
		$.each(reserve.rooms,function(i,val){
			total += parseFloat(val.price);
		});
	//calculate room * night
		total = total * parseFloat(reserve.info.night);
		reserve.summary.room=total;
	}
	

	
	var sum_option = 0;
	if(reserve.options!=undefined){
		$.each(reserve.options,function(i,val){
			sum_option += parseFloat(val.price);
		});
		reserve.summary.option = sum_option;
		total += sum_option;
	}
	
	reserve.summary.total = total;
	var money = parseFloat(total).toFixed(2).replace(money_pattern,"$1,");
	console.log("total_amount :" + money);
	$('#total_price').html("฿ " + money);
	$('#total_room').html(reserve.rooms.length); 
	
}

reserve.add_option = function(val){
	
	var money_pattern = /(\d)(?=(\d\d\d)+(?!\d))/g;   //format money
	var money = parseFloat(val.price).toFixed(2).replace(money_pattern,"$1,");
	
	var item = "<span id='"+val.key+"' href='#' class='list-group-item'>";
			item += "<h4 class='list-group-item-heading'>"+val.title+"<span class='pull-right'>฿ "+money+"</span></h4>";
			item += "<h4 class='list-group-item-text'>";
			item += "<span class='text-right'>"+val.desc+"</span><small/>";//<a href='javascript:void(0)' onclick=reserve.del_option('"+val.key+"','" + val.price+"');  >นำออก</a></small> 
			item += "</h4></span>";
			$('#list_reserve').append(item);
	
	if(reserve.options === undefined){
		reserve.options = [];
	}

	reserve.options.push(val);
	reserve.calculate();

}

reserve.del_option = function(key,money){

	$('#'+key).remove();
	
	reserve.options = jQuery.grep(reserve.options,function(item,index){ return item.key != key });
	reserve.calculate();
}

reserve.modal = function(title,detail,image){
	
	var view = '<div class="media">';
	
	if(image!='')
		view += '<div class="media-left"><a href="#"><img class="media-object" src="'+image+'" alt="..."></a></div>';
	
	view += '<div class="media-body">'+detail+'</div>';
	view += '</div>';
	
	$('#modaltitle').text(title);
	$('#modalcontent').html(view);
	$('#modaldialog').modal();
	
}

reserve.modal_nochange = function(){
	var title = pages.message.dialog_nochange_title; //"ไม่สามารถยกเลิกได้และเปลี่ยนแปลงได้";
	var detail = pages.message.dialog_nochange_detail;
	
	var expireDate = new Date();
	var numberOfDaysToAdd = 14;
	var lang = 'th';
	expireDate.setDate(expireDate.getDate() + numberOfDaysToAdd); 
	expireDate = moment(expireDate).format("DD/MM/YYYY");
	expireDate = utility.date_format(expireDate,lang);
	var condition = utility.get_templete("templete_nochange_detail.html");
	condition = condition.replace("{reserve_expire}",expireDate);
	//detail = detail.replace("{reserve_expire}",expireDate);
	var view = "<div class='media'>";
	view += "<div class='media-body'>"+detail+"<br/>";
	//view += '<button type="button" class="btn btn-warning" data-toggle="collapse" data-target="#modal_condition">เงื่อนไข</button></div>';
	view += "</div>";
	
	
	$('#modaltitle').text(title);
	$('#modalcontent').html(view);
	$('#view_condition').css('display','block');
	$('#modal_condition').collapse('hide');
	$('#modal_condition').html(condition);
	$('#modaldialog').modal();
}

reserve.modal_change = function(){
	
	var title = pages.message.dialog_change_title ;
	var detail = pages.message.dialog_change_detail; 
	//'ยกเลิกได้และเปลี่ยนแปลงได้';
	var expireDate = new Date();
	var numberOfDaysToAdd = 14;
	var lang = 'th';
	expireDate.setDate(expireDate.getDate() + numberOfDaysToAdd); 
	expireDate = moment(expireDate).format("DD/MM/YYYY");
	expireDate = utility.date_format(expireDate,lang);
	detail = detail.replace("{reserve_expire}",expireDate);
	
	var view = "<div class='media'>";
	view += "<div class='media-body'>"+detail+"</div>";
	view += "</div>";

	var condition = utility.get_templete("templete_change_detail.html");
	condition = condition.replace("{reserve_expire}",expireDate);
	
	$('#modaltitle').text(title);
	$('#modalcontent').html(view);
	$('#view_condition').css('display','block');
	$('#modal_condition').collapse('hide');
	$('#modal_condition').html(condition);
	$('#modaldialog').modal();
}

reserve.modal_internet = function(){
	
	var title =   pages.message.dialog_internet_title;//'ชำระเงินจองทางอินเทอร์เน็ต';
	var detail =  pages.message.dialog_internet_detail;
	var view = "<div class='media'>";
	view += "<div class='media-body'>"+detail+"</div>";
	view += "</div>";
	
	var expireDate = new Date();
	var numberOfDaysToAdd = 14;
	var lang = 'th';
	expireDate.setDate(expireDate.getDate() + numberOfDaysToAdd); 
	expireDate = moment(expireDate).format("DD/MM/YYYY");
	expireDate = utility.date_format(expireDate,lang);
	var condition = utility.get_templete("templete_internet_detail.html");
	condition = condition.replace("{reserve_expire}",expireDate);
	
	$('#modaltitle').text(title);
	$('#modalcontent').html(view);
	$('#view_condition').css('display','block');
	$('#modal_condition').collapse('hide');
	$('#modal_condition').html(condition);
	$('#modaldialog').modal();
}

reserve.modal_breakfast = function(){
	
	var title = pages.message_dialog_breakfast_title;//'อาหารเช้า';
	var detail = pages.message_dialog_breakfast_detail;
	var view = '<div class="media">';
	
	view += "<div class='media-body'>"+detail+"</div>";
	view += '</div>';
	
	var expireDate = new Date();
	var numberOfDaysToAdd = 14;
	var lang = 'th';
	expireDate.setDate(expireDate.getDate() + numberOfDaysToAdd); 
	expireDate = moment(expireDate).format("DD/MM/YYYY");
	expireDate = utility.date_format(expireDate,lang);
	
	var condition = utility.get_templete("templete_breakfast_detail.html");
	condition = condition.replace("{reserve_expire}",expireDate);
	$('#modaltitle').text(title);
	$('#modalcontent').html(view);
	$('#view_condition').css('display','block');
	$('#modal_condition').collapse('hide');
	$('#modal_condition').html(condition);
	$('#modaldialog').modal();
	
}

reserve.modal_view_deluxe = function(){
	
	call_room_detail_modal("Deluxe","deluxe","1");
	
}

reserve.modal_view_superior = function(){
	call_room_detail_modal("Superior","superior","2");
}

reserve.modal_room_detail =function (id){
	
	if(id==='1'){
		call_room_detail_modal("Deluxe","deluxe","1");
	}
	else if(id==='2'){
		call_room_detail_modal("Superior","superior","2");
	}
	else if(id==='2'){
		call_room_detail_modal("Villa","villa","3");
	}
	else if(id==='4'){
		call_room_detail_modal("Villa @ Sea","Villa@Sea","4");
	}
	else if(id==='5'){
		call_room_detail_modal("Suite","Suite","5");
	}
	else if(id==='6'){
		call_room_detail_modal("Suite @ Sea","Suite@Sea","6");
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
			//console.debug(resp);
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

//remove
function add_default_condition(){
	var condition= "";
	
	condition += "<p>Terms & Conditions</p>";
	condition += "<p>* Full payment in advance required. Yur credit card will be charged at the time of reservation.<br/>";
	condition += "* Rules & Restrictions Payments for bookings at Advance Purchase/Non-Refundable rates are not refundable and bookings may not be modified.<br/>";
	condition += "* The above rate are not include breakfast.<br/>";
	condition += "* If you depart early or you cancel or fail to honor this reservation for any reason, you will not receive any credit or refund. <br/>";
	condition += "* Extensions will require a new reservation for the additional date(s), subject to availability and prevailing rates, and this rate shall not apply<br/>";
	condition += "* This rate is not combinable with any other offers and promotions and is not available to groups.<br/>";
	condition += "* Rates are subject to availability.<br/>";
	condition += "* Rates are quoted in Thai Baht(THB)<br/>";
	condition += "* Rates quoted are subjected to 7% government tax and 10% service charge.<br/>";
	condition += "* Rate is subject to change without notice.</p>";
	condition += "<p>* Check in time is from 14:00 hours & Check out until 12:00 noon.<br/>";
	condition += "* Please note that children age 12 and older are charged the adult rate. Please include them in the number entered in the No. of Aduts box.<br/>";
	condition += "* Children below 12 years old sharing the existing bed with parents stay free. Breakfast for child is charged THB 234 per child per day subject to Tax and Servce Charge.<br/>";
	condition += "* Baby cot is free and advance request must be made.<br/>";
	condition += "* Extra bed is charge at THB 1020 per bed per night subject to tax and service charge and full daily breakfast.<br/>";
	condition += "* Exchange rates for information only.<br/>";
	condition += "* Please do not hesitate to contact us at the following e-amil address: rsvn@haven-huahin.com, we are at your disposal for any further information you need.<br/>";
	
	return condition;
}

function view_dayofweek(resp){
	//console.log(resp);

	var view = $('#view_dayofweek');
	view.html('');
	//one day
	var item = "";
	if(resp.data!=undefined){
		$.each(resp.data,function(i,val){

			item += "<div class='col-md-2 thumbnail'>";
			item += "<div class='text-center'> "+val.format_date+"</div><br>";
			item += "<div > Deluxe"+"<span class='pull-right'>0 "+" Room</span></div>";
			item += "<div > Superior"+"<span class='pull-right'>0 "+" Room</span></div>";
			item += "<div > Villa"+"<span class='pull-right'>0 "+" Room</span></div>";
			item += "<div > Villa At Sea"+"<span class='pull-right'>0 "+" Room</span></div>";
			item += "</div>";

		});
	}

	view.html(item);
	/*<div class='col-md-2 thumbnail'>
			<div class='text-center' id='date_one'>ศ. 16 ธ.ค. </div><br>
			<div>Deluxe<span class='pull-right'>0 Room</span></div>
			<div>Superior<span class='pull-right'>0 Room</span></div>
			<div>Villa<span class='pull-right'>0 Room</span></div>
			<div>Villa At Sea<span class='pull-right'>0 Room</span></div>
		</div>*/
	//two day

	//three day

	//four day

	//five day

}

