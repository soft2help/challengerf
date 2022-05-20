<?php
namespace App\DataFixtures\Traits;


use App\Entity\User;
use App\DataFixtures\BaseFixture;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;

trait UsuarioTrait{
    public static $roles=['ROLE_SUPER_ADMIN', 'ROLE_USER'];
    
    public function genUsuario($roleUsuario, $email=null,$defaultPassword=null,$userNameIsEmail=true) {
        /** @var BaseFixture $this */

        if(!$email)
            $email=$this->faker->email;

        if(!$defaultPassword)
            $defaultPassword=$this->faker->password;

        $username=$email;

        if(!$userNameIsEmail)
            $username=$this->faker->userName;
        
        $user = new User();       
        $user->setUsername($username);
        $user->setEmail($email);        
        $user->setSalt(md5(uniqid()));

        $user->setPlainPassword($defaultPassword);   
        $encoder = new MessageDigestPasswordEncoder('sha512', true, 5000);
     
        $password = $encoder->encodePassword($defaultPassword, $user->getSalt());
       
        $user->setPassword($password);

        $user->setEnabled(true);
        
        if(in_array($roleUsuario,self::$roles))
            $user->setRoles(["{$roleUsuario}"]);

        $this->userManager->updateCanonicalFields($user);

        return $user;
    }

}