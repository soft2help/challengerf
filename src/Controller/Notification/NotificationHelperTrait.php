<?php
namespace App\Controller\Notification;



use App\Entity\Challenge\Notification;
use App\Controller\HelperController;
use App\Helpers\Exceptions\NotFound;
use App\Controller\NotificationTrait as ControllerNotificationTrait;

trait NotificationHelperTrait{
    use ControllerNotificationTrait;

    private function getNotificationFromRequest():Notification{
        /** @var HelperController|ControllerNotificationTrait $this */

        $notificationId=$this->getNotificationIdFromRequest();
        
        return $this->getNotification($notificationId);
    }

    


    

   
}