<?php

// ddev-router terminates tls, so apache reports port 80 for https requests and OKAPI rejects them
if (isset($_SERVER['HTTP_X_FORWARDED_PORT'])) {
    $_SERVER['SERVER_PORT'] = $_SERVER['HTTP_X_FORWARDED_PORT'];
}
