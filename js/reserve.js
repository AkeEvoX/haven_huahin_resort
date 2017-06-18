var reserve = {};

reserve.get_confirmation = function(){

	var reserve_id = utility.querystr("reserve_id");
	$('#reserve_id').html(reserve_id);

	var endpoint = "services/reserve.php";
	var method = "get";
	var args = {"key":reserve_id,"_":new Date().getMilliseconds()};

	utility.service(endpoint,method,args,function(result){

		console.log(result);
		var money_pattern = /(\d)(?=(\d\d\d)+(?!\d))/g;   //format money
		var rooms ;
		var options;
		var summary;
		var customer;
		if(result.data.customer==null){
			alert('Sorry!! Not Found Information Reserve.');
			//window.location="quick_reservation.html";
			//return false;
		}
		if(result.data.customer!=null){
			
			customer = result.data.customer;
			$('#custome_name').html(customer.title+" "+customer.fname+" " + customer.lname)
			$('#customer_email').html(customer.email);	
		}
		
		//reserve info
		if(result.data.info!=null){
			var info = result.data.info;
			var item = "<div class='row'>";
			 item += "<div class='col-md-2'><label>ผู้เข้าพัก</label></div>";
			 item += "<div class='col-md-7'>";
			 item += "ผู้ใหญ่ : "+info.adults+" คน</br>";
			 item += "เด็กอายุ 5-11 : "+info.children_2+" คน</br>";
			 item += "เด็กอายุ 0 : 4 : "+info.children+" คน</br>";
			 item += "</div >";
			 item += "<div class='col-md-3 text-right'><h4>&nbsp;</h4></div>";
			 item += "</div ><hr/>";
			 
			$('#list_reserve').append(item);
		}
		

		var date = moment(result.data.info.date_start).add('days',14).format('DD/MM/YYYY');
		console.log(date + " || " + result.data.info.date_start);
		var cancel_date = utility.date_format_th(date);
		$('#cancel_date').html(cancel_date);
		
		
		//define information
		if(result.data != undefined){
			rooms = result.data.rooms;
			options = result.data.options;
			summary = result.data.summary;
		}
		
		//list room
		if(rooms != undefined ){
			
			console.warn("found cancel rooms.");
				$.each(rooms,function(i,val){
					
					var money = parseFloat(val.price).toFixed(2).replace(money_pattern,"$1,");
					var item = "<div class='row'>";
					 item += "<div class='col-md-2'><label>ห้องพัก "+(i+1)+"</label></div>";
					 item += "<div class='col-md-7'>";
					 item += val.room+"</br>";
					 item += val.type+"</br>";
					 item += "</div >";
					 item += "<div class='col-md-3 text-right'><h4>฿ "+money+"</h4></div>";
					 item += "</div ><hr/>";
					 
					$('#list_reserve').append(item);
					
				});
		}
		//list option
		if(options != undefined){

			var item = "";
			var price_option = 0;
			$.each(options,function(i,val){
				price_option += parseFloat(val.price);

				var money = parseFloat(val.price).toFixed(2).replace(money_pattern,"$1,");	

				item += "<div class='row'>";
				item += "<div class='col-md-2'>&nbsp;</div>";
				item += "<div class='col-md-7'>"+val.title+"<br>"+val.desc+"</div>";
				item += "<div class='col-md-3 text-right'>฿ "+money+"</div>";
				item += "</div>";
			});
			//summary options
			var money = parseFloat(price_option).toFixed(2).replace(money_pattern,"$1,");	
			var summary_tag = "<div class='row'>";
			summary_tag += "<div class='col-md-2'>ทางเลือก</div>";
		  	summary_tag += "<div class='col-md-7'>&nbsp;</div>";
		   	summary_tag += "<div class='col-md-3 text-right'><h4>฿ "+money+"</h4></div>";
			summary_tag += "</div>";
			item = summary_tag + item + "<hr/>";

			$('#list_reserve').append(item);
		}

		//summary price
		if(summary!=null){
			
			var total = parseFloat(summary.amount).toFixed(2).replace(money_pattern,"$1,");	
			var charge = parseFloat(summary.charge) ;
			var tax_price = parseFloat(summary.tax);
			var net_price = parseFloat(summary.net) + tax_price + charge;
			var service = parseFloat(charge).toFixed(2).replace(money_pattern,"$1,");
			var tax = parseFloat(tax_price).toFixed(2).replace(money_pattern,"$1,");
			var net = parseFloat(net_price).toFixed(2).replace(money_pattern,"$1,");
			
				var	item = "<div class='row'>";
			item += "<div class='col-md-3 '><h4>รวม</h4></div>";
			item += "<div class='col-md-offset-6 col-md-3 text-right'><span><h4>$ "+total+"</h4></span></div>";
			item += "</div>";
			
				
			item += "<div class='row'>";
			item += "<div class='col-md-offset-2 col-md-3 '><h4>ค่าบริการ</h4></div>";
			item += "<div class='col-md-offset-4 col-md-3 text-right'><span><h4>$ "+service+"</h4></span></div>";
			item += "</div>";
			
			item += "<div class='row'>";
			item += "<div class='col-md-offset-2 col-md-3 '><h4>ภาษีของรัฐ</h4></div>";
			item += "<div class='col-md-offset-3 col-md-4 text-right'><span><h4>$ "+tax+"</h4></span></div>";
			item += "</div>";
			
			item += "<div class='row rowspan'>";
			item += "<div class='col-md-offset-2 col-md-7'><pre><h4>รวมเป็นเงินที่ต้องชำระทั้งสิ้น</h4></pre></div>";
			item += "<div class='col-md-3 text-right'><pre><h4>฿ "+net+"</h4></pre></div>";
			item += "</div>";

			
			if($("input[name='orderRef']").length!=0) 
				$("input[name='orderRef']").val(reserve_id);
			
			if($("input[name='amount']").length!=0) 
				$("input[name='amount']").val(net_price);
			
			if($("input[name='payment_reserve_id']").length!=0) 
				$("input[name='payment_reserve_id']").val(reserve_id);
			
			if($("input[name='payment_amount']").length!=0) 
				$("input[name='payment_amount']").val(net_price);
			
			$('#list_reserve').append(item);
		
		}
		// var total = parseFloat(summary_price).toFixed(2).replace(money_pattern,"$1,");
		// var service_price = parseFloat(summary_price) * .10;
		// var tax_price = parseFloat(summary_price) * .07 ;
		// var net_price = parseFloat(summary_price) + tax_price + service_price;
		// console.log(net_price);
		// var service = parseFloat(service_price).toFixed(2).replace(money_pattern,"$1,");
		// var tax = parseFloat(tax_price).toFixed(2).replace(money_pattern,"$1,");
		// var net = parseFloat(net_price).toFixed(2).replace(money_pattern,"$1,");
		
	

	});

};


reserve.get_summary = function(){

	var endpoint = "services/info.php";
	var method="get";
	var args = {"_":new Date().getMilliseconds()};
	var lang = 'en';
	utility.service(endpoint,method,args,function(result){
		
		console.log(result);
		var money_pattern = /(\d)(?=(\d\d\d)+(?!\d))/g;   //format money
		var summary_price = 0;
		//set rooms info;
		reserve.info = result.data.info;
		
		if(result.info==null) { 
			alert('Sorry!! Not Found Information Reserve.');
			window.location="quick_reservation.html";
		}

		if(result.info){
			
			var info = result.info;
			$('#checkpoint_date').html(info.start_date);
			$('#travle_date').html(info.end_date);
			
			
			
			$('#date_start').html(utility.date_format(info.start_date,lang));
			$('#date_end').html(utility.date_format(info.end_date,lang));
			
			//var date = moment(info.start_date,'DD/MM/YYYY').add('days',14).format('DD/MM/YYYY');
			//console.log("exp : " + date + " || start :" + info.start_date);
			var expire_date = utility.date_format(info.expire_date,lang);// utility.date_format_th(date);
			$('#cancel_date').html(expire_date);
			$('#reserve_expire').html(expire_date);

			var rent ="";
			rent = "ผู้ใหญ่ "+ info.adults + " ท่าน ";
			rent += "เด็ก 5-11 "+ info.children_2 +" ท่าน"
			rent += "เด็ก 0-4 "+ info.children +" ท่าน"
			$('#rent_amount').html(rent);

			$('#night_unit').html(info.night);
			$('#adult_amount').html(info.adults);
			$('#child_2_amount').html(info.children_2);
			$('#child_amount').html(info.children);
			$('#promo_code').html(info.code);

			$('#comment').text(info.comment);
		}

		if(result.data.reserve != undefined){
			
			reserve.rooms = result.data.reserve.rooms;
			reserve.options = result.data.reserve.options;
			reserve.summary = result.data.reserve.summary;
		}
		
		//list room
		if(reserve.rooms != undefined ){
			console.warn("found cancel rooms.");
				$.each(reserve.rooms,function(i,val){
					summary_price += parseFloat(val.price);
					var money = parseFloat(val.price).toFixed(2).replace(money_pattern,"$1,");
					
					var item = "<div class='row'>";
					 item += "<div class='col-md-2'><label>ห้องพัก "+(i+1)+"</label><br><small>ผู้ใหญ่ 1</small></div>";
					 item += "<div class='col-md-7'>";
					 item += val.room+"</br>";
					 item += val.type+"</br>";
					 item += "</div >";
					 item += "<div class='col-md-3 text-right'><h4>฿ "+money+"</h4></div>";
					 item += "</div ><hr/>";
					 
					$('#list_reserve').append(item);
					
				});
		}
		//list option
		if(reserve.options != undefined){

			var item = "";
			var price_option = 0;
			$.each(reserve.options,function(i,val){
				summary_price += parseFloat(val.price);
				price_option += parseFloat(val.price);

				var money = parseFloat(val.price).toFixed(2).replace(money_pattern,"$1,");	

				item += "<div class='row'>";
				item += "<div class='col-md-2'>&nbsp;</div>";
				item += "<div class='col-md-7'>"+val.title+"<br/>"+val.desc+"</div>";
				item += "<div class='col-md-3 text-right'>฿ "+money+"</div>";
				item += "</div>";
			})
			//summary options
			var money = parseFloat(price_option).toFixed(2).replace(money_pattern,"$1,");	
			var summary_tag = "<div class='row'>";
			summary_tag += "<div class='col-md-2'>ทางเลือก</div>";
		  	summary_tag += "<div class='col-md-7'>&nbsp;</div>";
		   	summary_tag += "<div class='col-md-3 text-right'><h4>฿ "+money+"</h4></div>";
			summary_tag += "</div>";
			item = summary_tag + item + "<hr/>";

			$('#list_reserve').append(item);
		}

		//summary price
		if(reserve.summary!=undefined){

			/*
			charge
			tax
			net
			total_amount
			*/
			var total = parseFloat(reserve.summary.total_amount).toFixed(2).replace(money_pattern,"$1,");
			var service = parseFloat(reserve.summary.charge).toFixed(2).replace(money_pattern,"$1,");
			var tax = parseFloat(reserve.summary.tax).toFixed(2).replace(money_pattern,"$1,");
			var net = parseFloat(reserve.summary.net).toFixed(2).replace(money_pattern,"$1,");
		}
		/*
		var total = parseFloat(summary_price).toFixed(2).replace(money_pattern,"$1,");
		var service_price = parseFloat(summary_price) * .10;
		var tax_price = parseFloat(summary_price) * .07 ;
		var net_price = parseFloat(summary_price) + tax_price + service_price;
		
		var service = parseFloat(service_price).toFixed(2).replace(money_pattern,"$1,");
		var tax = parseFloat(tax_price).toFixed(2).replace(money_pattern,"$1,");
		var net = parseFloat(net_price).toFixed(2).replace(money_pattern,"$1,");
		console.log(net_price);
		*/
		
		var	item = "<div class='row'>";
		item += "<div class='col-md-2 '><h4>รวม</h4></div>";
		item += "<div class='col-md-offset-7 col-md-3 text-right'><span><h4>$ "+total+"</h4></span></div>";
		item += "</div>";
	

		item += "<div class='row'>";
		item += "<div class='col-md-offset-2 col-md-3 '><h4>ค่าบริการ</h4></div>";
		item += "<div class='col-md-offset-4 col-md-3 text-right'><span><h4>$ "+service+"</h4></span></div>";
		item += "</div>";
		
		item += "<div class='row'>";
		item += "<div class='col-md-offset-2 col-md-3 '><h4>ภาษีของรัฐ</h4></div>";
		item += "<div class='col-md-offset-3 col-md-4 text-right'><span><h4>$ "+tax+"</h4></span></div>";
		item += "</div>";
		
		item += "<div class='row rowspan'>";
		item += "<div class='col-md-offset-2 col-md-7'><pre><h4>รวมเป็นเงินที่ต้องชำระทั้งสิ้น</h4></pre></div>";
		item += "<div class='col-md-3 text-right'><pre><h4>฿ "+net+"</h4></pre></div>";
		item += "</div>";

		$('#list_reserve').append(item);

	});
}


reserve.get_receipt = function(val){
	
	var reserve_id = utility.querystr("reserve_id");
	$('#reserve_id').html(reserve_id);
	$('#title_reserve_id').html(reserve_id);
	
	//expire_date
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
		var rooms = {};
		var options = {};
		var summary = {};
		var summary_price = 0;
		
		var lang = 'en';
		
		$('#expire_date').html(utility.date_format(info.date_expire,lang));
		$('#start_date').html(utility.date_format(info.date_start,lang));
		$('#end_date').html(utility.date_format(info.date_end,lang));
		
		//customer information
		$('#cust_name').html(cust.title+" "+cust.fname+ " "+cust.lname);
		$('#cust_mobile').html(cust.prefix_mobile+""+cust.mobile);
		$('#cust_email').html(cust.email);
		/* 
		$('#card_type').html(payment.card_type);
		$('#card_holder').html(payment.card_holder);
		$('#card_number').html(payment.card_number);
		$('#card_expire').html(payment.card_expire);
		$('#card_validate').html(payment.card_validate); */
		
			//define information
		if(result.data !== undefined){
			
			rooms = result.data.rooms;
			options = result.data.options;
			summary = result.data.summary;
			console.log(summary);
		}
		
		
		if(rooms!==null){
			
			$.each(rooms,function(i,val){
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
			
			if(options !== null){
				var item = "";
				var price_option = 0;
				
				$.each(options,function(i,val){
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
	
		if(summary!=null){
			
			
			var total = parseFloat(summary.amount).toFixed(2).replace(money_pattern,"$1,");	
			var charge = parseFloat(summary.charge) ;
			var tax_price = parseFloat(summary.tax);
			var net_price = parseFloat(summary.net) + tax_price + charge;
			var service = parseFloat(charge).toFixed(2).replace(money_pattern,"$1,");
			var tax = parseFloat(tax_price).toFixed(2).replace(money_pattern,"$1,");
			var net = parseFloat(net_price).toFixed(2).replace(money_pattern,"$1,");
			
			//console.log("service="+service_price);
			
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
			
			$('#list_reserve').append(item);
		}
		
	
		
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
		
		
		
	});
}


reserve.payment_manual = function(args){
	
	var endpoint = "services/payment.php";
	var method = "POST";

	utility.data(endpoint,method,args,function(data){
		
		console.info(data);
		var response = JSON.parse(data);
		alert(response.result);
		window.location='quick_reservation.html';
	});
	
}
