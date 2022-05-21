<?php
namespace App\Entity\Oauth;

use FOS\OAuthServerBundle\Entity\Client as BaseClient;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Client extends BaseClient{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    protected $appName;


    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */

    protected $description;


    public function __construct(){
        parent::__construct();       
    }
    

    /**
     * Get the value of appName
     */ 
    public function getAppName(){
        return $this->appName;
    }

    /**
     * Set the value of appName
     *
     * @return  self
     */ 
    public function setAppName($appName){
        $this->appName = $appName;

        return $this;
    }

    /**
     * Get the value of description
     */ 
    public function getDescription(){
        return $this->description;
    }

    /**
     * Set the value of description
     *
     * @return  self
     */ 
    public function setDescription($description){
        $this->description = $description;

        return $this;
    }
}
