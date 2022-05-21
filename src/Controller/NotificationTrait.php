<?php
namespace App\Controller;

use App\Entity\Challenge\Notification;

trait NotificationTrait{

    public function getNotificationIdFromRequest(){
        /** @var HelperController $this */
        return $this->request->attributes->get("notificationId");
    }
    

    public function getNotification($notificationId):Notification{
        /** @var HelperController $this */
        $notification=$this->getManager()->getRepository(Notification::class)->find($notificationId);
        if(!$notificationId)
            throw new \Exception("Notification not exists",400);

        return $notification;
    }

    public function getNotificationFromRequest($request):Notification{
        $notificationId=$request->attributes->get("notificationId");
        return $this->getNotification($notificationId);
    }

}