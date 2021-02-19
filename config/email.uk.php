<?php

/**
 * E-mail configuration
 *
 * Those are configuration overrides for OCUK node only.
 */

/**
 * Address used to send technical notifications from the OC server/code etc.
 */
$config['technicalNotificationEmail'] = 'techNotifyUK@o#pencaching.pl';

/**
 * Technical contact address for users of this OC node
 */
$config['nodeTechContactEmail'] = 'de#g@be#st.pl';

/**
 * Contact address to OCTEAM for this node (reviewers and regional service for cachers)
 */
$config['ocTeamContactEmail'] = 'in#fo@op#enca#che.uk';

/**
 * Signature for OCTeam emails for this node
 */
$config['ocTeamEmailSignature'] = 'Regards, OC UK Team';

/**
 * No-reply email address for this node
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
