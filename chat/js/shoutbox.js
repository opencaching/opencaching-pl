/*
 * @package AJAX_Chat
 * @author Sebastian Tschan
 * @copyright (c) Sebastian Tschan
 * @license GNU Affero General Public License
 * @link https://blueimp.net/ajax/
 */

// Overrides functionality for the shoutbox view:

	ajaxChat.handleLogout = function() {
	}

ajaxChat.addMessageToChatList = function(dateObject, userID, userName, userRole, messageID, messageText, channelID, ip) {
// Prevent adding the same message twice:
if(this.getMessageNode(messageID)) {
return;
}
if(!this.onNewMessage(dateObject, userID, userName, userRole, messageID, messageText, channelID, ip)) {
return;
}
this.updateDOM('chatList', this.getChatListMessageString(dateObject, userID, userName, userRole, messageID, messageText, channelID, ip),true)
// This prepends the message instead of appending it to the list

}

ajaxChat.updateChatlistView = function() {
if(this.dom['chatList'].childNodes && this.settings['maxMessages']) {
while(this.dom['chatList'].childNodes.length > this.settings['maxMessages']) {
// Remove the last child for reverse scroll (instead of the first child):
this.dom['chatList'].removeChild(this.dom['chatList'].lastChild);
//this.dom['chatList'].removeChild(this.dom['chatList'].firstChild);
}
}

if(this.settings['autoScroll']) {
// Always scroll to the top for reverse scroll:
this.dom['chatList'].scrollTop = 0;
//this.dom['chatList'].scrollTop = this.dom['chatList'].scrollHeight;
}
}
