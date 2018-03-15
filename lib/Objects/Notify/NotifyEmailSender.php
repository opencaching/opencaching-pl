<?php
namespace lib\Objects\Notify;

use Utils\Email\Email;
use Utils\Email\EmailFormatter;
use Utils\Gis\Gis;
use Utils\Uri\SimpleRouter;
use lib\Objects\GeoCache\GeoCache;
use lib\Objects\OcConfig\OcConfig;
use lib\Objects\User\User;

class NotifyEmailSender
{

    const EMAIL_TEMPLATE_PATH = __DIR__ . '/../../../tpl/stdstyle/email/';

    /**
     * Sends email to $user with notifies about new caches in neighbourhoods
     *
     * @param Notify[] $notifiesList
     * @param User $user
     */
    public static function sendNewCacheNotify($notifiesList, User $user)
    {
        if (count($notifiesList) > 1) {
            $pluralSuffix = '_pl';
        } else {
            $pluralSuffix = '';
        }

        $serverUri = rtrim(OcConfig::getAbsolute_server_URI(), '/');
        $content = '';
        foreach ($notifiesList as $item) {
            $line = file_get_contents(self::EMAIL_TEMPLATE_PATH . 'newcache_notify_item.html');
            $line = mb_ereg_replace('{absolute_server_URI}', $serverUri, $line);
            $line = mb_ereg_replace('{cache_type}', tr($item->getCache()->getCacheTypeTranslationKey()), $line);
            $line = mb_ereg_replace('{cache_type_icon}', GeoCache::CacheIconByType($item->getCache()->getCacheType(), $item->getCache()->getStatus()), $line);
            $line = mb_ereg_replace('{cache_wp}', $item->getCache()->getWaypointId(), $line);
            $line = mb_ereg_replace('{cache_url}', $item->getCache()->getCacheUrl(), $line);
            $line = mb_ereg_replace('{cache_name}', $item->getCache()->getCacheName(), $line);
            $line = mb_ereg_replace('{cache_size}', tr($item->getCache()->getSizeTranslationKey()), $line);
            $line = mb_ereg_replace('{cache_direction}', Gis::bearing2Text(Gis::calcBearingBetween($user->getHomeCoordinates(), $item->getCache()->getCoordinates()), true), $line);
            $line = mb_ereg_replace('{cache_distance}', round(Gis::distanceBetween($user->getHomeCoordinates(), $item->getCache()->getCoordinates())), $line);
            $line = mb_ereg_replace('{cache_unit}', 'km', $line);
            $line = mb_ereg_replace('{cache_diff_icon}', $item->getCache()->getDifficultyIcon(), $line);
            $line = mb_ereg_replace('{cache_diff}', $item->getCache()->getDifficulty() / 2, $line);
            $line = mb_ereg_replace('{cache_ter_icon}', $item->getCache()->getTerrainIcon(), $line);
            $line = mb_ereg_replace('{cache_ter}', $item->getCache()->getTerrain() / 2, $line);
            $line = mb_ereg_replace('{cache_author_profile}', $item->getCache()
                ->getOwner()
                ->getProfileUrl(), $line);
            $line = mb_ereg_replace('{cache_author}', $item->getCache()
                ->getOwner()
                ->getUserName(), $line);
            $line = mb_ereg_replace('{cache_author_activity}', tr('user_activity01'), $line);
            $line = mb_ereg_replace('{cache_author_activity2}', tr('user_activity02'), $line);
            $line = mb_ereg_replace('{cache_author_found}', $item->getCache()
                ->getOwner()
                ->getFoundGeocachesCount(), $line);
            $line = mb_ereg_replace('{cache_author_dnf}', $item->getCache()
                ->getOwner()
                ->getNotFoundGeocachesCount(), $line);
            $line = mb_ereg_replace('{cache_author_hidden}', $item->getCache()
                ->getOwner()
                ->getHiddenGeocachesCount(), $line);
            $line = mb_ereg_replace('{cache_author_total}', $item->getCache()
                ->getOwner()
                ->getFoundGeocachesCount() + $item->getCache()
                ->getOwner()
                ->getNotFoundGeocachesCount() + $item->getCache()
                ->getOwner()
                ->getHiddenGeocachesCount(), $line);
            $line = mb_ereg_replace('{cache_date}', $item->getCache()
                ->getDatePlaced()
                ->format(OcConfig::instance()->getDateFormat()), $line);
            $content .= $line;
        }

        $subject = tr('notify_subject' . $pluralSuffix);
        $subject = mb_ereg_replace('{site_name}', OcConfig::getSiteName(), $subject);

        $formattedMessage = new EmailFormatter(self::EMAIL_TEMPLATE_PATH . 'newcache_notify.email.html', true);
        $formattedMessage->addFooterAndHeader($user->getUserName());
        $formattedMessage->setVariable('intro', tr('notify_intro' . $pluralSuffix));
        $formattedMessage->setVariable('mynbhUrl', SimpleRouter::getAbsLink('MyNeighbourhood', 'config'));
        $formattedMessage->setVariable('notifySettingsUrl', SimpleRouter::getAbsLink('UserProfile', 'notifySettings'));
        $formattedMessage->setVariable('content', $content);

        $email = new Email();
        $email->addToAddr($user->getEmail());
        $email->setReplyToAddr(OcConfig::getNoreplyEmailAddress());
        $email->setFromAddr(OcConfig::getNoreplyEmailAddress());
        $email->addSubjectPrefix(OcConfig::getMailSubjectPrefixForSite());
        $email->setSubject($subject);
        $email->setBody($formattedMessage->getEmailContent(), true);
        $email->send();
    }
}