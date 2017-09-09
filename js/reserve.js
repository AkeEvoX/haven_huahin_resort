var reserve = {};

reserve.get_confirmation = function(callback){

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
			
			alert(pages.message.reserve_not_found);
			//'Sorry!! Not Found Information Reserve.'
		}
		if(result.data.customer!=null){
			
			customer = result.data.customer;
			$('#custome_name').html(customer.title+" "+customer.fname+" " + customer.lname)
			$('#customer_email').html(customer.email);	
		}
		
		//reserve info
		if(result.data.info!=null){
			
			reserve.info =result.data.info;
			var info = reserve.info;
			var item = "<div class='row'>";
			item += "<div class='col-md-3'><label>"+pages.message.guest+"</label></div>";
			item += "<div class='col-md-4'>";
			item += pages.message.adults + " : "+info.adults+" "+pages.message.person+"</br>";
			item += pages.message.children_2 + " : "+info.children_2+" "+pages.message.person+"</br>";
			item += pages.message.children + " : "+info.children+" "+pages.message.person+"</br>";
			item += "</div >";
			item += "<div class='col-md-5'>";
			item += pages.message.reserve +" : "+ utility.datetime_format(info.date_reserve,pages.lang)+"<br/>";
			item += pages.message.checkin +" : "+utility.date_format(info.date_start,pages.lang)  +"<br/>";
			item += pages.message.checkout +" : "+utility.date_format(info.date_end,pages.lang) +"<br/>";
			item += "</div>";
			item += "</div ><hr/>";
			 
			$('#list_reserve').append(item);
		}
		

		var date = moment(result.data.info.date_start).add('days',14).format('DD/MM/YYYY');
		//set format expire date 
		reserve.info.expire_date = moment(reserve.info.expire_date).format('DD/MM/YYYY');
		//console.log(date + " || " + result.data.info.date_start);
		var cancel_date = utility.date_format_th(date);
		$('#cancel_date').html(cancel_date);
		
		if(result.data.reserve != undefined){
			
			reserve.rooms = result.data.reserve.rooms;
			reserve.options = result.data.reserve.options;
			reserve.summary = result.data.reserve.summary;
		}
		
		//define information
		if(result.data != undefined){

			rooms = result.data.rooms;
			options = result.data.options;
			summary = result.data.summary;
			reserve.rooms = result.data.rooms;	
		}
		//var sum_room_price = 0;
		//List room
		if(rooms != undefined ){
			
			console.warn("found cancel rooms.");
				$.each(rooms,function(i,val){

					var room_price = parseFloat(val.price); //* parseFloat(result.data.info.night) 
					//sum_room_price += room_price;
					var money = parseFloat(room_price).toFixed(2).replace(money_pattern,"$1,");
					
					var item = "<div class='row'>";
					 item += "<div class='col-md-3'><label>"+pages.message.room+" "+(i+1)+"</label></div>";
					 item += "<div class='col-md-6 col-xs-6 '>";
					 item += val.type+"</br>";
					 item += pages.message.bedding+val.bed_name+"</br>";
					 item += val.room+" ( "+ result.data.info.night +" "+pages.message.night+")</br>";
					 item += "</div >";
					 item += "<div class='col-md-3 text-right'><h4> "+money+"</h4></div>";
					 item += "</div ><hr/>";
					 
					$('#list_reserve').append(item);
					
				});
		}

		var net  = 0;
		//summary price
		if(summary!=null){
			
			
			var service = parseFloat(summary.service) ;
			var vat = parseFloat(summary.vat);
			var sum = parseFloat(summary.sum) ;	//summary.amount
			var net_parse = parseFloat(summary.net);

			sum = parseFloat(sum).toFixed(2).replace(money_pattern,"$1,");
			service = parseFloat(service).toFixed(2).replace(money_pattern,"$1,");
			vat = parseFloat(vat).toFixed(2).replace(money_pattern,"$1,");
			net = parseFloat(net_parse).toFixed(2).replace(money_pattern,"$1,");

			var item = "<div class='row'>";
			item += "<div class='col-md-3'>"+pages.message.service+"</div>";
			item += "<div class='col-md-6 col-xs-6'>&nbsp;</div>";
			item += "<div class='col-md-3 text-right'><h4> "+service+"</h4></div>";
			item += "</div>";


			item += "<div class='row'>";
			item += "<div class='col-md-3'>"+pages.message.tax+"</div>";
			item += "<div class='col-md-6 col-xs-6'>&nbsp;</div>";
			item += "<div class='col-md-3 text-right'><h4> "+vat+"</h4></div>";
			item += "</div>";
			
			item += "<div class='row'>";
			item += "<div class='col-md-3'>"+pages.message.summary+"</div>";
			item += "<div class='col-md-6 col-xs-6'>&nbsp;</div>";
			item += "<div class='col-md-3 text-right'><h4> "+sum+"</h4></div>";
			item += "</div>";
			item += "<hr/>";
		
			if($("input[name='orderRef']").length!=0) 
				$("input[name='orderRef']").val(reserve_id);
			
			if($("input[name='amount']").length!=0) 
				$("input[name='amount']").val(net_parse);
			
			if($("input[name='payment_reserve_id']").length!=0) 
				$("input[name='payment_reserve_id']").val(reserve_id);
			
			if($("input[name='payment_amount']").length!=0) 
				$("input[name='payment_amount']").val(net_parse);
			
			$('#list_reserve').append(item);
		
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
				item += "<div class='col-md-3 text-right'> "+money+"</div>";
				item += "</div>";
			});
			//summary options
			var money = parseFloat(price_option).toFixed(2).replace(money_pattern,"$1,");	
			var summary_tag = "<div class='row'>";
			summary_tag += "<div class='col-md-2'>"+pages.message.option+"</div>";
		  	summary_tag += "<div class='col-md-7'>&nbsp;</div>";
		   	summary_tag += "<div class='col-md-3 text-right'><h4> "+money+"</h4></div>";
			summary_tag += "</div>";
			item = summary_tag + item + "<hr/>";

			$('#list_reserve').append(item);
		}

		item = "<div class='row rowspan'>";
		item += "<div class='col-md-offset-2 col-md-7 col-xs-7'>"+pages.message.net_price_detail+"</div>";
		item += "<div class='col-md-3 text-right'><b><h4> "+net+"</h4></b></div>";
		item += "</div>";

		$('#list_reserve').append(item);

		
	
	},callback);

};

reserve.get_summary = function(callback){

	var endpoint = "services/info.php";
	var method="get";
	var args = {"_":new Date().getMilliseconds()};
	var lang = 'en';
	utility.service(endpoint,method,args,function(result){
		
		console.log(result);
		var money_pattern = /(\d)(?=(\d\d\d)+(?!\d))/g;   //format money
		var summary_price = 0;
		//set rooms info;
		reserve.info = result.info;
		
		if(result.info==null) { 
			alert('Sorry!! Not Found Information Reserve.');
			window.location="quick_reservation.html";
		}

		if(result.info){
			
			var info = result.info;
			$('#checkpoint_date').html(info.start_date);
			$('#travle_date').html(info.end_date);
			
			var start_date = moment(info.start_date,'DD-MM-YYYY').format('YYYY-MM-DD') ;
			var end_date =  moment(info.end_date,'DD-MM-YYYY').format('YYYY-MM-DD') ;
			var expire_date = moment(info.expire_date,'DD-MM-YYYY').format('YYYY-MM-DD') ;
			$('#date_start').html(utility.date_format(start_date,lang));
			$('#date_end').html(utility.date_format(end_date,lang));
			
			//var date = moment(info.start_date,'DD/MM/YYYY').add('days',14).format('DD/MM/YYYY');
			//console.log("exp : " + date + " || start :" + info.start_date);
			var expire_date = utility.date_format(expire_date,lang) + " , 00:00 (UTC+07:00) ";
			$('#cancel_date').html(expire_date);
			//$('#reserve_expire').html(expire_date);

			var rent ="";
			rent = pages.message.adults +" "+ info.adults + " "+pages.message.person;
			rent += pages.message.children_2 +" "+ info.children_2 + " " + pages.message.person;
			rent += pages.message.children +" "+ info.children +" "+pages.message.person;
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
		var sum_room_price = 0;
		if(reserve.rooms != undefined ){
			console.warn("found cancel rooms.");
				$.each(reserve.rooms,function(i,val){
					//summary_price += parseFloat(val.price);
					
					console.warn(val);
					
					console.warn("room price : "+val.price);
					var room_price = parseFloat(val.price) ;//* parseFloat(result.info.night)
					var money = parseFloat(room_price).toFixed(2).replace(money_pattern,"$1,");
					
					var item = "<div class='row'>";
					 item += "<div class='col-md-2'><label>"+pages.message.room+" "+(i+1)+"</label></div>";//<br><small>"+ pages.message.adults+" "+info.adults+"</small>
					 item += "<div class='col-md-7'>";
					 item += val.room+"</br>";
					 item += pages.message.bedding+val.bed_name+"</br>";
					 item += val.type+" ( "+ result.info.night+" "+pages.message.night+")</br>";
					 item += "</div >";
					 item += "<div class='col-md-3 text-right'><h4>฿ "+money+"</h4></div>";
					 item += "</div ><hr/>";

					$('#list_reserve').append(item);
					
				});
		}

		//summary room price
		if(reserve.summary!=undefined){
			
			var sum = parseFloat(reserve.summary.sum).toFixed(2).replace(money_pattern,"$1,");
			var service = parseFloat(reserve.summary.service).toFixed(2).replace(money_pattern,"$1,");
			var vat = parseFloat(reserve.summary.vat).toFixed(2).replace(money_pattern,"$1,");
			
			var item = "<div class='row'>";
			item += "<div class='col-md-3'>"+pages.message.service+"</div>";
			item += "<div class='col-md-2 '>&nbsp;</div>";
			item += "<div class='col-md-offset-4 col-md-3 text-right'><span><h4>฿ "+service+"</h4></span></div>";
			item += "</div>";
			
			item += "<div class='row'>";
			item += "<div class='col-md-3'>"+pages.message.tax+"</div>";
			item += "<div class='col-md-2'>&nbsp;</div>";
			item += "<div class='col-md-offset-3 col-md-4 text-right'><span><h4>฿ "+vat+"</h4></span></div>";
			item += "</div>";

			item += "<div class='row'>";
			item += "<div class='col-md-3 '><h4>"+pages.message.summary+"</h4></div>";
			item += "<div class='col-md-offset-6 col-md-3 text-right'><span><h4>฿ "+sum+"</h4></span></div>";
			item += "</div><hr>";
			$('#list_reserve').append(item);
		}
		
		//list option
		if(reserve.options != undefined){

			var item = "";
			var price_option = 0;
			$.each(reserve.options,function(i,val){
				//summary_price += parseFloat(val.price);
				price_option += parseFloat(val.price);

				var money = parseFloat(val.price).toFixed(2).replace(money_pattern,"$1,");	

				item += "<div class='row'>";
				item += "<div class='col-md-2'>&nbsp;</div>";
				item += "<div class='col-md-7'>"+val.title+"<br/>"+val.desc+"</div>";
				item += "<div class='col-md-3 text-right'>"+money+"</div>";
				item += "</div>";
			})
			//summary options
			var money = parseFloat(price_option).toFixed(2).replace(money_pattern,"$1,");	
			var summary_tag = "<div class='row'>";
			summary_tag += "<div class='col-md-2'>"+pages.message.extra+"</div>";
		  	summary_tag += "<div class='col-md-7'>&nbsp;</div>";
		   	summary_tag += "<div class='col-md-3 text-right'><h4>฿ "+money+"</h4></div>";
			summary_tag += "</div>";
			item = summary_tag + item + "<hr/>";

			$('#list_reserve').append(item);
		}

		//summary net price 
		var net = parseFloat(reserve.summary.net).toFixed(2).replace(money_pattern,"$1,");
		var item = "<div class='row rowspan'>";
		item += "<div class='col-md-offset-2 col-md-7'><pre><h4>"+pages.message.total_payment+"</h4></pre></div>";
		item += "<div class='col-md-3 text-right'><pre><h4>฿ "+net+"</h4></pre></div>";
		item += "</div>";

		$('#list_reserve').append(item);

	},callback);
}

reserve.get_receipt = function(){
	
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
		
			//define information
		if(result.data !== undefined){
			
			rooms = result.data.rooms;
			options = result.data.options;
			summary = result.data.summary;
			//console.log(summary);
		}
		
		
		if(rooms!==null){
			
			$.each(rooms,function(i,val){
				console.log("price="+val.price);
				summary_price += parseFloat(val.price);
				var money = parseFloat(val.price).toFixed(2).replace(money_pattern,"$1,");	
				var item = "<div class='row rowspan'>";
				 item += "<div class='col-md-2'><b>Room "+(i+1)+"</b><br/></div>";
				 item += "<div class='col-md-7'>"+val.type+"<br/>"+pages.message.bedding+"  "+val.bed_name+"  <br/>"+val.room + "</div>";
				 item += "<div class='col-md-3 text-right'><h4>฿ "+money+"</h4></div>";
				 item += "</div>";
				
				$('#list_reserve').append(item);
			});
		}
			
		if(summary!=null){
			
			
			var sum = parseFloat(summary.sum);	
			var service = parseFloat(summary.service) ;
			var vat = parseFloat(summary.vat);
			var net = parseFloat(summary.net);
			sum = parseFloat(sum).toFixed(2).replace(money_pattern,"$1,");
			service = parseFloat(service).toFixed(2).replace(money_pattern,"$1,");
			vat = parseFloat(vat).toFixed(2).replace(money_pattern,"$1,");
			net = parseFloat(net).toFixed(2).replace(money_pattern,"$1,");
			
			//console.log("service="+service_price);
			
			var item = "<div class='row rowspan'>";
			item += "<div class='col-md-2'><b>&nbsp;</b><br/></div>";
			item += "<div class='col-md-7'><br/>";
			item += "<p>"+pages.message.service_no_include+"</p>";
			item += "<p>"+pages.message.tax_no_include+"</p>";
			item += "<p>"+pages.message.summary+":</p>";
			item += "</div>";
			item += "<div class='col-md-3 text-right'><br/>";
			item += "<p><h4>฿ "+service+"</h4></p>";
			item += "<p><h4>฿ "+vat+"</h4></p>";
			item += "<p><h4>฿ "+sum+"</h4></p>";
			//item += "<p>฿ "+net+"</p>";
			item += "</div>";
			item += "<hr>";
			
			$('#list_reserve').append(item);
		}
			
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
				item += "<div class='col-md-3 text-right'><i>฿ "+money+"</i></div>";
				item += "</div>";
			});
			
			//summary options
			var money = parseFloat(price_option).toFixed(2).replace(money_pattern,"$1,");	
			var summary_tag = "<div class='row'>";
			summary_tag += "<div class='col-md-2'><b>"+pages.message.extra+"</b></div>";
			summary_tag += "<div class='col-md-7'>&nbsp;</div>";
			summary_tag += "<div class='col-md-3 text-right'><h4>฿ "+money+"</h4></div>";
			summary_tag += "</div>";
			item = summary_tag + item + "<hr>";

			$('#list_reserve').append(item);
		}
		
		if(summary!=null){
			
			var net = parseFloat(summary.net);
			net = parseFloat(net).toFixed(2).replace(money_pattern,"$1,");
			
			var item = "<div class='row rowspan'>";
			item += "<div class='col-md-2'><b>&nbsp;</b><br/></div>";
			item += "<div class='col-md-7'><br/>";
			item += "<p>"+pages.message.total_payment+"</p>";
			item += "</div>";
			item += "<div class='col-md-3 text-right'><br/>";
			item += "<p><h4>฿ "+net+"</h4></p>";
			item += "</div>";
			item += "<hr>";
			
			$('#list_reserve').append(item);
		}
			
			
		
	
		
		
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
