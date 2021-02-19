<?php

/**
 * Configuration of translations and localization of the OC code
 *
 * This is a default configuration.
 * It may be customized in node-specific configuration file.
 */

$config = [];

/**
 * Default node language
 */
$config['defaultLang'] = 'en';

/**
 * List of languages supported by node.
 * Use two-lower-letters codes.
 * Please note that for now we still have some translations in DB!
 */
$config['supportedLanguages'] = ['pl', 'en', 'nl', 'ro'];

/**
 * If node support crowdinInContext mode
 */
$config['crowdinInContextSupported'] = true;

/**
 * Crowdin in-context translation plugin needs "pseudo" language file to inject its identifiers
 * See https://crowdin.com/project/oc-polish-code-translations/settings#in-context for details.
 */
$config['crowdinInContextPseudoLang'] = 'aa';
