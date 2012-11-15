var gpbp_dont_toggle = false;
/** PostVoting object. */
function PostVoting(oOptions)
{
	this.opt = oOptions;
	this.sMessageId = '';
	this.sPosterId = '';
	this.bXmlHttpCapable = typeof(window.XMLHttpRequest) == 'undefined' ? false : true;
}

// Send a vote.
PostVoting.prototype.vote = function (sVoteDirection, iMessageId)
{
	if (!this.bXmlHttpCapable)
		return false;

	// For IE 5.0 support, 'call' is not yet used.
	this.tmpMethod = getXMLDocument;
	this.tmpMethod(smf_prepareScriptUrl(this.opt.sScriptUrl) + 'action=gpbp;sa=' + sVoteDirection + ';msg=' + iMessageId + ';' + this.opt.sSessionVar + '=' + this.opt.sSessionId + ';board=' + this.opt.iBoard + ';topic=' + this.opt.iTopic + ';xml', this.onVoteCasted);
	delete this.tmpMethod;
}

// The callback function used for the XMLhttp request sending the post vote.
PostVoting.prototype.onVoteCasted = function(XMLDoc)
{
	// Prepare the values to update the DOM.
	// Which post was it again?
	this.sMessageId = XMLDoc.getElementsByTagName('message')[0].childNodes[0].nodeValue;
	// Succeeded? What is the result (up, not up, down, not down)?
	var outputTitle = '';
	var outputImage = '';
	var litArrowStatus = true;
	var updateOpposite = XMLDoc.getElementsByTagName('opposite')[0].childNodes[0].nodeValue;
	switch (XMLDoc.getElementsByTagName('success')[0].childNodes[0].nodeValue)
	{
		case 'down':
			outputTitle = this.opt.sVotedDown;
			outputAlt = this.opt.sVotedDownAlt;
			outputImage = 'down';
			break;
		case 'not down':
			outputTitle = this.opt.sToVoteDown;
			outputAlt = this.opt.sVoteDownAlt;
			outputImage = 'down';
			litArrowStatus = false;
			break;
		case 'up':
			outputTitle = this.opt.sVotedUp;
			outputAlt = this.opt.sVotedUpAlt;
			outputImage = 'up';
			break;
		case 'not up':
			outputTitle = this.opt.sToVoteUp;
			outputAlt = this.opt.sVoteUpAlt;
			outputImage = 'up';
			litArrowStatus = false;
			break;
	}
	// What is the new vote count for this message?
	var outputCounter = XMLDoc.getElementsByTagName('counter')[0].childNodes[0].nodeValue;
	if (outputCounter > 0)
		outputCounter = '+' + outputCounter;
	this.sPosterId = XMLDoc.getElementsByTagName('who')[0].childNodes[0].nodeValue;

	// Do the DOM dance! Update respect counter(s), the vote counter, and the button image and its title.
	if (this.sPosterId != 0)
	{
		// Not a guest, so whose Respect got affected and what is his new Respect counter?
		var outputRespect = XMLDoc.getElementsByTagName('who')[0].getAttribute('respect');
		if (outputRespect > 0)
			outputRespect = '+' + outputRespect;
		// Attempt to modify each of this person's counters in the page.
		this.j = document.getElementsByTagName("span");
		for (var i = this.j.length - 1; i >= 0; i--)
			if (this.j[i].className == 'gpbp_respect_count_' + this.sPosterId)
				setInnerHTML(this.j[i], outputRespect);
		delete this.j;
	}
	// Look for the vote counter.
	this.j = document.getElementsByTagName("span");
	for (var i = this.j.length - 1; i >= 0; i--)
	{
		if (this.j[i].id == 'gpbp_score_' + this.sMessageId)
		{ 
			setInnerHTML(this.j[i], outputCounter);
			break;
		}
	}
	delete this.j;
	for (var i = document.images.length - 1; i>=0; i--)
	{
		this.gotcha = 0;
		// Also update the opposite arrow if needed.
		if (document.images[i].id == 'gpbp_vote_' + outputImage + '_' + this.sMessageId)
		{
			document.images[i].setAttribute("src", this.createImageURL(outputImage, litArrowStatus ? '_lit' : '' ));
			document.images[i].setAttribute("title", outputTitle);
			document.images[i].setAttribute("alt", outputAlt);
			this.gotcha++;
		}
		else if (updateOpposite == 1 && (document.images[i].id == 'gpbp_vote_' + (outputImage == 'up' ? 'down' : 'up') + '_' + this.sMessageId))
		{
			document.images[i].setAttribute("src", this.createImageURL(outputImage == 'up' ? 'down' : 'up', litArrowStatus ? '' : '_lit'));
			document.images[i].setAttribute("title", outputImage == 'up' ? this.opt.sToVoteDown : this.opt.sToVoteUp);
			document.images[i].setAttribute("alt", outputImage == 'up' ? this.opt.sVoteDownAlt : this.opt.sVoteUpAlt);
			this.gotcha++;
		}
		if (this.gotcha == 1 + updateOpposite)
		{
			delete this.gotcha;
			break;
		}
	}

	gpbp_dont_toggle = true;

	return true;
}

// Request list of post voters.
PostVoting.prototype.voterslist = function (iMessageId)
{
	if (!this.bXmlHttpCapable)
		return false;

	ajax_indicator(true);
	this.tmpMethod = getXMLDocument;
	this.tmpMethod(smf_prepareScriptUrl(this.opt.sScriptUrl) + 'action=gpbp;sa=voterslist;msg=' + iMessageId + ';' + this.opt.sSessionVar + '=' + this.opt.sSessionId + ';board=' + this.opt.iBoard + ';topic=' + this.opt.iTopic + ';xml', this.onVotersRequest);
	delete this.tmpMethod;
}

// The callback function used for the XMLhttp request sending the list of voters.
PostVoting.prototype.onVotersRequest = function(XMLDoc)
{
	// Prepare the values to update the DOM.
	// Which post was it again?
	this.sMessageId = XMLDoc.getElementsByTagName('message')[0].childNodes[0].nodeValue;
	// Hide the list display button
	document.getElementById('gpbp_voterslist_' + this.sMessageId).style.display = 'none';
	// Fill the select up.
	var oSelect = document.getElementById('gpbp_voters_select_' + this.sMessageId);
	var voters = XMLDoc.getElementsByTagName('voter');
	for (i = 0; i < voters.length; i++)
	{
		oSelect.options[i+1] = new Option( ( !is_ff ? ( parseInt(voters[i].getAttribute('score')) > 0 ? "+ " : "- " ) : '') + voters[i].childNodes[0].nodeValue, voters[i].getAttribute('id'), false, false);
		oSelect.options[i+1].className = 'gpbp_voted_' + ( parseInt(voters[i].getAttribute('score')) > 0 ? "up" : "down" );
	}
	// No voters... say something at least!
	if (voters.length == 0)
	{
		oSelect.options[1] = new Option ( this.opt.sNoVoters, 0, false, false);
		oSelect.options[1].disabled = true;
	}
	// Show the select.
	oSelect.style.display = 'inline';
	ajax_indicator(false);
}

// Returns a voting button image URL, given the direction ('up' || 'down) and lit status suffix ('' || '_lit'). 
PostVoting.prototype.createImageURL = function(direction, litSuffix)
{
	// Image extension fix (for backwards compatibility)
	var imageExt = this.opt.sButtonSet != '' ? 'png' : 'gif';
	// Return the generated URL.
	return this.opt.sImagesUrl + '/gpbp' + this.opt.sButtonSet + '_arrow_' + direction + litSuffix + '.' + imageExt
}

/** PostVoting object end. */

// Display a hidden post.
function gpbp_show_post(iMessageId, hide_again)
{
	var current = document.getElementById('gpbp_hidden_' + iMessageId);
	// The "Post Hidden" bar must be hidden, by removing its gpbp_do_show class and using an inline style.
	gpbp_RemoveClassRecursive(current, 'gpbp_do_show', true);
	// Now to display the post itself, remove the gpbp_hider class from any element within.
	gpbp_RemoveClassRecursive(current, 'gpbp_hider', false);
	
	return false;
}

// Hide a revealled post... again.
function gpbp_hide_post_again(iMessageId)
{
	var current = document.getElementById('gpbp_hidden_' + iMessageId);
	current.className += ( current.className == '' ? '' : ' ' ) + 'gpbp_hider';
	var current = document.getElementById('gpbp_hidden_notice_' + iMessageId);
	current.className += ( current.className == '' ? '' : ' ' ) + 'gpbp_do_show';
}

// Recursive class lookup+removal. Meant to support any theme, if it's WC3-compliant at least...
function gpbp_RemoveClassRecursive(current, targetClass, hide_inline)
{
	if (current == null)
		return;

	// Class sweeping.
	if (current.className != undefined && current.className != '')
	{
		names = current.className.split(" ");
		found = [];
		var gotcha = false;
		// This will hopefully help get rid of the target class from any element.
		for (var i = names.length - 1; i >= 0; i--)
			if (names[i] == targetClass)
				gotcha = true;
			else
				found[found.length] = names[i];

		// Is there a NEED to tweak its class? 
		if (gotcha)
		{
			current.className = '';
			for (var i = 0, l = found.length; i < l; i++)
				current.className += (i > 0 ? ' ' : '') + found[i];

			// If required, hide this element via inline property.
			if (hide_inline)
				current.style.display = 'none';
		}

		// For efficiency's sake, and browser healthyness!
		delete names;
		delete found;
	}

	// Do the same for any and every descendant element.
	var i = 0, currentChild = current.childNodes[i];
	while (currentChild != null)
	{
		gpbp_RemoveClassRecursive(currentChild, targetClass, hide_inline);
		i++;
		currentChild = current.childNodes[i];
	}
}

// Redirect to a member's profile
function gpbp_toProfile(chosen_select) {
	var member_id = document.getElementById(chosen_select).options[document.getElementById(chosen_select).selectedIndex].value;
	if (parseInt(member_id) == 0)
		return; 
	window.location = smf_scripturl + "?action=profile;u=" + member_id;
}

// Toggles icon images in voting buttons on hover.
function gpbp_toggleVotingIcon(chosen_image) 
{
	var voting_button = document.getElementById(chosen_image);
	//find out the corresponding direction and suffix from the current url
	var image_filename = voting_button.src.slice(voting_button.src.lastIndexOf("/") + 1);
	// Make sure icons are updated only when they should (i.e. not after changing a vote).
	if (! gpbp_dont_toggle)
		voting_button.src = oVotePost.createImageURL(image_filename.search(/_down/) == -1 ? "up" : "down" , image_filename.search(/_lit/) == -1 ? "_lit" : "");
	gpbp_dont_toggle = false;
}