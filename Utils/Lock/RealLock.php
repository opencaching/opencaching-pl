<?php
/**
 * Contains \Utils\Lock\RealLock class definition.
 */
namespace Utils\Lock;

use \RuntimeException;

/**
 * Superclass of every class implementing real locking mechanism, like f.ex.
 * file locking.
 */
abstract class RealLock extends Lock
{
    /**
     * Checks if object creation is done exactly from \Utils\Lock\Lock
     * class, not from external code. If so, calls {@see internalConstruct()}
     * then.
     *
     * @param string[] $settings the real locking mechanism implementation
     *     settings
     */
    final public function __construct($settings)
    {
        $trace = debug_backtrace();
        if (
            empty($trace[1])
            || !isset($trace[1]['class'])
            || $trace[1]['class'] !== __NAMESPACE__ . "\\Lock"
        ) {
            throw new RuntimeException(
                "caller has to be the " . __NAMESPACE__ . "\\Lock itself class"
            );
        }
        $this->internalConstruct($settings);
    }

    /**
     * Serves as a constructor specific for implemented real locking mechanism
     * class
     *
     * @param string[] $settings the real locking mechanism implementation
     *     settings
     */
    abstract protected function internalConstruct($settings);
    
    /**
     * Internal tryLock method declaration. Should be implemented by the real
     * locking class to realize the actual lock set up.
     *
     * @param mixed $identifier {@see Lock::tryLock()}
     * @param int $mode {@see Lock::tryLock()}
     * @param string[] $options {@see Lock::tryLock()}
     *
     * @return resource {@see Lock::tryLock()}
     */
    abstract public function internalTryLock(
        $identifier,
        $mode,
        array $options = null
    );

    /**
     * Internal unlock method declaration. Should be implemented by the real
     * locking class to unlock the previously set up lock.
     *
     * @param resource $handle {@see Lock::unlock()}
     *
     * @return boolean {@see Lock::unlock()}
     */
    abstract public function internalUnlock($handle);

    /**
     * Internal forceUnlock method declaration. Should be implemented by the
     * real locking class to forcedly unlock the resource given by identifier.
     *
     * @param mixed $identifier {@see Lock::forceUnlock()}
     * @param string[] $options {@see Lock::forceUnlock()}
     *
     * @return boolean {@see Lock::forceUnlock()}
     */
    abstract public function internalForceUnlock(
        $identifier,
        array $options = null
    );
}

