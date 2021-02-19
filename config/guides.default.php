<?php

/**
 * This is configuration of guides criterias
 *
 * This is a DEFAULT configuration for ALL nodes, which contains necessary vars.
 *
 * If you want to customize these values for your node
 * create config for your node (file guides.pl.php for OCPL)
 * and there override $guides array values as needed.
 *
 */

$guides = [
    'guideActivePeriod' => 90,      // every guide should found/create a geocache in last X days
    'guideGotRecommendations' => 20, // guide should get X recomendations in his/her caches
];
