(function($) {

	// Pseudo Random String, change the text to change the random generator, no string for complete random design at every refresh

	var rng = new RNG('Voorhoede 6');
	//var rng = new RNG();

	var planeContainer = $('.bg-planes');
	var classPlaneCenter = 'bg-plane-large';
	var classPlaneSide = ['bg-plane-medium','', '', '', ''];
	var classesColor = ['yellow','red','blue','yellow-light','red-light','blue-light'];
	var classPlaneCenterColor = 'green-light';

	/*
	 Dimensions in EM
	 */

	var avgMiddlePlaneDistance = 70;
	var middlePlaneYOffset = 10;
	var middlePlaneXCenter = -20;
	var middlePlaneXOffset = 10;

	var avgSidePlaneDistance = 40;
	var sidePlaneYOffset = 8;
	var sidePlaneXCenterLeft = -55;
	var sidePlaneXCenterRight = -55;
	var sidePlaneXOffset = 6;


	// get the dimensions of the page
	var _docHeight = (document.height !== undefined) ? document.height : document.body.offsetHeight;

	var middlePlaneCount = Math.round(_docHeight / (getFontSize("body") * avgMiddlePlaneDistance));
	var sidePlaneCount = Math.round(_docHeight / (getFontSize("body") * avgSidePlaneDistance));


	// remove existing elements from the background container
	while(planeContainer.firstChild) {
		planeContainer.removeChild(planeContainer.firstChild);
	}

	// center elements
	for (var i = -1; i <= middlePlaneCount; i++) {
		var centerY = i * avgMiddlePlaneDistance;
		var posX = randomOffset(middlePlaneXCenter, middlePlaneXOffset);
		var posY = randomOffset(centerY, middlePlaneYOffset);
		var element = newBGElement(posX, posY, classPlaneCenterColor , classPlaneCenter,'left');
		planeContainer.append(element);
	}

	// left elements
	for (var i = -1; i <= sidePlaneCount; i++) {
		var centerY = i * avgSidePlaneDistance;
		var element = newBGElement(
			randomOffset(sidePlaneXCenterLeft, sidePlaneXOffset),
			randomOffset(centerY, sidePlaneYOffset),
			getRandomItemFromArray(classesColor),
			getRandomItemFromArray(classPlaneSide),
			'left'
		);
		planeContainer.append(element);
	}

	// right elements
	for (var i = -1; i <= sidePlaneCount; i++) {
		var centerY = i * avgSidePlaneDistance;
		var posX = randomOffset(sidePlaneXCenterRight, sidePlaneXOffset);
		var posY = randomOffset(centerY, sidePlaneYOffset);
		var color = getRandomItemFromArray(classesColor);
		var size = getRandomItemFromArray(classPlaneSide);
		var element = newBGElement(posX, posY, color , size, 'right');
		planeContainer.append(element);
	}

	function getRandomItemFromArray(array) {
		return array[Math.floor(rng.random() * array.length)];
	}

	function getFontSize(el) {
		var x = document.getElementsByTagName(el)[0];
		if (x.currentStyle) {
			// IE
			var y = x.currentStyle['fontSize'];
		} else if (window.getComputedStyle) {
			// FF, Opera
			var y = document.defaultView.getComputedStyle(x,null).getPropertyValue('font-size');
		}
		return y.replace('px','');
	}

	function newBGElement(posX, posY, color, size, anchor) {

		var newBGItem = document.createElement('div');
		newBGItem.className = "bg-plane";
		newBGItem.className += " " + size ;
		newBGItem.className += " " + color;
		if (anchor === 'right') {
			newBGItem.style.right = posX + 'em';
		} else {
			newBGItem.style.left = posX + 'em';
		}
		newBGItem.style.top = posY + 'em';

		return newBGItem;
	}

	function randomOffset(center,offset) {
		var min = center - offset;
		var max = center + offset;
		return Math.floor(rng.random() * (max - min + 1)) + min;
	}


})(('undefined' == typeof Zepto) ? jQuery : Zepto);

