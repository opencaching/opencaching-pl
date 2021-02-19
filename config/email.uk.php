<?php

/**
 * Configuration of emails for OCUK code
 */

/**
 * Address used to send technical notifications from the OC server/code etc.
 * ALL HASHES WILL BE AUTOMATICALLY REMOVED FROM EMAIL ADDRESS!
 */
$config['technicalNotificationEmail'] = 'techNotifyUK@o#pencaching.pl';

/**
 * Technical contact address for users of this OC node
 * ALL HASHES WILL BE AUTOMATICALLY REMOVED FROM EMAIL ADDRESS!
 */
$config['nodeTechContactEmail'] = 'de#g@be#st.pl';

/**
 * Contact address to OCTEAM for this node (reviewers and regional service for cachers)
 * ALL HASHES WILL BE AUTOMATICALLY REMOVED FROM EMAIL ADDRESS!
 */
$config['ocTeamContactEmail'] = 'in#fo@op#enca#che.uk';

/**
 * Signature for OCTeam emails for this node
 * ALL HASHES WILL BE AUTOMATICALLY REMOVED FROM EMAIL ADDRESS!
 */
$config['ocTeamEmailSignature'] = 'Regards, OC UK Team';

/**
 * No-reply email address for this node
 * ALL HASHES WILL BE AUTOMATICALLY REMOVED FROM EMAIL ADDRESS!
 */
$config['noReplyEmail'] = 'nor#eply@op#encache.uk';

/**
 * Prefix added to subject of emails sending from OC code
 */
$config['mailSubjectPrefix'] = 'OCUK';

/**
 * Prefix added to subject of emails sending from OC code in context of cache review
 */
$config['mailSubjectPrefixForReviewers'] = 'OCTeam';
