<?php
/**
 * Configuration settings for src\Utils\Lock objects:
 * - "type":
 *      The locking mechanism used in objects; currently supported values:
 *      - "file": uses flock with filenames based on locking identifiers
 *                and dirname based on configuration setting
 *      Each mechanism implemented must have defined settings array under the
 *      corresponding value key, where at least `class` key must be specified
 *      with value containing full class name of the mechanism implementation.
 * - "file":
 *      Settings used in file lock mechanism
 *      - "class":
 *           Must be the 'src\Utils\Lock\FileLock' string
 *      - "dir":
 *           Path (subdirectory of $dynbasepath) of directory used in "file"
 *           locking mechanism to store locks.
 *           Created on first use attempt (including parent dirs) if not exists.
 */
$lock = [
    "type" => "file",
    "file" => [
        "class" => "src\Utils\Lock\FileLock",
        "dir" => "oc_lock"
    ]
];
