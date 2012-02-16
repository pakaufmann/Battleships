var orientation = 1;
var shipLength = 5;

$(function() {
	$("#ships").add("#orientation").buttonset();
	
	//load initial values
	shipLength = $("#ships input:checked").attr("value");
	orientation = $("#orientation input:checked").attr("value");
	
	$("#userField td").click(function() {
		var coords = getCoords(this);
		var valid = true;
		
		//check if valid
		onFields(this, function(x, y, i) {
			if($("#field_" + x + "_" + (y + i)).size() == 0 ||
			   $("#field_" + x + "_" + (y + i)).hasClass("shipAdded")) {
				valid = false;
			}
		}, function(x, y, i) {
			if($("#field_" + (x + i) + "_" + y).size() == 0 ||
			   $("#field_" + (x + i) + "_" + y).hasClass("shipAdded")) {
				valid = false;
			}
		});
		
		if(!valid) {
			messageBar("This position is not valid", "error");
			return;
		}
		if($("#ships input[value=" + shipLength + "]").button("option", "disabled")) {
			messageBar("This Ship is already set!", "error");
			return;
		}
		
		$.getJSON("/singleplayer" + coords.x + "/" + coords.y + "/" + shipLength + "/" + orientation, function(response) {
			
		});
		
		$("#ships input[value=" + shipLength + "]").button("option", "disabled", true);
		onFields(this, function(x,y,i) {
			$("#field_" + x + "_" + (y + i)).addClass("shipAdded");
		}, function(x,y,i) {
			$("#field_" + (x + i) + "_" + y).addClass("shipAdded");
		});
		
		messageBar("Valid position", "info");
		
	}).hover(function() {
		onFields(this, function(x, y, i) {
			$("#field_" + x + "_" + (y + i)).addClass("addShip");
		}, function(x, y, i) {
			$("#field_" + (x + i) + "_" + y).addClass("addShip");
		});
	}, function() {
		onFields(this, function(x, y, i) {
			$("#field_" + x + "_" + (y + i)).removeClass("addShip");
		}, function(x, y, i) {
			$("#field_" + (x + i) + "_" + y).removeClass("addShip");
		});
	});
	
	$("#orientation input").click(function() {
		orientation = $("#orientation input:checked").attr("value"); 
	});
	
	$("#ships input").click(function() {
		shipLength = $("#ships input:checked").attr("value");
	});
});

function messageBar(message, messageType) {
	$("#message")
	.stop(true).clearQueue().removeClass()
	.addClass(messageType).html(message)
	.slideDown('slow').delay(1500)
	.slideUp(function() {$(this).removeClass(messageType) });
}

function getCoords(field) {
	var xy = $(field).attr("id").split("_");
	var obj = {};
	obj["x"] = parseInt(xy[1]);
	obj["y"] = parseInt(xy[2]);
	
	return obj;
}

function onFields(field, horizontalFunction, verticalFunction) {
	var coords = getCoords(field);
	
	for(var i=0;i<shipLength;i++) {
		if(orientation == 1) {
			horizontalFunction(coords.x, coords.y, i);
		} else {
			verticalFunction(coords.x, coords.y, i);
		}
	}
}