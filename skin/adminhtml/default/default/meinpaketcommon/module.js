function startProcessing() {
	var form = $('allyouneed_form');
	//$('startButton').hide();
	$('disabled-till-export').show();

	$$('li.nr1').each(function(aElement) {
		aElement.removeClassName('active');
	});

	$$('li.nr2').each(function(aElement) {
		aElement.addClassName('active');
	});

	$('allyouneed-description').hide();
	form.submit();
	form.disable();
	//form.hide();
}

function setLocationWithWait(gotourl) {
	setLocation(gotourl);
	$('disabled-till-export').show();
}

function submitForm(actionUrl) {
	var form = $('allyouneed_form');
	form.writeAttribute('action', actionUrl);
	form.submit();

}

function showResponse(req) {
	alert(req.responseText);
}
