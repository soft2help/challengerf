<?php
namespace App\Controller\User;

use App\Entity\User;
use App\Controller\HelperController;

trait UserTrait{    

    public function getUserIdFromRequest(){
        /** @var HelperController $this */
        return $this->request->attributes->get("userId");
    }
        
    public function getUserById($userId):User{
        /** @var HelperController $this */
        $user=$this->getManager()->getRepository(User::class)->find($userId);
        if(!$user)
            throw new \Exception("User not exists",400);

        return $user;
    }
    
    public function getUserFromRequest():User{
        /** @var HelperController|$this $this */
        $userId=$this->getUserIdFromRequest();
        
        return $this->getUserById($userId);
    }

}
