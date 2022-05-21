<?php
namespace App\Repository;


use App\Helpers\Repository\RepositoryHelper;


class NotificationRepository extends RepositoryHelper{
    /**
     * @return Notification[]
     */
    public function getNotifications(){
        $qB=$this->createQueryBuilder('n');

        return $qB
            ->getQuery()
            ->getResult();
    }
    
    public function getNotification($notificationId){
        $qB=$this->createQueryBuilder('n');

        return $qB
            ->where($qB->expr()->eq('n.id',':notificationId'))
            ->setParameter('notificationId',$notificationId)
            ->getQuery()
            ->getResult();
    }

    public function deleteNotificationsOlderThanOneWeek(){
        $qB=$this->createQueryBuilder('n');
        $from=new \DateTime();
        $from->modify('-1 week');

        return $qB
            ->delete()
            ->andWhere('n.date < :from')
            ->setParameter('from', $from)
            ->getQuery()
            ->execute();
    }

   

}