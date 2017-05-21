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

function view_list_room(resp){
	var rooms = "";
	
	if(resp.data!=null){

		//console.debug(resp.data);

		var templete_master = "";
		// var templete = "";
		templete_master = utility.get_templete("templete_room.html");
		//console.log(templete_master);
		
		// templete = templete_master.replace("{room_name}","deluxe");
		$.each(resp.data,function(index){
			//console.log(resp.data[room]);
			var room = resp.data[index];
			var templete = templete_master;
			templete = templete.replace("{room_name}",room.id);
			templete = templete.replace("{bed_list}",set_bed_list(room.beds,room.title));
			templete = templete.replace("{package_list}",set_package_list(room.packages,room.title));
			$('#list_room').append(templete);
		});

		/*
		$.each(resp.data,function(room){

			rooms += "<div class='row'>";
			rooms += "<div class='panel panel-default'>";
			rooms += "<div class='panel-heading' style='background-color:black;'>";
			rooms += "<h3 class='panel-title' style='color:white;margin:10px 5px 10px 1px;'>"+room.name+"</h3>";
			rooms += "</div>";
			rooms += "";
			rooms += "";
			rooms += "";
			rooms += "";
			rooms += "";
			rooms += "";
			rooms += "";
			rooms += "";
			rooms += "";
			rooms += "";
			rooms += "";
			rooms += "";
			rooms += "";
		});
		templete = templete.repace("{}",row);
		*/


	}
}

function set_bed_list(items,room_name){

	var result = "<select class='form-control' id='type_bed_"+room_name+"' name='type_bed_"+room_name+"' >";
	$.each(items,function(index){
		var bed = items[index];
		result += "<option value='"+bed.id+"'>"+bed.title+"</option>";
	});

	result += "</select>";
	return result;
}

function set_package_list(items,room_name){
	var result = "";
	//console.warn(items);

	/*
<span href="#" class="list-group-item">
					<h4 class="list-group-item-heading">Room Only<span class='pull-right'>฿ 2,803.73</span></h4>
					<small class="list-group-item-text ">
						<div class='col-sm-3'><span class='glyphicon glyphicon-remove'></span> ไม่รวมอาหารเช้า</div>
						<div class='col-sm-3' onclick='reserve.modal_change()'; style='cursor:pointer;'><span class='glyphicon glyphicon-ok'></span> สามารถเปลี่ยนแปลงหรือยกเลิกได้</div>
						<div class='col-sm-3' onclick='reserve.modal_internet()'; style='cursor:pointer;'><span class='glyphicon glyphicon-credit-card'></span> ชำระเงินทางอินเตอร์เน็ต</div>
						<button onclick="add_room(4,'superior','Room Only','2803.73')" type='button' class='btn btn-default brown btn-sm col-sm-3'>เลือก</button>
						&nbsp;
					</small>
				  </span>

	*/
	if(items!=null){
		//console.log(items);
		$.each(items,function(index){
			var package = items[index];

			result+= "<span href="#" class="list-group-item">";			

			console.log(package);
		});
	}

	return result;
}