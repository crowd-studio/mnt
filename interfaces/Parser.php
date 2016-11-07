<?php

/**
 * Singleton class
 *
 */
final class Parser
{
	protected static $inst = null;
    /**
     * Call this method to get singleton
     *
     * @return UserFactory
     */
    public static function Instance()
    {
        if (self::$inst === null) {
            self::$inst = new Parser();
        }
        return self::$inst;
    }

    /**
     * Private ctor so nobody else can instance it
     *
     */
    protected function __construct()
    {

    }

    /**
     * Private ctor so nobody else can instance it
     *
     */
    protected function __clone()
    {

    }
}