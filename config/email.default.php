<?php

/**
 * E-mail configuration
 *
 * This is a default configuration.
 * It may be customized in node-specific configuration file.
 *
 * ALL HASHES WILL BE AUTOMATICALLY REMOVED FROM EMAIL ADDRESS!
 */

$config = [];

/**
 * Address (or array of addresses) used to send technical notifications from the OC server/code etc.
 */
$config['technicalNotificationEmail'] = 'techNotify#@opencac#hing.pl';

/**
 * Technical contact address for users of this OC node
 */
$config['nodeTechContactEmail'] = 'rt@o#pencaching.pl';

/**
 * Contact address to OCTEAM for this node (reviewers and regional service for cachers)
 */
$config['ocTeamContactEmail'] = 'cog@o#pencaching.pl';

/**
 * Signature for OCTeam emails for this node
 */
$config['ocTeamEmailSignature'] = 'Best regards from OC Team!';

/**
 * No-reply email address for this node
 */
$config['noReplyEmail'] = 'noreply@o#pencaching.pl';

/**
 * Prefix added to subject of emails sending from OC code
 */
$config['mailSubjectPrefix'] = 'OCPL';

/**
 * Prefix added to subject of emails sending from OC code in context of cache review
 */
$config['mailSubjectPrefixForReviewers'] = 'COG';
