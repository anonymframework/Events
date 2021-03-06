<?php
/**
 * This file belongs to the AnoynmFramework
 *
 * @author vahitserifsaglam <vahit.serif119@gmail.com>
 * @see http://gemframework.com
 *
 * Thanks for using
 */

namespace Sagi\Event;

/**
 * the class of event collector
 *
 * Class EventCollector
 * @package Sagi\Event
 */
class EventCollector
{

    /**
     * the list of event
     *
     * @var array
     */
    private static $listeners;

    /**
     * get the registered listeners
     *
     * @return array
     */
    public static function getListeners()
    {
        return self::$listeners;
    }

    /**
     * register the listeners
     *
     * @param array $listeners
     */
    public static function setListeners($listeners)
    {
        foreach ($listeners as $name => $listener) {
            static::addListener($name, $listener);
        }
    }


    /**
     * register a new listener
     *
     * @param string|Event $name the name or instance of event
     * @param string|EventListener $listener the name or instance of event listener
     */
    public static function addListener($name, $listener)
    {
        if (!isset(static::$listeners[$name])) {
            static::$listeners[$name] = [];
        }

        static::$listeners[$name][] = $listener;
    }
}