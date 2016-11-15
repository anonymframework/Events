<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include "vendor/autoload.php";
$event = new \Sagi\Event\EventDispatcher();

use Sagi\Event\Event;
use Sagi\Event\SubscriberInterface;

class MyEvent extends \Sagi\Event\Event {

}

class MyEventListener extends \Sagi\Event\EventListener  implements SubscriberInterface{
    /**
     * @return array
     */
    public function getSubscribedEvents()
    {
        return array(
            'update' => 'updateItems',
            'delete' => 'DeleteItems',
        );
    }

    public function updateItems(){
        echo 'update fired';
    }

    public function DeleteItems(){
        echo 'delete fired';
    }
}
$myevent = new MyEvent();


$event->listen($myevent, new MyEventListener());

var_dump($event->fire($myevent));