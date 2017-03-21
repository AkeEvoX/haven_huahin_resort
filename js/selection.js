var reserve = {};
reserve.info = null;
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
	console.log("add room >  " + key +"|"+ id + "|" + name + "|"+type+"|"+price);
	var room = { "key" : key ,"id":id, "room":name , "type" : type , "price" : price }; 
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
	$('#data_reserve').val(JSON.stringify(reserve));
}

reserve.get_info = function(){
	var endpoint = "services/info.php";
	var method = "get";
	var args = {"_":new Date().getMilliseconds()};

	utility.service(endpoint,method,args,function(result){
		
		console.log(result);
		var money_pattern = /(\d)(?=(\d\d\d)+(?!\d))/g;   //format money
		
		if(result.data.info==null) { 
			alert('Sorry!! Not Found Information Reserve.');
			window.location="quick_reservation.html";
		}

		//set rooms info;
		reserve.info = result.data.info;

		if(reserve.info!=undefined){
			$('#checkpoint_date').val(reserve.info.date);
			$('#travel_date').val(reserve.info.date);
			$('#adult_amount').val(reserve.info.adults);
			$('#child_amount').val(reserve.info.children);
			$('#promo_code').val(reserve.info.code);
			
		}

		if(result.data.reserve != undefined){
			
			reserve.rooms = result.data.reserve.rooms;
			reserve.summary = result.data.reserve.summary;
		}
		
		if(reserve.summary!=undefined){

			var total =parseFloat(reserve.summary.total_amount).toFixed(2).replace(money_pattern,"$1,");
			$('#total_price').html("฿ " + total);
			$('#total_room').html(reserve.rooms.length +"ห้องพัก " + reserve.info.night + "คืน");
		}
		
		if(reserve.rooms != undefined ){
			
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
	var endpoint = "services/info.php";
	var method="get";
	var args = {"_":new Date().getMilliseconds()};
	utility.service(endpoint,method,args,function(result){
		console.log(result);
		var money_pattern = /(\d)(?=(\d\d\d)+(?!\d))/g;   //format money
		var info = result.data.info;
		
		var cust = result.data.customer;
		var payment = result.data.payment;
		var reserve = result.data.reserve;
		var summary = reserve.summary;
		
		$('#cust_name').html(cust.title+" "+cust.fname+ " "+cust.lname);
		$('#cust_mobile').html(cust.prefix_mobile+""+cust.mobile);
		$('#cust_email').html(cust.email);
		$('#card_type').html(payment.card_type);
		$('#card_holder').html(payment.card_holder);
		$('#card_number').html(payment.card_number);
		$('#card_expire').html(payment.card_expire_month+"/"+payment.card_expire_year);
		$('#card_validate').html(payment.card_validate);
		
		
		if(reserve!=undefined){
			
			$.each(reserve.rooms,function(i,val){
				
				var money = parseFloat(val.price).toFixed(2).replace(money_pattern,"$1,");	
				var item = "<div class='row rowspan'>";
				 item += "<div class='col-md-3'><b>Room "+(i+1)+"</b><br/></div>";
				 item += "<div class='col-md-7'>"+val.room+"<br/>"+val.type+ "</div>";
				 item += "<div class='col-md-2 text-right'>"+money+"</div>";
				 item += "</div>";
				/*
				<div class='row rowspan'>
					<div class='col-md-3'><b>Room 1</b><br/>
					2 adults
					</div>
					<div class='col-md-7'>
						Deluxe</br>
						Internet Rate
					</div>
					<div class='col-md-2 text-right'>
						4,248.00
					</div>
				</div>
				*/
				$('#list_reserve').append(item);
			});
			
		}
		var total = parseFloat(summary.total_amount).toFixed(2).replace(money_pattern,"$1,");	
		var service_price = parseFloat(summary.total_amount) * .03;
		var tax_price = parseFloat(summary.total_amount) * .07 ;
		var net_price = parseFloat(summary.total_amount) + tax_price + service_price;
		var service = parseFloat(service_price).toFixed(2).replace(money_pattern,"$1,");
		var tax = parseFloat(tax_price).toFixed(2).replace(money_pattern,"$1,");
		var net = parseFloat(net_price).toFixed(2).replace(money_pattern,"$1,");
		
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

reserve.add_option = function(val){
	
	//var val = JSON.parse(data);

	var money_pattern = /(\d)(?=(\d\d\d)+(?!\d))/g;   //format money
	var money = parseFloat(val.price).toFixed(2).replace(money_pattern,"$1,");
	
	var item = "<span id='"+val.key+"' href='#' class='list-group-item'>";
			item += "<h4 class='list-group-item-heading'>"+val.title+"<span class='pull-right'>฿ "+money+"</span></h4>";
			item += "<h4 class='list-group-item-text '>";
			item += "&nbsp;<small class='pull-right'>";//<a href='javascript:void(0)' onclick=reserve.del_option('"+val.key+"','" + val.price+"');  >นำออก</a></small> 
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
	
	
	var condition = "<h3>เงื่อนไขการขาย</h3>";
	condition += "<p>";
	condition += "เงินค่าที่พักเต็มจำนวนจะถูกเรียกเก็บจากบัตรเครดิตของท่านไม่นานหลังจากยืนยันการจองของท่าน<br/>";
	condition += "การจองนี้ไม่สามารถยกเลิกหรือแก้ไข<br/>";
	condition += "ในกรณีที่ท่านไม่แสดงตัว จะมีค่าปรับ 100%<br/>";
	condition += "</p>";
	condition += add_default_condition();
	
	$('#modaltitle').text(title);
	$('#modalcontent').html(view);
	$('#modal_condition').collapse('hide');
	$('#modal_condition').html(condition);
	$('#modaldialog').modal();
}

reserve.modal_change = function(){
	
	var title = 'ยกเลิกได้และเปลี่ยนแปลงได้';
	var view = '<div class="media">';
	
	view += '<div class="media-body">ข้อเสนอนี้สามารถยกเลิกหรือแก้ไขได้โดยไม่มีค่าธรรมเนียมจนถึงวันที่ 4 มีนาคม 2560,00:00 (UTC+07:00) หลังจากวันที่ดังกล่าวอาจมีค่าธรรมเนียมในการยกเลิกหรือแก้ไข ในกรณีทีท่านไม่มาแสดงตัว จะมีค่าปรับ 100%</div>';
	view += '</div>';
	var condition = "<h3>เงื่อนไขการขาย</h3>";
	condition += "<p>";
	condition += "ชำระค่ามัดจำ 100% ของ 1 คืน เดี๋ยวนี้เพื่อรับรองการจองของท่าน ยอดเงินส่วนที่เหลือจะถูกเรียกเก็บที่โรงแรม<br/>";
	condition += "ข้อเสนอนี้สามารถยกเลิกหรือแก้ไขได้โดยไม่มีค่าธรรมเนียมจนถึงวันที่ 4 มีนาคม 2560, 00:00 (UTC+07:00)<br/>";
	condition += "หลังจากวันที่ดังกล่าวอาจมีค่าธรรมเนียมในการยกเลิกหรือแก้ไข<br/>";
	condition += "ในกรณีที่ท่านไม่แสดงตัว จะมีค่าปรับ 100%<br/>";
	condition += "</p>";
	condition += add_default_condition();
	
	$('#modaltitle').text(title);
	$('#modalcontent').html(view);
	$('#modal_condition').collapse('hide');
	$('#modal_condition').html(condition);
	$('#modaldialog').modal();
}

reserve.modal_internet = function(){
	
	var title = 'ชำระเงินจองทางอินเทอร์เน็ต';
	var view = '<div class="media">';
	
	view += '<div class="media-body">ชำระค่ามัดจำ 100% of 1 คืน เดี๋ยวนี้เพื่อรับรองการจองของท่าน ยอดเงินส่วนที่เหลือจะถูกเรียกเก็บที่โรงแรม</div>';
	view += '</div>';
	
	var condition = "<h3>เงื่อนไขการขาย</h3>";
	condition += "<p>";
	condition += "ชำระค่ามัดจำ 100% ของ 1 คืน เดี๋ยวนี้เพื่อรับรองการจองของท่าน ยอดเงินส่วนที่เหลือจะถูกเรียกเก็บที่โรงแรม<br/>";
	condition += "ข้อเสนอนี้สามารถยกเลิกหรือแก้ไขได้โดยไม่มีค่าธรรมเนียมจนถึงวันที่ 4 มีนาคม 2560, 00:00 (UTC+07:00)<br/>";
	condition += "หลังจากวันที่ดังกล่าวอาจมีค่าธรรมเนียมในการยกเลิกหรือแก้ไข<br/>";
	condition += "ในกรณีที่ท่านไม่แสดงตัว จะมีค่าปรับ 100%<br/>";
	condition += "</p>";
	condition += add_default_condition();
	
	$('#modaltitle').text(title);
	$('#modalcontent').html(view);
	$('#modal_condition').collapse('hide');
	$('#modal_condition').html(condition);
	$('#modaldialog').modal();
}

reserve.modal_breakfast = function(){
	
	var title = 'อาหารเช้า';
	var view = '<div class="media">';
	
	if(image!='')
		view += '<div class="media-left"><a href="#"><img class="media-object" src="'+image+'" alt="..."></a></div>';
	
	view += '<div class="media-body">เราให้บริการอาหารไทย และอาหารนานาชาติ คัดสรรคุณภาพดี รสชาติอร่อย และพิธีพิถันในการปรุงอาหาร <br/> เปิดบริการตั้งแต่เวลา 7.00 - 10.00 น.</div>';
	view += '</div>';
	
	var condition = "<h3>เงื่อนไขการขาย</h3>";
	condition += "<p>";
	condition += "ชำระค่ามัดจำ 100% ของ 1 คืน เดี๋ยวนี้เพื่อรับรองการจองของท่าน ยอดเงินส่วนที่เหลือจะถูกเรียกเก็บที่โรงแรม<br/>";
	condition += "ข้อเสนอนี้สามารถยกเลิกหรือแก้ไขได้โดยไม่มีค่าธรรมเนียมจนถึงวันที่ 4 มีนาคม 2560, 00:00 (UTC+07:00)<br/>";
	condition += "หลังจากวันที่ดังกล่าวอาจมีค่าธรรมเนียมในการยกเลิกหรือแก้ไข<br/>";
	condition += "ในกรณีที่ท่านไม่แสดงตัว จะมีค่าปรับ 100%<br/>";
	condition += "</p>";
	condition += add_default_condition();
	
	$('#modaltitle').text(title);
	$('#modalcontent').html(view);
	$('#modal_condition').collapse('hide');
	$('#modal_condition').html(condition);
	$('#modaldialog').modal();
	
}

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