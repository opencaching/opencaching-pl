<?php

/**
 * News configuration
 *
 * This is a default configuration.
 * It may be customized in node-specific configuration file.
 */

$news = [];

/**
 * Does news should be visible on start page even fro non-logged users
 */
$news['showOnStartPageForNonLoggedUsers'] = false;

/**
 * List of local (node-specific) categories
 * which are added to common categories defined in NEWS model
 *
 * Names of categories can't be started with "_" - reserved for common categories
 */
$news['localCategories'] = [];
