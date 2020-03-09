<?php

/**
 * This is column containing formatted date or date and time.
 * $data should contain:
 * - date - date ;) - DateTime type or string which can be converted to DateTime
 * - showTime - bool
 */

use src\Utils\Text\Formatter;

return function ($data) {
    echo Formatter::date($data['date'], $data['showTime']);
};
