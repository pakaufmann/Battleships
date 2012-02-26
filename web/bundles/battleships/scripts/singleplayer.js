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
	
	
	$("#userField td").click(function() {
		var coords = getCoords(this);
		var valid = true;
		
		//check if valid
		onFields(this, function(x, y, i) {
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
		clickedTd = this;
		$.getJSON("singleplayer/addShip/" + coords.x + "/" + coords.y + "/" + shipLength + "/" + orientation, function(data) {
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
		
	}).hover(function() {
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

function selectShip() {
	$("#ships input").removeAttr("checked");
	$("#ships input").not("[disabled]").first().attr("checked", "checked");
	$("#ships").buttonset("refresh");
}

/**
 * checks if all ships have been set, if yes calls serverside
 */
function checkShips() {
	if($("#ships input:disabled").size() == 5) {
		$.getJSON("singleplayer/startGame", function(data) {
			if(data.success) {
				//disable userfield
				$("#userField td").unbind("click").unbind("hover");
				
				//add click and hover handler to ai field
				$("#aiField td").click(aiFieldClick).hover(function() {
					$(this).addClass("shoot");
				}, function() {
					$(this).removeClass("shoot");
				});
			} else {
				messageBar("A serverside error occured", "error");
			}
		});
	}
}

/**
 * handles click on ai field
 */
function aiFieldClick() {
	//check if field already shot
	if($(this).hasClass("shot")) {
		messageBar("You have already shot on this field", "error");
		return;
	}
	
	xy = getCoords(this);
	
	var hitField = $(this);
	
	$.getJSON("singleplayer/shoot/" + xy.x + "/" + xy.y, function(data) {
		if(data.success) {
			if(data.alreadyHit) {
				messageBar("You have already hit this field!", "warning");
			}
			if(data.hit) {
				hitField.addClass("hit").addClass("shot");
			} else {
				hitField.addClass("shot");
			}
			$.each(data.aiHitFields, function(i, f) {
				var x = f.x;
				var y = f.y;
				var hitField = $("#userField_" + x + "_" + y);
				
				hitField.addClass("shot");
				
				if(hitField.hasClass("shipAdded")) {
					hitField.addClass("hit");
				}
			});
		} else {
			messageBar("the server returned an error", "error");
		}
	});
}

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