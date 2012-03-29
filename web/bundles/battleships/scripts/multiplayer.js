/**
 * checks if all ships have been set, if yes calls serverside
 */
function checkShips() {
	return globalCheckShips("multiplayer", function(data) {
		if(data.userReady) {
			//disable userfield
			$("#userField td").unbind("click").unbind("hover");
			
			if(data.isUserTurn) {
				//add click and hover handler to enemy field
				$("#enemyField td").click(enemyFieldClick).hover(function() {
					$(this).addClass("shoot");
				}, function() {
					$(this).removeClass("shoot");
				});
			} else {
				//start waiting for the opposite player
				waitForOppositePlayer();
			}
		} else {
			messageBar("The other user is not ready yet", "info");
			setTimeout("checkShips()", 10000);
		}
	});
}

function checkUserWon(data) {
	if(data.userWon) {
		messageBar("Congrats, you've won the game", "success", 5000);
		$("#aiField td").unbind("click").unbind("hover");
	}
}

/**
 * handles click on ai field
 */
function enemyFieldClick(event) {
	var hitField = $(this);
	var mouseX = event.pageX;
	var mouseY = event.pageY;
	
	return globalFieldClick("multiplayer", event, this, function(data) {
		//check if user has won
		checkUserWon(data);
		
		if(data.alreadyHit) {
			messageBar("You have already hit this field!", "warning");
		}
		
		//if we hit anything, show it
		if(!data.alreadyHit) {
			if(data.hit) {
				//if we hit something, we can proceed normally
				shootAnimation(hitField, mouseX, mouseY, "hit shot");
				hitField.addClass("hit").addClass("shot");
			} else {
				shootAnimation(hitField, mouseX, mouseY, "shot");
				hitField.addClass("shot");
				
				//remove this handler and start the procedure to wait for the enemy shot
				$("#enemyField td").unbind("click").unbind("hover");
				waitForOppositePlayer();
			}
		}
	});
}

function waitForOppositePlayer() {
	$.getJSON("multiplayer/checkOpposite", function(data) {
		if(!data.oppositeWon && !data.userWon) {
			if(!data.playerTurn) {
				messageBar("Opposite player still shooting", "info", 1000);
				setTimeout("waitForOppositePlayer()", 5000);
			} else {
				messageBar("It's your turn again", "info", 2000);
				$("#enemyField td").click(enemyFieldClick).hover(function() {
					$(this).addClass("shoot");
				}, function() {
					$(this).removeClass("shoot");
				});
			}
		}
		
		if(data.oppositeWon) {
			messageBar("The opposite player has won", "info", 5000);
		}
		
		checkUserWon(data);
		
		//actualize the player field
		shootFields(data.hitFields);
	});
}

function addUserShip() {
	return globalAddUserShip("multiplayer", this);	
}