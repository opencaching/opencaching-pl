<?php
namespace Controllers\Admin;

use Controllers\BaseController;
use Utils\Uri\Uri;
use lib\Objects\Admin\Report;
use lib\Objects\Admin\ReportCommons;
use lib\Objects\Admin\ReportEmailSender;
use lib\Objects\GeoCache\GeoCache;
use lib\Objects\User\User;

class ReportCacheController extends BaseController
{

    private $infoMsg;

    private $errorMsg;

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->checkSecurity();
        switch ($_REQUEST['action']) {
            case 'add':
                $this->addReport();
                break;
            case 'view':
                $this->showReport();
                break;
            case 'save':
                $this->saveReport();
                break;
            case 'info':
                $this->showInfo();
                break;
            default:
                $this->view->redirect('/');
                break;
        }
        exit();
    }

    private function addReport()
    {
        $this->checkReferer();
        $this->checkParam('cacheid');
        $this->view->setVar('cacheid', $_REQUEST['cacheid']);
        $this->view->setVar('reasonSelect', ReportCommons::generateTypeSelect());
        $this->view->setVar('report_js', Uri::getLinkWithModificationTime('/tpl/stdstyle/report/report.js'));
        $this->view->addLocalCss(Uri::getLinkWithModificationTime('/tpl/stdstyle/admin/reports.css'));
        $this->view->setTemplate('report/report_add');
        $this->view->buildView();
        exit();
    }

    private function saveReport()
    {
        $this->checkReferer();
        $this->checkParam('cacheid', true);
        $this->checkParam('type', true);
        $this->checkParam('reason', true);
        $this->checkParam('content', true);
        if ((! in_array($_POST['type'], ReportCommons::getReportRecipientsArray())) || (! in_array($_POST['reason'], ReportCommons::getTypesArray()))) {
            $this->errorMsg = tr('admin_reports_info_errform') . ' (params)';
            $this->redirectToInfoPage();
        }
        try {
            $cache = new GeoCache([
                'cacheId' => $_POST['cacheid']
            ]);
        } catch (\Exception $e) {
            $this->errorMsg = tr('admin_reports_info_errform') . ' (cacheid)';
            $this->redirectToInfoPage();
        }
        $cacheOwner = new User([
            'userId' => $cache->getOwnerId()
        ]);
        $content = nl2br(strip_tags($_POST['content']));
        switch ($_POST['type']) {
            case ReportCommons::RECIPIENT_OWNER:
                ReportEmailSender::sendReport2COMail2CO($cacheOwner, $this->loggedUser, $cache, $content, $_POST['reason'], isset($_POST['report-pubemail']));
                ReportEmailSender::sendReport2COMail2S($this->loggedUser, $cache, $content, $_POST['reason']);
                $this->infoMsg = tr('reports_user_msg_reportok');
                break;
            case ReportCommons::RECIPIENT_OCTEAM:
                $report = new Report();
                $report->setObjectType(ReportCommons::OBJECT_CACHE);
                $report->setUserIdSubmit($this->loggedUser->getUserId());
                $report->setCacheId($cache->getCacheId());
                $report->setType($_POST['reason']);
                $report->setContent($content);
                $report->setDateSubmit(new \DateTime('now'));
                $report->setStatus(ReportCommons::STATUS_NEW);
                if ($report->saveReport() == null) {
                    $this->errorMsg = tr('reports_user_msg_reporterr');
                } else {
                    ReportEmailSender::sendReport2OCTMail2CO($cacheOwner, $report);
                    ReportEmailSender::sendReport2OCTMail2S($this->loggedUser, $report);
                    $userlist = ReportCommons::getOcTeamArray();
                    foreach ($userlist as $user) { // Send mails to all OC Team members
                        ReportEmailSender::sendReport2OCTMail2OCTeam(new User([
                            'userId' => $user['user_id']
                        ]), $report);
                    }
                    $this->infoMsg = tr('reports_user_msg_reportok');
                }
                unset($report);
                break;
        }
        unset($cacheOwner);
        unset($cache);
        $this->redirectToViewPage($_POST['cacheid']);
        exit();
    }

    private function showReport()
    {
        $this->view->setVar('infoMsg', $this->infoMsg);
        $this->view->setVar('errorMsg', $this->errorMsg);
        $this->view->setTemplate('report/report_show');
        $this->view->buildView();
        exit();
    }

    private function showInfo()
    {
        if (isset($_REQUEST['infomsg'])) {
            $this->infoMsg = strip_tags(urldecode($_REQUEST['infomsg']));
        }
        if (isset($_REQUEST['errormsg'])) {
            $this->errorMsg = strip_tags(urldecode($_REQUEST['errormsg']));
        }
        $this->view->loadJQuery();
        $this->view->setVar('infoMsg', $this->infoMsg);
        $this->view->setVar('errorMsg', $this->errorMsg);
        $this->view->setTemplate('report/report_info');
        $this->view->buildView();
        exit();
    }

    private function redirectToInfoPage()
    {
        $uri = '/report.php?action=info';
        if ($this->errorMsg !== null) {
            $uri .= '&errormsg=' . urlencode($this->errorMsg);
        }
        if ($this->infoMsg !== null) {
            $uri .= '&infomsg=' . urlencode($this->infoMsg);
        }
        $this->view->redirect($uri);
        exit();
    }

    private function redirectToViewPage($cacheid)
    {
        $uri = '/viewcache.php?cacheid=' . $cacheid;
        if ($this->errorMsg !== null) {
            $uri .= '&errormsg=' . urlencode($this->errorMsg);
        }
        if ($this->infoMsg !== null) {
            $uri .= '&infomsg=' . urlencode($this->infoMsg);
        }
        $this->view->redirect($uri);
        exit();
    }

    private function checkParam($paramName, $post = false)
    {
        if ($post) {
            if (! isset($_POST[$paramName])) {
                $this->errorMsg = tr('admin_reports_info_errform');
                $this->showInfo();
            }
        } else {
            if (! isset($_REQUEST[$paramName])) {
                $this->errorMsg = tr('admin_reports_info_errform');
                $this->showInfo();
            }
        }
    }

    private function checkSecurity()
    {
        if (! $this->isUserLogged()) {
            $this->redirectToLoginPage();
            exit();
        }
        if (! isset($_REQUEST['action'])) {
            $this->view->redirect('/');
            exit();
        }
    }

    private function checkReferer()
    {
        if (! isset($_SERVER['HTTP_REFERER']) || (parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST) != parse_url($this->ocConfig->getAbsolute_server_URI(), PHP_URL_HOST))) {
            $this->view->redirect('/');
        }
    }
}
