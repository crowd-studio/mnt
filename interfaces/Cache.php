<?php

/**
 * Singleton class
 *
 */
final class Trans
{
	protected static $instance = null;
    /**
     * Call this method to get singleton
     *
     * @return UserFactory
     */
    public static function Instance()
    {
        if ($this->inst === null) {
            $this->inst = new Trans();
        }
        return $this->inst;
    }

    /**
     * Private ctor so nobody else can instance it
     *
     */
    protected function __construct(){}

    /**
     * Private ctor so nobody else can instance it
     *
     */
    protected function __clone(){}
}