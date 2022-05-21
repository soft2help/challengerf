<?php
namespace App\Helpers\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

trait updateTrait{

    /**
     * @var \DateTime
     * @ORM\Column(name="UpdateDate", type="datetime", nullable=true)
     * @Assert\DateTime()
     * @Groups({"update"})
     */
    private $update;



    /**
     * Undocumented function
     *
     * @return \DateTime
     */
    public function getUpdate(){
        return $this->update;
    }

    /**
     * Set fecha
     *
     * @param \DateTime|null $update
     *
     * @return $this
     */
    public function setUpdate(?\DateTime $update = null){
        $this->update=$update;

        return $this;
    }


}