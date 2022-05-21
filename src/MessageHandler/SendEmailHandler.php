<?php
namespace App\MessageHandler;

use App\Entity\Challenge\Notification;
use App\Message\SendEmail;
use App\Repository\DoctrineNotificationRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;


class SendEmailHandler implements MessageHandlerInterface
{
    private $notificationRepository;
    private $mailer;

    public function __construct(DoctrineNotificationRepository $notificationRepository, \Swift_Mailer $mailer){
        $this->notificationRepository = $notificationRepository;
        $this->mailer=$mailer;
    }

    public function __invoke(SendEmail $sendEmail){
        /** @var Notification $notification */
        $notification = $this->notificationRepository->get($sendEmail->getNotificationId());
        
        

        $htmlEmail = $sendEmail->getHtmlEmail();
        $player=$notification->getPlayer()->getName();
        $message=$notification->getMessage();
        $htmlEmail = str_replace("{player}",$player,$htmlEmail);
        $htmlEmail = str_replace("{message}",$message,$htmlEmail);

        $txtplain="New Notification! {$player} with message {$message}";

        $subscriptions=$notification->getPlayer()->getSubscriptions();
        
        $addresses=[];
        foreach($subscriptions as $subscription){
            $addresses[]=$subscription->getMadeBy()->getEmail();
        }

        if(empty($addresses))
            return;

        $message = (new \Swift_Message('Player Notification'))
        ->setFrom('noreply@soft2help.net')
        ->setTo($addresses)
        ->setBody(
            $htmlEmail,
            'text/html'
        )

        // you can remove the following code if you don't define a text version for your emails
        ->addPart( $txtplain,
            'text/plain'
        );

        $this->mailer->send($message);

    }
}