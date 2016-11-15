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

use Sagi\Event\Event as EventDispatch;
use Sagi\Event\EventCollector;
use Sagi\Event\EventListener;
use Closure;

/**
 *
 * Class Event
 * @package Anonym
 */
class EventDispatcher
{
    /**
     * store the list of fired events
     *
     * @var EventDispatch
     */
    private $firing;


    /**
     * create a new instance and registerde event collector
     *
     */
    public function __construct()
    {

        $this->listeners = EventCollector::getListeners();
    }

    /**
     * execute the event
     *
     * @param string|EventDispatch $event the name of event.
     * @param array $parameters the parameters for closure events
     * @return array the response
     * @throws EventException
     * @throws EventListenerException
     * @throws EventNameException
     * @throws EventNotFoundException
     */
    public function fire($event = null, $parameters = [])
    {

        if (!is_array($parameters)) {
            $parameters = [$parameters];
        }

        $response = [];
        list($listeners, $event) = $this->resolveEventAndListeners($event);


        foreach ($listeners as $listener) {

            if ($listener instanceof Closure) {
                $response[] = $this->resolveClosureListener($listener,
                    $parameters);
            }elseif(is_object($listener)){
                $response[] = $this->resolveObjectListener(
                    $listener,
                    $event
                );
            }elseif(is_array($listener)){
                $response[] = $this->resolveArrayListener($listener, $event, $parameters);
            }
        }


        return $this->resolveResponseArray($response);
    }

    /**
     * resolve the return parameter
     *
     * @param array $response
     * @return mixed
     */
    private function resolveResponseArray(array $response)
    {
        if ($count = count($response)) {
            if ($count === 1) {
                $response = $response[0];
            }
        }
        $this->firing[] = $response;

        return $response;
    }

    /**
     * @param array $listeners
     * @param Event $event
     * @param array $parameters
     * @return array
     */
    private function resolveArrayListener(array $listeners, EventDispatch $event, array  $parameters = [])
    {

         $response = $this->resolveObjectListener([$listeners[0], $listeners[1]], $event, $parameters);



        return $response;
    }
    /**
     * resolve the object listener
     *
     * @param \Sagi\Event\EventListener $listener
     * @param Event $event
     * @param array $params
     * @return mixed
     */
    private function resolveObjectListener($listener, EventDispatch $event,array $params = [])
    {
        array_push($params, $event);

        $method = is_array($listener) ? $listener[1] : 'handle';

        $listener = is_array($listener) ? $listener[0] : $listener;

        if (!method_exists($listener, $method)) {
            throw new EventListenerException(sprintf('%s listener does not have %s method', get_class($listener), $method));
        }

        return call_user_func_array([$listener, $method], $params);
    }

    /**
     * resolve the callable listener
     *
     * @param Closure $listener
     * @param array $parameters
     * @return mixed
     */
    private function resolveClosureListener(Closure $listener, array $parameters)
    {
        return call_user_func_array($listener, $parameters);
    }

    /**
     * resolve the event and listener
     *
     * @param mixed $event
     * @throws EventListenerException
     * @return null|string|EventDispatch
     */
    private function resolveEventAndListeners($event)
    {

        if (is_object($event) && $event instanceof EventDispatch) {
            $name = get_class($event);
        } else {
            $name = $event;
        }

        if (is_string($name)) {
            if ($this->hasListiner($name) && $listeners = $this->getListeners($name)) {
                if (count($listeners) === 1) {
                    $listeners = $listeners[0];
                    $listeners = [$listeners instanceof Closure ? $listeners : (new $listeners)];
                }
            } else {
                throw new EventListenerException(sprintf('Your %s event havent got listener', $event));
            }
        }

        return [$listeners, $event];
    }

    /**
     * register a new listener
     *
     * @param string|Event $name the name or instance of event
     * @param string|EventListener $listenerInterface the name or instance of event listener
     * @return $this
     */
    public function listen($name, $listenerInterface)
    {

        $name = is_object($name) ? get_class($name) : $name;

        if ($listenerInterface instanceof EventListener && $listenerInterface instanceof SubscriberInterface) {
            $listeners = $listenerInterface->getSubscribedEvents();

            foreach ($listeners as $listener => $callback) {
                if (!$callback instanceof Closure) {
                    $callback = [$listenerInterface, $callback];
                }

                EventCollector::addListener($name, $callback);
            }

        } else {
            EventCollector::addListener($name, $listenerInterface);
        }

        return $this;
    }


    /**
     * return the registered listeners
     *
     * @param string $eventName get the event listeners
     * @return mixed
     * @throws EventNameException
     */
    public function getListeners($eventName = '')
    {
        if (!is_string($eventName)) {
            throw new EventNameException('Event adı geçerli bir string değeri olmalıdır');
        }

        return EventCollector::getListeners()[$eventName];
    }

    /**
     * check the isset any listener
     *
     * @param string $eventName the name of event
     * @return bool
     */
    public function hasListiner($eventName = '')
    {
        $listeners = EventCollector::getListeners();

        return isset($listeners[$eventName]);
    }

    /**
     * get the last fired event response
     *
     * @return mixed
     */
    public function firing()
    {
        return end($this->firing);
    }
}