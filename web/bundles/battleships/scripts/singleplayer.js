/**
 * checks if all ships have been set, if yes calls serverside
 */
function checkShips() {
	globalCheckShips("singleplayer", function(data) {
		//disable userfield
		$("#userField td").unbind("click").unbind("hover");
		
		//add click and hover handler to ai field
		$("#aiField td").click(aiFieldClick).hover(function() {
			$(this).addClass("shoot");
		}, function() {
			$(this).removeClass("shoot");
		});
		
		//check if reload and somebody already won the game
		checkWon(data);
	});
}

/**
 * handles click on ai field
 */
function aiFieldClick(event) {
	var hitField = $(this);
	var mouseX = event.pageX;
	var mouseY = event.pageY;
	
	return globalFieldClick("singleplayer", event, this, function(data) {
		//check if anybody won
		checkWon(data);
		
		if(data.alreadyHit) {
			messageBar("You have already hit this field!", "warning");
		}
		
		//if we hit anything, show it
		if(!data.alreadyHit) {
			if(data.hit) {
				shootAnimation(hitField, mouseX, mouseY, "hit shot");
				hitField.addClass("hit").addClass("shot");
			} else {
				shootAnimation(hitField, mouseX, mouseY, "shot");
				hitField.addClass("shot");
			}
		}
		//for each shot ai field, show it on the gamefield
		shootFields(data.aiHitFields);
	});
}

function checkWon(data) {
	if(data.userWon) {
		messageBar("Congrats, you've won the game", "success", 5000);
		$("#aiField td").unbind("click").unbind("hover");
	}
	if(data.aiWon) {
		messageBar("You've lost the game", "error", 5000);
		$("#aiField td").unbind("click").unbind("hover");
	}
}

function addUserShip() {
	return globalAddUserShip("singleplayer", this);
}