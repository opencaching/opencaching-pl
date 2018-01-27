<?php
/**
 * Contains \Utils\Lock\Lock class definition. This is the entry class to
 * common locking mechanism, providing sufficient abstraction layer where the
 * real locking is performed by one of subclasses, based on configuration.
 *
 * General example (source file, exclusive, nonblocking):
 * $handle = Lock::tryLock(__FILE__, Lock::EXCLUSIVE | Lock::NONBLOCKING);
 * if ($handle) {
 *  // do something in critical section
 *  Lock::unlock($handle);
 * }
 *
 * General example (class instance, exclusive, blocking):
 * $handle = Lock::tryLock($this)
 * if ($handle) {
 *  // do something in critical section
 *  Lock::unlock($handle)
 * }
 *
 * Real file example:
 * $options = [ Lock::OPTION_USE_EXISTING_FILE ];
 * $fileHandle = Lock::tryLock("existing_file_path", Lock::EXCLUSIVE, $options);
 * if ($fileHandle) {
 *  // do something f.ex. write to $fileHandle
 *  Lock::unlock($fileHandle, $options);
 * }
 *
 * Only file lock mechanism is currently implemented, so the existing file
 * option is provided for future use, when another locking mechanism will be
 * implemented too.
 *
 * To implement another locking mechanism extend the \Utils\Lock\RealLock
 * class and add corresponding settings to Config files
 * (f.ex. Config/lock.default.php).
 */
namespace Utils\Lock;

use lib\Objects\OcConfig\OcConfig;

/**
 * Provides the abstraction layer to locking mechanism. Should be used as the
 * only entry point to locking.
 */
abstract class Lock
{
    /** Indicates the locking should be exclusive */
    const EXCLUSIVE = 0;
    /**
     * Indicates the locking should be shared, overrides exclusive in
     * bitwise or
     */
    const SHARED = 1;
    /**
     * Indicates the locking should be nonblocking, i.e. the tryLock method
     * should not wait until the resource will be available to lock.
     * Can be used with exclusive and shared lock both.
     */
    const NONBLOCKING = 2;

    /**
     * Option to inform that the identifier is an existing file path or handle,
     * external to the locking settings
     */
    const OPTION_USE_EXISTING_FILE = "lock_use_existing_file";

    /**
     * Tries to lock the resource given by identifier using given mode.
     * The real locking mechanism is based on configuration settings.
     * If the options passed contain OPTION_USE_EXISTING_FILE and the identifier
     * is an existing file path or handle, the file locking is used regardless
     * of config settings.
     *
     * @param mixed $identifier the identifier of resource being locked on, can
     *     be a file path or an object for example.
     * @param int $mode the locking mode, should be set using EXCLUSIVE, SHARED
     *     and NONBLOCKING constants with bitwise or. Possible values:
     *     0 - exclusive,blocking; 1 - shared, blocking; 2 - exclusive,
     *     nonblocking; 3 - shared, nonblocking
     * @param string[] $options options providing additional information.
     *     Currently supported OPTION_USE_EXISTING_FILE constant indicating
     *     the file locking should be used on existing file.
     *
     * @return resource the lock handle on success, null on failure
     */
    final public static function tryLock(
        $identifier,
        $mode = self::EXCLUSIVE,
        array $options = null
    ) {
        $result = null;
        if (self::useExistingFile($identifier, $options)) {
            $result = (new FileLock(null))->internalTryLock(
                $identifier,
                $mode,
                $options
            );
        } else {
            $result = self::getRealLock()->internalTryLock(
                $identifier,
                $mode,
                $options
            );
        }
        return $result;
    }

    /**
     * Unlocks the resource previously locked by tryLock method. The successfuly
     * unclocked handle is always closed.
     *
     * @param resource $handle the resource being the result of previous tryLock
     *     method call
     * @param string[] $options options providing additional information.
     *     Currently supported OPTION_USE_EXISTING_FILE constant indicating
     *     the file unlocking should be used regardless of config settings.
     *
     * @return boolean true if unlocking succeded, false otherwise
     */
    final public static function unlock($handle, array $options = null)
    {
        $result = false;
        if (self::useExistingFile($handle, $options)) {
            $result = (new FileLock(null))->internalUnlock($handle);
        } else {
            $result = self::getRealLock()->internalUnlock($handle);
        }
        return $result;
    }

    /**
     * Forcedly removes all locks set up on the resource described by given
     * identifier. In most cases it is done by removing the resource itself
     *
     * CAUTION: Use only as a last resort. Can cause data loss and system
     *     inconsistency!
     *
     * @param mixed $identifier the identifier of resource to unlock, can
     *     be a file path or an object for example.
     * @param string[] $options options providing additional information.
     *     Currently supported OPTION_USE_EXISTING_FILE constant indicating
     *     the file unlocking should be used regardless of config settings.
     *
     * @return boolean true if unlocking succeded, false otherwise
     */
    final public static function forceUnlock($identifier, array $options = null)
    {
        $result = false;
        if (self::useExistingFile($identifier, $options)) {
            $result = (new FileLock(null))->internalForceUnlock(
                $identifier,
                $options
            );
        } else {
            $result = self::getRealLock()->internalForceUnlock($identifier);
        }
        return $result;
    }

    /**
     * Determines and creates real locking mechanism class instance, based on
     * config settings
     *
     * @return object instance of {@see RealLock} subclass, created according to
     *     config settings, null if no correct settings found
     */
    final private static function getRealLock()
    {
        $lockConfig = OcConfig::instance()->getLockConfig();
        $result = null;
        if (
            !empty($lockConfig["type"]) &&
            !empty($lockConfig[$lockConfig["type"]])
        ) {
            $settings = $lockConfig[$lockConfig["type"]];
            if (!empty($settings["class"])) {
                $result = new $settings["class"]($settings);
            }
        }
        return $result;
    }

    /**
     * Checks if file locking on external existing file should be used
     *
     * @param mixed $identifier the identifier to check if it is an existing
     *     file path or handle
     * @param string[] $options options to check if contain
     *     OPTION_USE_EXISTING_FILE
     * @return boolean true if existing file locking should be used, false
     *     otherwise
     */
    final protected static function useExistingFile(
        $identifier,
        $options = null
    ) {
        $result = false;
        if (
            $options != null
            && in_array(self::OPTION_USE_EXISTING_FILE, $options)
        ) {
            if (is_string($identifier) && is_file($identifier)) {
                $result = true;
            } elseif (
                is_resource($identifier)
                && get_resource_type($identifier) == 'stream'
            ) {
                $meta_data = stream_get_meta_data($identifier);
                $result = (
                    $meta_data != null
                    && isset($meta_data['stream_type'])
                    && $meta_data['stream_type'] === 'STDIO'
                    && isset($meta_data['wrapper_type'])
                    && $meta_data['wrapper_type'] === 'plainfile'
                );
            }
        }
        return $result;
    }
}
