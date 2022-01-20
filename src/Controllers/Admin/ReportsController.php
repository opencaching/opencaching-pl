<?php

namespace src\Controllers\Admin;

use src\Controllers\BaseController;
use src\Controllers\LogEntryController;
use src\Models\Admin\Report;
use src\Models\Admin\ReportCommons;
use src\Models\Admin\ReportEmailSender;
use src\Models\Admin\ReportEmailTemplate;
use src\Models\Admin\ReportLog;
use src\Models\Admin\ReportPoll;
use src\Models\Admin\ReportWatches;
use src\Models\ChunkModels\PaginationModel;
use src\Models\GeoCache\GeoCache;
use src\Models\User\MultiUserQueries;
use src\Models\User\User;
use src\Utils\Uri\HttpCode;
use src\Utils\Uri\Uri;

class ReportsController extends BaseController
{
    private $infoMsg = null;

    private $errorMsg = null;

    public function isCallableFromRouter(string $actionName): bool
    {
        // all public methods can be called by router
        return true;
    }

    public function index()
    {
        // Check if user is logged and has admin rights
        if (! $this->isUserLogged()) {
            if (isset($_REQUEST['ajax'])) {
                $this->ajaxErrorResponse('User not logged', HttpCode::STATUS_UNAUTHORIZED);
            } else {
                $this->redirectToLoginPage();
            }
        }

        if (! $this->loggedUser->hasOcTeamRole()) {
            if (isset($_REQUEST['ajax'])) {
                $this->ajaxErrorResponse('User is not admin', HttpCode::STATUS_UNAUTHORIZED);
            } else {
                $this->view->redirect('\\');
            }
        }

        $this->view->setVar('user', $this->loggedUser);

        if (isset($_REQUEST['action'])) {
            switch ($_REQUEST['action']) {
                case 'showreport':
                    $this->checkReportId();
                    $this->showSingleReport($_REQUEST['id']);
                    break;
                case 'showwatch':
                    $this->showWatch();
                    break;
                case 'addnote':
                    $this->addNote();
                    break;
                case 'newlog':
                    $this->addLog();
                    break;
                case 'addpoll':
                    $this->addPoll();
                    break;
                case 'cancelpoll':
                    $this->cancelPoll();
                    break;
                case 'remindpoll':
                    $this->remindPoll();
                    break;
                case 'savevote':
                    $this->saveVote();
                    break;
                case 'sendemail':
                    $this->sendEmail();
                    break;
                case 'watchOn':
                    $this->turnWatchReportOnAjax();

                    exit();
                case 'watchOff':
                    $this->turnWatchReportOffAjax();

                    exit();
                case 'changeCacheStatus':
                    $this->changeCacheStatusAjax();

                    exit();
                case 'changeStatus':
                    $this->changeStatusAjax();

                    exit();
                case 'changeLeader':
                    $this->changeLeaderAjax();

                    exit();
                case 'getTemplates':
                    $this->getEmailTemplatesAjax();

                    exit();
                case 'getTemplate':
                    $this->getTemplateByIdAjax();

                    exit();
                default:
                    if (isset($_REQUEST['ajax'])) {
                        $this->ajaxErrorResponse('Invalid/no action', HttpCode::STATUS_BAD_REQUEST);
                    }
            }
        }
        $this->showReportsList();
    }

    private function turnWatchReportOnAjax()
    {
        $this->checkSecurity(true);
        $this->paramAjaxCheck('id');
        $this->reportIdAjaxCheck($_REQUEST['id']);
        ReportWatches::turnWatchOnByReportId($_REQUEST['id'], $this->loggedUser->getUserId());
        $this->ajaxSuccessResponse();
    }

    private function turnWatchReportOffAjax()
    {
        $this->checkSecurity(true);
        $this->paramAjaxCheck('id');
        $this->reportIdAjaxCheck($_REQUEST['id']);
        ReportWatches::turnWatchOffByReportId($_REQUEST['id'], $this->loggedUser->getUserId());
        $this->ajaxSuccessResponse();
    }

    private function changeStatusAjax()
    {
        $this->checkSecurity(true);
        $this->paramAjaxCheck('id');
        $this->reportIdAjaxCheck($_REQUEST['id']);
        $this->paramAjaxCheck('status');

        if (! in_array($_REQUEST['status'], ReportCommons::getStatusesArray())) {
            $this->ajaxErrorResponse('Invalid new status', HttpCode::STATUS_BAD_REQUEST);
        }
        $report = new Report(['reportId' => $_REQUEST['id']]);
        $oldleader = $report->getUserIdLeader();

        if ($report->changeStatus($_REQUEST['status'])) {
            if ($oldleader != $report->getUserIdLeader()) {
                $this->ajaxSuccessResponse('reqReloadPage');
            } else {
                $this->ajaxSuccessResponse(tr($report->getReportStatusTranslationKey()));
            }
        } else {
            $this->ajaxErrorResponse('Poll is active!', HttpCode::STATUS_BAD_REQUEST);
        }
    }

    private function changeLeaderAjax()
    {
        $this->checkSecurity(true);
        $this->paramAjaxCheck('id');
        $this->reportIdAjaxCheck($_REQUEST['id']);
        $this->paramAjaxCheck('leader');

        if ($_REQUEST['leader'] != ReportCommons::USER_NOBODY) {
            $user = new User(['userId' => $_REQUEST['leader']]);

            if (! $user->hasOcTeamRole()) {
                unset($user);
                $this->ajaxErrorResponse('Invalid new leader', HttpCode::STATUS_BAD_REQUEST);
            }
            unset($user);
        }
        $report = new Report(['reportId' => $_REQUEST['id']]);
        $oldstatus = $report->getStatus();
        $report->changeLeader($_REQUEST['leader']);

        if ($oldstatus == ReportCommons::STATUS_NEW) { //Status changed new -> in progress => Page needs to be reloaded
            $this->ajaxSuccessResponse('reqReloadPage');
        } else {
            $this->ajaxSuccessResponse($report->getUserLeader()->getUserName());
        }
    }

    private function changeCacheStatusAjax()
    {
        // Security and parameters validation
        $this->checkSecurity(true);
        $this->paramAjaxCheck('id');
        $this->reportIdAjaxCheck($_REQUEST['id']);
        $this->paramAjaxCheck('status');
        $newStatus = $_REQUEST['status'];

        if (! in_array($newStatus, GeoCache::CacheStatusArray())) {
            $this->ajaxErrorResponse('Invalid new cache status', HttpCode::STATUS_BAD_REQUEST);
        }

        $report = Report::fromIdFactory($_REQUEST['id']);

        if ($newStatus != $report->getCache()->getStatus()) {
            $report->changeCacheStatus($newStatus);
        }

        $this->ajaxSuccessResponse(tr($report->getCache()->getStatusTranslationKey()));
    }

    private function getEmailTemplatesAjax()
    {
        $this->checkSecurity(true);
        $this->paramAjaxCheck('recipient');
        $this->paramAjaxCheck('objecttype');
        $this->ajaxSuccessResponse(ReportEmailTemplate::generateTemplateArray($_REQUEST['recipient'], $_REQUEST['objecttype']));
    }

    private function getTemplateByIdAjax()
    {
        $this->checkSecurity(true);
        $this->paramAjaxCheck('id');
        $this->reportIdAjaxCheck($_REQUEST['id']);
        $this->paramAjaxCheck('templateid');
        $report = new Report(['reportId' => $_REQUEST['id']]);
        $content = ReportEmailTemplate::getProcessedTemplate($_REQUEST['templateid'], $report);
        unset($report);
        $this->ajaxSuccessResponse($content);
    }

    private function paramAjaxCheck($paramName)
    {
        if (! isset($_REQUEST[$paramName])) {
            $this->ajaxErrorResponse('No parameter: ' . $paramName, HttpCode::STATUS_BAD_REQUEST);
        }
    }

    private function reportIdAjaxCheck($reportId)
    {
        if (! ReportCommons::isValidReportId($reportId)) {
            $this->ajaxErrorResponse('Incorrect report ID', HttpCode::STATUS_BAD_REQUEST);
        }
    }

    private function showReportsList()
    {
        if (isset($_REQUEST['reportId']) && ! empty($_REQUEST['reportId']) && ! isset($_REQUEST['reset'])) {
            if (ReportCommons::isValidReportId($_REQUEST['reportId'])) {
                $this->redirectToSingleReport($_REQUEST['reportId']);
            } else {
                $this->errorMsg = tr('admin_reports_err_noID');
            }
        }

        if (isset($_REQUEST['reset'])) {
            $this->resetSession();
        } else {
            $this->setSession();
        }

        if (isset($_REQUEST['infomsg'])) {
            $this->infoMsg = strip_tags(urldecode($_REQUEST['infomsg']));
        }

        if (isset($_REQUEST['errormsg'])) {
            $this->errorMsg = strip_tags(urldecode($_REQUEST['errormsg']));
        }
        $paginationModel = new PaginationModel(ReportCommons::REPORTS_PER_PAGE);
        $reportsCount = ReportCommons::getReportsCounts($this->loggedUser, $_SESSION['reportWp'], $_SESSION['reportType'], $_SESSION['reportStatus'], $_SESSION['reportUser']);
        $paginationModel->setRecordsCount($reportsCount);
        [$limit, $offset] = $paginationModel->getQueryLimitAndOffset();
        $reports = ReportCommons::getReports($this->loggedUser, $_SESSION['reportWp'], $_SESSION['reportType'], $_SESSION['reportStatus'], $_SESSION['reportUser'], $offset, $limit);
        $this->view->setVar('paginationModel', $paginationModel);
        $this->view->setVar('reports', $reports);
        $this->view->setVar('reportsCount', $reportsCount);
        $this->view->setVar('dateFormat', $this->ocConfig->getDatetimeFormat());
        $this->view->setVar('typeSelect', ReportCommons::generateTypeSelect($_SESSION['reportType'], true));
        $this->view->setVar('statusSelect', ReportCommons::generateStatusSelect(true, $_SESSION['reportStatus']));
        $this->view->setVar('userSelect', ReportCommons::generateUserSelect(false, $_SESSION['reportUser']));
        $this->view->setVar('reports_js', Uri::getLinkWithModificationTime('/views/admin/reports_list.js'));
        $this->view->setVar('infoMsg', $this->infoMsg);
        $this->view->setVar('errorMsg', $this->errorMsg);
        $this->view->setVar('cleanUri', $this->getCleanUri());
        $this->view->loadJQuery();
        $this->view->addLocalCss(Uri::getLinkWithModificationTime('/views/admin/reports.css'));
        $this->view->setTemplate('admin/reports_list');
        $this->view->buildView();

        exit();
    }

    private function showSingleReport($id)
    {
        if (isset($_REQUEST['infomsg'])) {
            $this->infoMsg = strip_tags(urldecode($_REQUEST['infomsg']));
        }

        if (isset($_REQUEST['errormsg'])) {
            $this->errorMsg = strip_tags(urldecode($_REQUEST['errormsg']));
        }
        $report = new Report(['reportId' => $id]);
        $logController = new LogEntryController();
        $inactivePolls = ReportPoll::getInActivePolls($id);
        $lastLogs = $logController->loadLogs($report->getCache(), false, 0, 5);
        $this->view->setVar('lastLogs', $lastLogs);
        $this->view->setVar('report', $report);
        $this->view->setVar('leaderSelect', ReportCommons::generateUserSelect(true, $report->getUserIdLeader()));
        $this->view->setVar('statusSelect', ReportCommons::generateStatusSelect(false, $report->getStatus()));
        $this->view->setVar('reports_js', Uri::getLinkWithModificationTime('/views/admin/report_show.js'));
        $this->view->setVar('reportLogs', ReportLog::getLogs($id));
        $this->view->setVar('activePolls', ReportPoll::getActivePolls($id));
        $this->view->setVar('inactivePolls', $inactivePolls);
        $this->view->setVar('includeGCharts', ! empty($inactivePolls));
        $this->view->setVar('logSelect', ReportEmailTemplate::generateTemplateSelect(ReportEmailTemplate::RECIPIENT_CACHELOG, $report->getType()));
        $this->view->setVar('infoMsg', $this->infoMsg);
        $this->view->setVar('errorMsg', $this->errorMsg);
        $this->view->setVar('cleanUri', $this->getCleanUri());
        $this->view->loadJQuery();
        $this->view->addLocalCss(Uri::getLinkWithModificationTime('/views/admin/reports.css'));
        $this->view->setTemplate('admin/report_show');
        $this->view->buildView();

        exit();
    }

    private function showWatch()
    {
        $paginationModel = new PaginationModel(ReportCommons::REPORTS_PER_PAGE);
        $reportsCount = ReportCommons::getReportsCounts($this->loggedUser, $_SESSION['reportWp'], $_SESSION['reportType'], $_SESSION['reportStatus'], $_SESSION['reportUser']);
        $paginationModel->setRecordsCount($reportsCount);
        [$limit, $offset] = $paginationModel->getQueryLimitAndOffset();
        $reports = ReportCommons::getWatchedReports($this->loggedUser, $offset, $limit);
        $this->view->setVar('paginationModel', $paginationModel);
        $this->view->setVar('reports', $reports);
        $this->view->setVar('dateFormat', $this->ocConfig->getDatetimeFormat());
        $this->view->setVar('reports_js', Uri::getLinkWithModificationTime('/views/admin/reports_list.js'));
        $this->view->loadJQuery();
        $this->view->addLocalCss(Uri::getLinkWithModificationTime('/views/admin/reports.css'));
        $this->view->setTemplate('admin/reports_watch');
        $this->view->buildView();

        exit();
    }

    private function addNote()
    {
        $this->checkSecurity();
        $this->checkReportId();
        $this->checkParam('note', true);
        $report = new Report(['reportId' => $_REQUEST['id']]);
        $report->addNote($_POST['note']);
        unset($report);
        $this->infoMsg = tr('admin_reports_info_notesaved');
        $this->redirectToSingleReport($_REQUEST['id']);
    }

    private function addLog()
    {
        $this->checkSecurity();
        $this->checkReportId();
        $this->checkParam('content', true);
        $report = new Report(['reportId' => $_REQUEST['id']]);

        if ($report->addOcTeamLog($_REQUEST['content'])) {
            $report->updateLastChanged();
            $report->saveReport();
            $logid = ReportLog::addLog($_REQUEST['id'], ReportLog::TYPE_CACHELOG_ADD, nl2br(strip_tags($_REQUEST['content'])));
            $report->sendWatchEmails($logid);

            if ($report->getUserIdLeader() != ReportCommons::USER_NOBODY && $this->loggedUser->getUserId() != $report->getUserIdLeader() && ! $report->isReportWatched($report->getUserIdLeader())) {
                ReportEmailSender::sendReportWatch($report, $report->getUserLeader(), $logid);
            }
            $this->infoMsg = tr('admin_reports_info_logok');
        } else {
            $this->errorMsg = tr('admin_reports_err_log');
        }
        $this->redirectToSingleReport($_REQUEST['id']);
    }

    private function sendEmail()
    {
        $this->checkSecurity();
        $this->checkReportId();
        $this->checkParam('email-recipient', true);
        $this->checkParam('content', true);
        $report = new Report(['reportId' => $_REQUEST['id']]);
        $report->sendEmail($_POST['email-recipient'], $_POST['content']);
        unset($report);
        $this->infoMsg = tr('mailto_messageSent');
        $this->redirectToSingleReport($_REQUEST['id']);
    }

    private function addPoll()
    {
        $this->checkSecurity();
        $this->checkReportId();
        $this->checkParam('period', true);
        $this->checkParam('question', true);
        $this->checkParam('ans1', true);
        $this->checkParam('ans2', true);
        $report = new Report(['reportId' => $_REQUEST['id']]);
        $report->createPoll($_POST['period'], $_POST['question'], $_POST['ans1'], $_POST['ans2'], (isset($_POST['ans3'])) ? $_POST['ans3'] : null);
        unset($report);
        $this->infoMsg = tr('admin_reports_info_pollok');
        $this->redirectToSingleReport($_REQUEST['id']);
    }

    private function cancelPoll()
    {
        $this->checkSecurity();
        $this->checkReportId();
        $this->checkParam('pollid');

        if (! ReportPoll::isValidPollId($_REQUEST['pollid'])) {
            $this->errorMsg = tr('admin_reports_info_errform');
        } else {
            $poll = new ReportPoll(['pollId' => $_REQUEST['pollid']]);

            if (! $poll->cancelPoll()) {
                $this->errorMsg = tr('admin_reports_err_poll');
            } else {
                $logid = ReportLog::addLog($_REQUEST['id'], ReportLog::TYPE_POLL_CANCEL, null, $_REQUEST['pollid']);
                $report = new Report(['reportId' => $_REQUEST['id']]);
                $report->sendWatchEmails($logid);

                if ($report->getUserIdLeader() != ReportCommons::USER_NOBODY && $report->getUserIdLeader() != $this->loggedUser->getUserId() && ! $report->isReportWatched($report->getUserIdLeader())) {
                    // If somebody cancels pool in the report assigned to another user - inform leader even if he don't watch this report
                    ReportEmailSender::sendReportWatch($report, $report->getUserLeader(), $logid);
                }
                unset($report);
                $this->infoMsg = tr('admin_reports_info_pollcanceled');
            }
        }
        $this->redirectToSingleReport($_REQUEST['id']);
    }

    private function remindPoll()
    {
        $this->checkSecurity();
        $this->checkReportId();
        $this->checkParam('pollid');
        $voterlist = ReportPoll::getVotersArray($_REQUEST['pollid']);
        $userlist = MultiUserQueries::getOcTeamMembersArray();

        foreach ($userlist as $user) {
            if (! in_array($user['user_id'], $voterlist) && $user['user_id'] != $this->loggedUser->getUserId()) {
                ReportEmailSender::sendNewPoll(new ReportPoll(['pollId' => $_REQUEST['pollid']]), new User(['userId' => $user['user_id']]), true);
            }
        }
        $this->infoMsg = tr('admin_reports_info_reminder');
        $this->redirectToSingleReport($_REQUEST['id']);
    }

    private function saveVote()
    {
        $this->checkSecurity();
        $this->checkReportId();
        $this->checkParam('pollid', true);
        $this->checkParam('vote', true);

        if (! ReportPoll::isValidPollId($_POST['pollid'])) { // check if pollid id valid
            $this->errorMsg = tr('admin_reports_info_errform');
        } else {
            $poll = new ReportPoll(['pollId' => $_POST['pollid']]);
            // All checks will be done in addVote
            if ($poll->addVote($_POST['vote'])) {
                $this->infoMsg = tr('admin_reports_info_voteok');
            } else {
                $this->errorMsg = tr('admin_reports_info_errform');
            }
            unset($poll);
        }
        $this->redirectToSingleReport($_REQUEST['id']);
    }

    private function checkReportId()
    {
        if (! (isset($_REQUEST['id']) && ReportCommons::isValidReportId($_REQUEST['id']))) {
            $this->errorMsg = tr('admin_reports_err_noID');
            $this->redirectToReportList();
        }
    }

    private function checkParam($paramName, $post = false)
    {
        if ($post == false && ! isset($_REQUEST[$paramName])) {
            $this->errorMsg = tr('admin_reports_info_errform');
            $this->redirectToReportList();
        } elseif ($post == true && ! isset($_POST[$paramName])) {
            $this->errorMsg = tr('admin_reports_info_errform');
            $this->redirectToReportList();
        }
    }

    private function checkSecurity($ajax = false)
    {
        if (! isset($_SERVER['HTTP_REFERER']) || (parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST) != parse_url($this->ocConfig->getAbsolute_server_URI(), PHP_URL_HOST))) {
            if ($ajax) {
                $this->ajaxErrorResponse('No hacking please!', HttpCode::STATUS_FORBIDDEN);
            }
            $this->errorMsg = 'No hacking please!';
            $this->redirectToReportList();
        }
    }

    private function redirectToSingleReport($id)
    {
        $uri = '/admin_reports.php?action=showreport&id=' . $id;

        if ($this->errorMsg !== null) {
            $uri .= '&errormsg=' . urlencode($this->errorMsg);
        }

        if ($this->infoMsg !== null) {
            $uri .= '&infomsg=' . urlencode($this->infoMsg);
        }
        $this->view->redirect($uri);
    }

    private function redirectToReportList()
    {
        $uri = '/admin_reports.php';

        if ($this->errorMsg !== null) {
            $uri = Uri::setOrReplaceParamValue('errormsg', $this->errorMsg, $uri);
        }

        if ($this->infoMsg !== null) {
            $uri = Uri::setOrReplaceParamValue('infomsg', $this->infoMsg, $uri);
        }
        $this->view->redirect($uri);
    }

    private function getCleanUri(): ?string
    {
        $cleanUri = Uri::removeParam('errormsg');

        return Uri::removeParam('infomsg', $cleanUri);
    }

    private function setSession()
    {
        if (isset($_REQUEST['reportType'])) {
            $_SESSION['reportType'] = (int) $_REQUEST['reportType'];
        } elseif (! isset($_SESSION['reportType'])) {
            $_SESSION['reportType'] = ReportCommons::DEFAULT_REPORTS_TYPE;
        }

        if (isset($_REQUEST['reportStatus'])) {
            $_SESSION['reportStatus'] = (int) $_REQUEST['reportStatus'];
        } elseif (! isset($_SESSION['reportStatus'])) {
            $_SESSION['reportStatus'] = ReportCommons::DEFAULT_REPORTS_STATUS;
        }

        if (isset($_REQUEST['reportUser'])) {
            $_SESSION['reportUser'] = (int) $_REQUEST['reportUser'];
        } elseif (! isset($_SESSION['reportUser'])) {
            $_SESSION['reportUser'] = ReportCommons::DEFAULT_REPORTS_USER;
        }

        if (isset($_REQUEST['reportWp']) && $_REQUEST != '') {
            $_SESSION['reportWp'] = $_REQUEST['reportWp'];
        } elseif (! isset($_SESSION['reportWp'])) {
            $_SESSION['reportWp'] = '';
        }
    }

    private function resetSession()
    {
        $_SESSION['reportType'] = ReportCommons::DEFAULT_REPORTS_TYPE;
        $_SESSION['reportStatus'] = ReportCommons::DEFAULT_REPORTS_STATUS;
        $_SESSION['reportUser'] = ReportCommons::DEFAULT_REPORTS_USER;
        $_SESSION['reportWp'] = '';
    }
}
