<?php
/**
 *  SAGI DATABASE ORM FILE
 *
 */

namespace Sagi\Event;


interface SubscriberInterface
{
    /**
     * @return array
     */
    public  function getSubscribedEvents();
}