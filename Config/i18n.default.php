<?php
/**
 * This is simple configuration of translations and localizatin of the OC code
 *
 * This is a DEFAULT configuration for ALL nodes, which contains necessary vars.
 *
 * If you to customize it for your node
 * create config for your node and there override array values as needed.
 */

$config = [];

/**
 * List of languages supported by node.
 * Use two-lower-letters codes.
 * Please note that for now still we have also translations in DB!
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

