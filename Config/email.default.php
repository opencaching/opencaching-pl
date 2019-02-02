<?php
/**
 * Configuration of emails for OCNL code
 *
 * This is a DEFAULT configuration for ALL nodes, which contains necessary vars.
 *
 * If you to customize it for your node
 * create config for your node and there override array values as needed.
 *
 */

$config = [];

/**
 * Address used to send technical notifications from the OC server/code etc.
 * ALL HASHES WILL BE AUTOMATICALLY REMOVED FROM EMAIL ADDRESS!
 */
$config['technicalNotificationEmail'] = '#tech#No#tif#y#@#ope#ncac#hing.pl';

/**
 * Technical contact address for users of this OC node
 * ALL HASHES WILL BE AUTOMATICALLY REMOVED FROM EMAIL ADDRESS!
 */
$config['nodeTechContactEmail'] = 'rt@o#pencaching.pl';

/**
 * Contact address to OCTEAM for this node (reviewers and regional service for cachers)
 * ALL HASHES WILL BE AUTOMATICALLY REMOVED FROM EMAIL ADDRESS!
 */
$config['ocTeamContactEmail'] = 'cog@o#pencaching.pl';

/**
 * Signature for OCTeam emails for this node
 * ALL HASHES WILL BE AUTOMATICALLY REMOVED FROM EMAIL ADDRESS!
 */
$config['ocTeamEmailSignature'] = 'Pozdrawiamy, Zespół www.opencaching.pl';

/**
 * No-reply email address for this node
 * ALL HASHES WILL BE AUTOMATICALLY REMOVED FROM EMAIL ADDRESS!
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


