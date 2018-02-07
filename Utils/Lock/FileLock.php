<?php
/**
 * Contains \Utils\Lock\FileLock class definition.
 */
namespace Utils\Lock;

use \RuntimeException;

/**
 * Implements locking mechanism using flock function
 */
final class FileLock extends RealLock
{
    /** @var string directory path where to store dynamic lock files */
    private $lockDir;

    /**
     * Creates directory for dynamic lock files storage if specified in settings
     *
     * @param string[] $settings {@see RealLock::internalConstruct()}
     *
     * @see RealLock::internalConstruct()
     */
    protected function internalConstruct($settings)
    {
        $lockDir = null;
        if (!empty($settings["dir"])) {
            $lockDir = $settings["dir"];
        }
        if ($lockDir != null && !is_dir($lockDir)) {
            mkdir($lockDir, 0755, true);
        }
        $this->lockDir = $lockDir;
    }

    /**
     * Sets the flock flags according to mode value, then checks if existing
     * file should be locked or a dynamic one, opens it if neccessary and tries
     * to flock the open file.
     * CAUTION: Nonblocking may not work in MS Windows, untested
     *
     * @param mixed $identifier {@see Lock::tryLock()}
     * @param int $mode {@see Lock::tryLock()}
     * @param string[] $options {@see Lock::tryLock()}
     *
     * @return resource {@see Lock::tryLock()}
     *
     * @see RealLock::internalTryLock()
     */
    public function internalTryLock($identifier, $mode, array $options = null)
    {
        $lockMode = LOCK_EX;
        if (($mode & self::SHARED) == self::SHARED) {
            $lockMode = LOCK_SH;
        }
        if (($mode & self::NONBLOCKING) == self::NONBLOCKING) {
            $lockMode |= LOCK_NB;
        }
        if (self::useExistingFile($identifier, $options)) {
            if (is_resource($identifier)) {
                $result = $identifier;
            } else {
                $result = fopen($identifier, "w+");
            }
        } else {
            $result = fopen($this->getPathFromId($identifier), "w+");
        }
        if ($result) {
            if (!flock($result, $lockMode)) {
                fclose($result);
                $result = null;
            }
        } else {
            $result = null;
        }
        return $result;
    }

    /**
     * Calls flock with LOCK_UN on resource given as a handle
     *
     * @param resource $handle {@see Lock::unlock()}
     *
     * @return boolean {@see Lock::unlock()}
     *
     * @see RealLock::internalUnlock()
     */
    public function internalUnlock($handle)
    {
        $result = (is_resource($handle) && flock($handle, LOCK_UN));
        if ($result) {
            fclose($handle);
        }
        return $result;
    }

    /**
     * Unlink file identified by a parameter, making possible to create it anew
     * and lock in next tryLock call.
     * CAUTION: the existing file external will be deleted too in result of
     * method call
     *
     * @param mixed $identifier {@see Lock::forceUnlock()}
     * @param string[] $options {@see Lock::forceUnlock()}
     *
     * @return boolean {@see Lock::forceUnlock()}
     *
     * @see RealLock::internalUnlock()
     */
    public function internalForceUnlock($identifier, array $options = null)
    {
        $result = false;
        if (self::useExistingFile($identifier, $options)) {
            $path = $identifier;
        } else {
            $path = $this->getPathFromId($identifier);
        }
        if (is_file($path)) {
            $result = unlink($path);
        }
        return $result;
    }

    /**
     * Translates identifier to path including directory for dynamic lock files
     * storage. Needs 'dir' setting defined.
     *
     * @param mixed $identifier the resource identifier passed to lock/unlock
     *     methods
     *
     * @throws RuntimeException if 'dir' setting was not specified
     *
     * @return string dynamic file path
     */
    private function getPathFromId($identifier)
    {
        if ($this->lockDir == null) {
            throw new RuntimeException(
                "The locking directory is not specified in settings"
            );
        }
        $result = $this->lockDir . "/";
        if (is_object($identifier)) {
            $result .= str_replace('\\', '.', get_class($identifier));
        } elseif (is_string($identifier)) {
            $idnetifier = str_replace('\\', '.', $identifier);
            $result .= str_replace('/', '.', $identifier);
        } else {
            $result .= (string)$identifier;
        }
        return $result;
    }
}
