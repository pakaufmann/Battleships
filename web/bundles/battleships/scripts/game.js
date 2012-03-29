var orientation = 1;
var shipLength = 5;

$(function() {
	$("#ships").add("#orientation").buttonset();
	
	//check state
	selectShip();
	checkShips();
	
	//load initial values
	shipLength = $("#ships input:checked").attr("value");
	orientation = $("#orientation input:checked").attr("value");
	
	$("#userField td").click(addUserShip).hover(function() {
		onFields(this, function(x, y, i) {
			$("#userField_" + x + "_" + (y + i)).addClass("addShip");
		}, function(x, y, i) {
			$("#userField_" + (x + i) + "_" + y).addClass("addShip");
		});
	}, function() {
		onFields(this, function(x, y, i) {
			$("#userField_" + x + "_" + (y + i)).removeClass("addShip");
		}, function(x, y, i) {
			$("#userField_" + (x + i) + "_" + y).removeClass("addShip");
		});
	});
	
	$("#orientation input").click(function() {
		orientation = $("#orientation input:checked").attr("value"); 
	});
	
	$("#ships input").click(function() {
		shipLength = $("#ships input:checked").attr("value");
	});
});

function globalAddUserShip(gameType, field) {
	var coords = getCoords(field);
	var valid = true;
	
	//check if valid
	onFields(field, function(x, y, i) {
		if($("#userField_" + x + "_" + (y + i)).size() == 0 ||
		   $("#userField_" + x + "_" + (y + i)).hasClass("shipAdded")) {
			valid = false;
		}
	}, function(x, y, i) {
		if($("#userField_" + (x + i) + "_" + y).size() == 0 ||
		   $("#userField_" + (x + i) + "_" + y).hasClass("shipAdded")) {
			valid = false;
		}
	});
	
	if(!valid) {
		messageBar("This position is not valid", "error");
		return;
	}
	
	//check if already set
	if($("#ships input[value=" + shipLength + "]").button("option", "disabled")) {
		messageBar("This Ship is already set!", "error");
		return;
	}
	
	//add the ship also on the serverside
	clickedTd = field;
	$.getJSON(gameType + "/addShip/" + coords.x + "/" + coords.y + "/" + shipLength + "/" + orientation, function(data) {
		if(!data.success) {
			messageBar("There was a server error, the ship could not be set there", "error");
		} else {
			$("#ships input[value=" + shipLength + "]").button("option", "disabled", true);
			onFields(clickedTd, function(x,y,i) {
				$("#userField_" + x + "_" + (y + i)).addClass("shipAdded");
			}, function(x,y,i) {
				$("#userField_" + (x + i) + "_" + y).addClass("shipAdded");
			});
			
			messageBar("Valid position", "info");
			
			//move to next ship which is not set and check it
			selectShip();
			shipLength = $("#ships input:checked").attr("value");
			
			//check if all ships have been set, if yes, call serverside
			checkShips();
		}
	});
}

function getCoords(field) {
	var xy = $(field).attr("id").split("_");
	var obj = {};
	obj["x"] = parseInt(xy[1]);
	obj["y"] = parseInt(xy[2]);
	
	return obj;
}

function messageBar(message, messageType, delay) {
	if(delay == null) {
		delay = 1500;
	}
	$("#message")
	.stop(true).clearQueue().removeClass()
	.addClass(messageType).html(message)
	.slideDown('slow').delay(delay)
	.slideUp(function() {$(this).removeClass(messageType) });
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

function shootAnimation(hitField, x, y, classes) {
	var posTop = y - hitField.height()/2;
	var posLeft = x - hitField.width()/2;
	$("html").append("<div id=\"tempShot\" class=\"field " + classes + "\" style=\"position: absolute; top: " + posTop + "px; left: " + posLeft + "px;\"></div>");
	
	//whileloop needs to be here, because sometimes the animation is run before the append is
	//completed, leading to a round dot somewhere on the screen
	var animationRun = false;
	while(!animationRun) {
		if($("#tempShot").size() == 0) {
			continue;
		}
		
		animationRun = true;
		$("#tempShot").effect("explode", 1000, function() {
			$(this).remove();
			$("#tempShot").remove();
		});
	}
}

function selectShip() {
	$("#ships input").removeAttr("checked");
	$("#ships input").not("[disabled]").first().attr("checked", "checked");
	$("#ships").buttonset("refresh");
}

function globalCheckShips(gameType, jsonFunction) {
	if($("#ships input:disabled").size() == 5) {
		$.getJSON(gameType + "/startGame", function(data) {
			if(data.success) {
				jsonFunction(data);
			} else {
				messageBar("A serverside error occured", "error");
			}
		});
	}
}

function shootFields(fields) {
	$.each(fields, function(i, f) {
		var x = f.x;
		var y = f.y;
		var hitField = $("#userField_" + x + "_" + y);
		
		if(!hitField.hasClass("shot")) {
			hitField.addClass("shot");
			
			if(hitField.hasClass("shipAdded")) {
				hitField.addClass("hit");
			}
		}
	});
}

function globalFieldClick(gameType, event, hitField, jsonFunction) {
	//get mouse position (for animation, because jquery can't handle the rotate properly)
	var mouseX = event.pageX;
	var mouseY = event.pageY;
	
	//check if field already shot
	if($(hitField).hasClass("shot")) {
		messageBar("You have already shot on this field", "error");
		return;
	}
	
	xy = getCoords(hitField);
	
	$.getJSON(gameType + "/shoot/" + xy.x + "/" + xy.y, function(data) {
		if(data.success) {
			jsonFunction(data);
		} else {
			messageBar("the server returned an error", "error");
		}
	});
}