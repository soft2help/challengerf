<?php
namespace App\Message;

class SendEmail{
    /**
     * @var string
     */
    private $notificationId;
    
    /**
     * @var string
     */
    private $htmlEmail;

    public function __construct(string $notificationId, string $htmlEmail){
        $this->notificationId = $notificationId;
        $this->htmlEmail = $htmlEmail;
    }

    public function getNotificationId(): string
    {
        return $this->notificationId;
    }


    public function getHtmlEmail(): string
    {
        return $this->htmlEmail;
    }
}