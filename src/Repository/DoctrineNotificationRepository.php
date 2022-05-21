<?php
namespace App\Repository;

use App\Entity\Challenge\Notification;

use Doctrine\ORM\EntityManagerInterface;

class DoctrineNotificationRepository
{
    private $entityManager;
    /** @var NotificationRepository $repository */
    private $repository;

    public function __construct(EntityManagerInterface $entityManager){
        $this->entityManager = $entityManager;
        $this->repository = $this->entityManager->getRepository(Notification::class);
    }

    

    public function get($id): ?Notification{
        return $this->repository->find($id);
    }

    public function deleteOlderNotications(){
        $this->repository->deleteNotificationsOlderThanOneWeek();
    }

    // other methods, that you defined in your repository's interface.
}