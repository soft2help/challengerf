<?php
namespace App\Helpers\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;
use Swagger\Annotations as SWG;

trait dateTrait{

    /**
     * @var \DateTime
     * @ORM\Column(name="CreateDate", type="datetime", nullable=false)
     * @Assert\DateTime()
     * @SWG\Property(example="22/09/2020 20:20:30", description="format date it will depends of denormalizer or normalizer") 
     * @Groups({"date"})
     */
    private $date;



    /**
     * Undocumented function
     *
     * @return \DateTime
     */
    public function getDate(){
        return $this->date;
    }

    /**
     * Set date
     *
     * @param \DateTime $date date
     *
     * @return $this
     */
    public function setDate(\DateTime $date = null){
        $this->date=$date;

        return $this;
    }


}