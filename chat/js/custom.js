/*
 * @package AJAX_Chat
 * @author Sebastian Tschan
 * @copyright (c) Sebastian Tschan
 * @license GNU Affero General Public License
 * @link https://blueimp.net/ajax/
 */

// Overriding client side functionality:

/*

// Example - Overriding the replaceCustomCommands method:
ajaxChat.replaceCustomCommands = function(text, textParts) {
    return text;
}


    ajaxChat.sendMessageWrapper('/list');

 */
 ajaxChat.customInitialize = function() {
    ajaxChat.addChatBotMessageToChatList('Witamy na OpenCaching PL ChatBox. Użyj /who aby wylistować kto jest online, użyj komendy /list aby otrzymać wykaz kanłów i kliknij myszką na nazwę kanału aby wybrać go');
    }

