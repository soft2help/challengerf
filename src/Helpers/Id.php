<?php
namespace App\Helpers;

use App\Doctrine\ORM\Id\Sha1IdGenerator;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Swagger\Annotations as SWG;

trait Id{

    /**
     * @var string|null
     * @ORM\Id
     * @ORM\Column(name="Id", type="string", length=40, nullable=true)
     * @SWG\Property(example="f3a82dc40d20c4da2b7fcbd7d2fb2f4871470c67", description="Primeray key de la entidad") 
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="App\Doctrine\ORM\Id\Sha1IdGenerator")
     * @Groups({"Id"})
     */
    protected $id;


    protected $storeId;



    /**
     * Get the value of id
     */ 
    public function getId($generateId=false){
        if($generateId && !$this->id)
            $this->id=Sha1IdGenerator::getIdentifier($this);

        return $this->id;
    }

    /**
     * Set the value of id
     *
     * @return  self
     */ 
    public function setId(?string $id){
        $this->id = $id;

        return $this;
    }



    public function storeId(){
        $this->storeId=$this->id;
    }

    /**
     * Get the value of storeId
     */ 
    public function getStoreId(){
        return $this->storeId;
    }

    /**
     * Set the value of storeId
     *
     * @return  self
     */ 
    public function setStoreId($storeId){
        $this->storeId = $storeId;

        return $this;
    }
}