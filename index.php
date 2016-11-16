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
            'updateItems',
            'DeleteItems',
        );
    }

    public function updateItems($id){

    }

    public function DeleteItems(){
        echo 'delete fired';
    }
}

$event->subscribe('event', new MyEventListener());

$event->fire('event', [
    'id' => 1
]);