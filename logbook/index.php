<?
    switch($_GET['page']) {
        default:
        case 'logbook':
            include("logbook.php");
            break;
        case 'cachevalidator':
            include("cachevalidator.php");
            break;
    }
?>