<?php
namespace Controllers;


use lib\Objects\User\User;
use Utils\Uri\Uri;
use lib\Objects\User\UserMessage;

class UserProfileController extends BaseController
{

    /** @var User $requestedUser */
    private $requestedUser;

    public function __construct()
    {
        parent::__construct();
        $this->requestedUser = $this->loadRequestedUser();
    }

    public function index()
    {
        // there is nothing here yet
    }

    public function mailTo()
    {
        if(!$this->loggedUser){
            // this view is only for authorized user
            $this->redirectToLoginPage();
        }

        tpl_set_tplname('userProfile/mailto');

        $this->view->setVar('mailto_css',
            Uri::getLinkWithModificationTime('tpl/stdstyle/userProfile/mailto.css'));

        $this->view->setVar('requestedUser', $this->requestedUser);

        $sendAction = isset($_REQUEST['sendEmailAction']);
        $displaySubjectError = $sendAction &&
            (!isset($_REQUEST['mailSubject']) || empty($_REQUEST['mailSubject']));
        $displayTextError = $sendAction &&
            (!isset($_REQUEST['mailText']) || empty($_REQUEST['mailText']));

        if(!$sendAction){
            $mailSubject = '';
            $mailText = '';
            $attachEmailAddress = false;
        }else{
            $mailSubject = (!$displaySubjectError) ? strip_tags($_REQUEST['mailSubject']) : '';
            $mailText = (!$displayTextError) ? strip_tags($_REQUEST['mailText']) : '';
            $attachEmailAddress = isset($_REQUEST['attachEmailAddress']);
        }

        $formDisabled = false;
        $infoMsg = null;
        $errorMsg = null;
        if(!$this->requestedUser){
            //disable form if there is no recipient! - it should never happen.
            $formDisabled = true;
            $errorMsg = 'No recipient selected? Try to send mail again and if it happens contact site admins.';

        }else{
            if($sendAction && !$displaySubjectError && !$displayTextError){

                if(! isset($_SESSION['mailTo-mail-send']) ){
                    UserMessage::SendUserMessage($this->loggedUser, $this->requestedUser, $mailSubject, htmlspecialchars($mailText), $attachEmailAddress);
                }
                $formDisabled = true; //disable form on send with no-errors
                $infoMsg = tr('mailto_messageSent');

                $_SESSION['mailTo-mail-send'] = true; //set marker that mail was sent - prevents resend by page refresh

            }else{
                if($displaySubjectError){
                    $errorMsg = tr('mailto_lackOfSubject');
                }elseif ($displayTextError){
                    $errorMsg = tr('mailto_lackOfText');
                }
            }
        }

        if(!$formDisabled && isset($_SESSION['mailTo-mail-send'])){
            unset($_SESSION['mailTo-mail-send']);
        }

        $this->view->setVar('mailSubject', $mailSubject);
        $this->view->setVar('mailText', $mailText);
        $this->view->setVar('attachEmailAddress', $attachEmailAddress);

        $this->view->setVar('displaySubjectError', $displaySubjectError);
        $this->view->setVar('displayTextError', $displayTextError);

        $this->view->setVar('formDisabled', $formDisabled);

        $this->view->setVar('errorMsg', $errorMsg);
        $this->view->setVar('infoMsg', $infoMsg);
        $this->view->setVar('reloadUrl', '');

        tpl_BuildTemplate();
    }

    private function loadRequestedUser()
    {
        if (isset($_REQUEST['userid'])) {
            return User::fromUserIdFactory($_REQUEST['userid']);
        }
        return null;
    }
}


