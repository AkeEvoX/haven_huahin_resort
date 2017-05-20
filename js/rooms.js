var rooms = {};

rooms.filter_room = function(startdate,enddate){

	var start_date = "";
	var end_date = "";

	var endpoint = "services/rooms.php";
	var method="get";
	var args = {"_":new Date().getMilliseconds()
	,"service":"filter"
	,"startdate":startdate
	,"enddate":enddate};
	
	utility.service(endpoint,method,args,view_list_room);


};

function view_list_room(resp){
	var rooms = "";
	
	if(resp.data!=null){

		console.debug(resp.data);

		// var templete_master = "";
		// var templete = "";
		// templete_master = utility.get_templete("../templete_room.html");
		
		// templete = templete_master.replace("{room_name}","deluxe");

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
		//$('#list_room').append(templete);
		//resp.rooms
		
		//$('#list_room').append(templete);
		
		
	}
	
	
	
}