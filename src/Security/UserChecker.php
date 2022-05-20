<?php
namespace App\Security;

use App\Entity\User;
use App\Exception\AccountDeletedException;
use App\Helpers\Exceptions\GeneralException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccountExpiredException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;

class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user){
        if (!$user instanceof User) {
            return;
        }

        if(!$user->getActivo()){            
            throw new AuthenticationException('El usuario no está activo');
        }

        if($user->hasRole("ROLE_SUPER_ADMIN"))
            return;

        
    }

    public function checkPostAuth(UserInterface $user){
        
    }
}