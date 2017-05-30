var reserve = {};
//reserve.info = null;
reserve.rooms = [];
reserve.options =  [];
reserve.summary = null;
reserve.customer = [];
reserve.payment = [];
//genaral.get_info();


//list_reserve

function add_room(id,name,type,price){
	
	var list = $('#list_reserve');
	var item = "";
	var key = moment().format("YYYYMMDDHHMMSS");
	name = decodeURIComponent(name);
	type = decodeURIComponent(type);
	console.log("add room >  " + key +"|"+ id + "|" + name + "|"+type+"|"+price);
	var room = { "key" : key ,"package id":id, "room":name , "type" : type , "price" : price }; 
	reserve.rooms.push(room); 
	
	var money_pattern = /(\d)(?=(\d\d\d)+(?!\d))/g;   //formoney
	var money = parseFloat(price).toFixed(2).replace(money_pattern,"$1,")
	
	item += "<span id='"+key+"'  href='#' class='list-group-item'>";
	item += "<h4 class='list-group-item-heading'>"+ name +" <span class='pull-right'>฿ "+money+"</span></h4>";
	item += "<p class='list-group-item-text'>" + type;
	item += "<span class='pull-right' style='cursor:pointer;' onclick=del_room("+key+")>นำออก</span></p>";
	item += "</span>";
	list.append(item);
	recalculate();
}

function del_room(key){
	
	$('#'+key).remove();
	reserve.rooms = jQuery.grep(reserve.rooms,function(item,index){ return item.key != key });
	recalculate();
}

function recalculate() {
	var view_total = $('#total_price');
	var total = 0.0;
	var money = 0.0;

	//var total_pattern = /[^0-9.-]+/g; //format calculate
	var money_pattern = /(\d)(?=(\d\d\d)+(?!\d))/g;   //formoney
	$.each(reserve.rooms,function(i,val){
		
		if(val.price != "")
			total += parseFloat(val.price);
		//total += parseFloat(val.price.replace(total_pattern,''));
		console.debug("price : " + total);
		
	});
	money = total.toFixed(2).replace(money_pattern,"$1,");
	view_total.html("฿ "+money);

	reserve.summary = {"total_amount":total.toFixed(2)};
	
	//$('#data_reserve').val(JSON.stringify(reserve.source));
	
	$('#total_room').html(reserve.rooms.length); 
	$('#data_reserve').val(JSON.stringify(reserve));
	
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
		var items = result.data;
		if(info==null) { 
			alert('Sorry!! Not Found Information Reserve.');
			window.location="quick_reservation.html";
		}
		
		$('#data_reserve').val(JSON.stringify(result.data));
		console.warn($('#data_reserve').val());
		
		//set rooms info;
		//reserve.info = result.data.info;

		if(info!==null){
			$('#checkpoint_date').val(info.start_date);
			$('#travel_date').val(info.end_date);
			$('#night_unit').val(info.night);
			$('#adult_amount').val(info.adults);
			$('#child_amount').val(info.children);
			$('#child_2_amount').val(info.children_2);
			$('#promo_code').val(info.code);
		}
		
		//console.log(result.data.reserve);
		/*
		if(items !== null){
			
			reserve.rooms = items.reserve.rooms ? items.reserve.rooms : {};
			reserve.summary =  items.reserve.summary;
		}
		*/
		if(items.reserve !==null && items.reserve.summary !==null){
			//assign summery
			reserve.summary = items.reserve.summary;
			var total =parseFloat(reserve.summary.total_amount).toFixed(2).replace(money_pattern,"$1,");
			$('#total_price').html("฿ " + total);
			$('#total_room').html(reserve.rooms.length);
			$('#total_night').html(info.night);
		}
		
		if(items.reserve !==null && items.reserve.rooms !== null ){
			//assign rooms list
			reserve.rooms = items.reserve.rooms;
			
			$.each(reserve.rooms,function(i,val){
				
				//console.warn(val);
				var money = parseFloat(val.price).toFixed(2).replace(money_pattern,"$1,");
				
				var item = "<span id='"+val.key+"' href='#' class='list-group-item'>";
				item += "<h4 class='list-group-item-heading'>ห้องพัก "+(i+1)+"<span class='pull-right'>฿ "+money+"</span></h4>";
				item += "<h4 class='list-group-item-text '>";
				item += val.room +" <small class='pull-right' style='cursor:pointer;' onclick=del_room("+val.key+") >นำออก</small> ";
				item += val.type + "</h4></span>";

				$('#list_reserve').append(item);
				
			});
		}
	});
}

reserve.get_receipt = function(val){
	
	var reserve_id = utility.querystr("reserve_id");
	$('#reserve_id').html(reserve_id);
	
	var endpoint = "services/reserve.php";
	var method="get";
	//var args = {"_":new Date().getMilliseconds()};
	var args = {"key":reserve_id,"_":new Date().getMilliseconds()};
	utility.service(endpoint,method,args,function(result){
		console.log(result);
		var money_pattern = /(\d)(?=(\d\d\d)+(?!\d))/g;   //format money
		var info = result.data.info;
		
		var cust = result.data.customer;
		var payment = result.data.payment;
		var reserve = {};
		var summary_price = 0;
		
		$('#cust_name').html(cust.title+" "+cust.fname+ " "+cust.lname);
		$('#cust_mobile').html(cust.prefix_mobile+""+cust.mobile);
		$('#cust_email').html(cust.email);
		$('#card_type').html(payment.card_type);
		$('#card_holder').html(payment.card_holder);
		$('#card_number').html(payment.card_number);
		$('#card_expire').html(payment.card_expire);
		$('#card_validate').html(payment.card_validate);
		
			//define information
		if(result.data !== undefined){
			
			reserve.rooms = result.data.rooms;
			reserve.options = result.data.options;
			reserve.summary = result.data.summary;
		}
		
		
		if(reserve!==undefined){
			
			$.each(reserve.rooms,function(i,val){
				console.log("price="+val.price);
				summary_price += parseFloat(val.price);
				var money = parseFloat(val.price).toFixed(2).replace(money_pattern,"$1,");	
				var item = "<div class='row rowspan'>";
				 item += "<div class='col-md-3'><b>Room "+(i+1)+"</b><br/></div>";
				 item += "<div class='col-md-7'>"+val.room+"<br/>"+val.type+ "</div>";
				 item += "<div class='col-md-2 text-right'>"+money+"</div>";
				 item += "</div>";
				
				$('#list_reserve').append(item);
			});
			
			if(reserve.options !== undefined){
				var item = "";
				var price_option = 0;
				
				$.each(reserve.options,function(i,val){
					//summary.total_amount += parseFloat(val.price);
					summary_price += parseFloat(val.price);
					price_option += parseFloat(val.price);

					var money = parseFloat(val.price).toFixed(2).replace(money_pattern,"$1,");	

					item += "<div class='row'>";
					item += "<div class='col-md-2'>&nbsp;</div>";
					item += "<div class='col-md-7'>"+val.title+"</div>";
					item += "<div class='col-md-3 text-right'>฿ "+money+"</div>";
					item += "</div>";
				});
				
				//summary options
			var money = parseFloat(price_option).toFixed(2).replace(money_pattern,"$1,");	
			var summary_tag = "<div class='row'>";
			summary_tag += "<div class='col-md-2'><b>ทางเลือก</b></div>";
		  	summary_tag += "<div class='col-md-7'>&nbsp;</div>";
		   	summary_tag += "<div class='col-md-3 text-right'><h4>฿ "+money+"</h4></div>";
			summary_tag += "</div>";
			item = summary_tag + item + "<hr/>";

			$('#list_reserve').append(item);
			
			}
			
		}
	
		
		var total = parseFloat(summary_price).toFixed(2).replace(money_pattern,"$1,");	
		var service_price = parseFloat(summary_price) * 0.10;
		var tax_price = parseFloat(summary_price) * 0.07 ;
		var net_price = parseFloat(summary_price) + tax_price + service_price;
		var service = parseFloat(service_price).toFixed(2).replace(money_pattern,"$1,");
		var tax = parseFloat(tax_price).toFixed(2).replace(money_pattern,"$1,");
		var net = parseFloat(net_price).toFixed(2).replace(money_pattern,"$1,");
		
		console.log("service="+service_price);
		
		var item = "<div class='row rowspan'>";
		item += "<div class='col-md-3'><b>Total</b><br/></div>";
		item += "<div class='col-md-7'><br/>";
		item += "<p>Not included : Service Charge</p>";
		item += "<p>Not included : VAT</p>";
		item += "<p>The taxes which are not included are to be paid to the hotel. The total amount is:</p>";
		item += "</div>";
		item += "<div class='col-md-2 text-right'>"+total+"<br/>";
		item += "<p>"+service+"</p>";
		item += "<p>"+tax+"</p>";
		item += "<p>"+net+"</p>";
		item += "</div>";
		/*
		<div class='row rowspan'>
			<div class='col-md-3'><b>Total</b><br/></div>
			<div class='col-md-7'><br/>
				<p>Not included : Service Charge</p><br/>
				<p>Not included : VAT</p><br/>
				<p>The taxes which are not included are to be paid to the hotel. The total amount is:</p>
			</div>
			<div class='col-md-2 text-right'>
				4,248.00
			</div>
		</div>
		*/
		
		$('#list_reserve').append(item);
		
	});


}

reserve.get_roomofweek = function(){
	var endpoint = "services/daysofweek.php";
	var method="get";
	//var args = {"_":new Date().getMilliseconds()};
	var args = {"_":new Date().getMilliseconds()};
	
	utility.service(endpoint,method,args,view_dayofweek);
}

reserve.add_option = function(val){
	
	//var val = JSON.parse(data);

	var money_pattern = /(\d)(?=(\d\d\d)+(?!\d))/g;   //format money
	var money = parseFloat(val.price).toFixed(2).replace(money_pattern,"$1,");
	
	var item = "<span id='"+val.key+"' href='#' class='list-group-item'>";
			item += "<h4 class='list-group-item-heading'>"+val.title+"<span class='pull-right'>฿ "+money+"</span></h4>";
			item += "<h4 class='list-group-item-text'>";
			item += "<span class='text-right'>"+val.desc+"</span><small/>";//<a href='javascript:void(0)' onclick=reserve.del_option('"+val.key+"','" + val.price+"');  >นำออก</a></small> 
			item += "</h4></span>";
			$('#list_reserve').append(item);
	
	reserve.options.push(val);
	
	var price = parseFloat(val.price);
	var total = parseFloat(reserve.summary.total_amount ) + price;
	var money_pattern = /(\d)(?=(\d\d\d)+(?!\d))/g;   //format money
	
	reserve.summary.total_amount = total;
	total = parseFloat(total).toFixed(2).replace(money_pattern,"$1,");
	$('#total_price').html("฿ " + total);
	
	//reserve.calucate_option();
}
reserve.del_option = function(key,price){

	//var val = JSON.parse(data);
	$('#'+key).remove();
	
	reserve.options = jQuery.grep(reserve.options,function(item,index){ return item.key != key });
	var price = parseFloat(price);
	var total = parseFloat(reserve.summary.total_amount) - price;
	var money_pattern = /(\d)(?=(\d\d\d)+(?!\d))/g;   //format money
	
	reserve.summary.total_amount = total;
	total = parseFloat(total).toFixed(2).replace(money_pattern,"$1,");
	$('#total_price').html("฿ " + total);
	//reserve.calucate_option();
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
	
	var title = 'ไม่สามารถยกเลิกได้และเปลี่ยนแปลงได้';
	var view = '<div class="media">';
	view += '<div class="media-body">ในกรณีที่ท่านไม่มาแสดงตัว จะมีค่าปรับ 100%<br/>';
	//view += '<button type="button" class="btn btn-warning" data-toggle="collapse" data-target="#modal_condition">เงื่อนไข</button></div>';
	view += '</div>';
	
	var expireDate = new Date();
	var numberOfDaysToAdd = 14;
	var lang = 'th';
	expireDate.setDate(expireDate.getDate() + numberOfDaysToAdd); 
	expireDate = moment(expireDate).format("DD/MM/YYYY");
	expireDate = utility.date_format(expireDate,lang);
	var condition = utility.get_templete("templete_nochange_detail.html");
	condition = condition.replace("{reserve_expire}",expireDate);
	
	
	$('#modaltitle').text(title);
	$('#modalcontent').html(view);
	$('#view_condition').css('display','block');
	$('#modal_condition').collapse('hide');
	$('#modal_condition').html(condition);
	$('#modaldialog').modal();
}
reserve.modal_change = function(){
	
	var title = 'ยกเลิกได้และเปลี่ยนแปลงได้';
	var expireDate = new Date();
	var numberOfDaysToAdd = 14;
	var lang = 'th';
	expireDate.setDate(expireDate.getDate() + numberOfDaysToAdd); 
	expireDate = moment(expireDate).format("DD/MM/YYYY");
	expireDate = utility.date_format(expireDate,lang);
	
	var view = '<div class="media">';
	view += '<div class="media-body">ข้อเสนอนี้สามารถยกเลิกหรือแก้ไขได้โดยไม่มีค่าธรรมเนียมจนถึงวันที่ '+expireDate+',00:00 (UTC+07:00) หลังจากวันที่ดังกล่าวอาจมีค่าธรรมเนียมในการยกเลิกหรือแก้ไข ในกรณีทีท่านไม่มาแสดงตัว จะมีค่าปรับ 100%</div>';
	view += '</div>';

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
	
	var title = 'ชำระเงินจองทางอินเทอร์เน็ต';
	var view = '<div class="media">';
	
	view += '<div class="media-body">ชำระค่ามัดจำ 100% of 1 คืน เดี๋ยวนี้เพื่อรับรองการจองของท่าน ยอดเงินส่วนที่เหลือจะถูกเรียกเก็บที่โรงแรม</div>';
	view += '</div>';
	
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
	
	var title = 'อาหารเช้า';
	var view = '<div class="media">';
	
	view += '<div class="media-body">เราให้บริการอาหารไทย และอาหารนานาชาติ คัดสรรคุณภาพดี รสชาติอร่อย และพิธีพิถันในการปรุงอาหาร <br/> เปิดบริการตั้งแต่เวลา 7.00 - 10.00 น.</div>';
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
	console.log(resp);

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

