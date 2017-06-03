<?php

# This is a wrapper for OKAPI's "services/caches/formatters/gpx" method. It
# takes parameters from okapiGpxFormatterWidget, executes OKAPI's gpx formatter
# with the signed-in user's credentials, and returns the result GPX file.
#
# Please note, that this file is NOT part of the official API. It may
# stop working at any time.

$rootpath = "../";

require_once($rootpath . 'okapi/facade.php');

try {
    $user_id = \okapi\Facade::detect_user_id();
    if ($user_id === null) {
        throw new \okapi\BadRequest("Please sign in first.");
    }

    $jsonParams = isset($_POST['params']) ? $_POST['params'] : "";
    $params = json_decode($jsonParams, true);
    if ($params === null) {
        throw new \okapi\BadRequest("Missing or invalid parameters.");
    }

    if (isset($params['_filename'])) {
        $filename = $params['_filename'];
        unset($params['_filename']);
    } else {
        $filename = "results.gpx";
    }

    $response = \okapi\Facade::service_call(
        "services/caches/formatters/gpx",
        $user_id,
        $params
        );
    $response->content_disposition = 'attachment; filename="'.$filename.'"';
    $response->display();
} catch (\okapi\BadRequest $e) {
    http_response_code(400);
    header("Content-Type: text/plain");
    die(
        "Error occurred. Invalid session state, or invalid parameters.\n\n".
        "Please note, that this URL is NOT part of the official API. It may\n".
        "stop working at any time. If you're an external developer, then you\n".
        "SHOULD use our official API instead."
    );
}
