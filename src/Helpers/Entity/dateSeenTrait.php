<?php
namespace App\Helpers\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

trait dateSeenTrait{

    /**
     * @var \DateTime
     * @ORM\Column(name="DateSeen", type="datetime", nullable=true)
     * @Assert\DateTime()
     * @Groups({"dateseen"})
     */
    private $dateSeen;



    /**
     * Undocumented function
     *
     * @return \DateTime
     */
    public function getDateSeen(){
        return $this->dateSeen;
    }

    /**
     * Set dateseen
     *
     * @param \DateTime $dateSeen dateseen
     *
     * @return $this
     */
    public function setDateSeen(\DateTime $dateSeen = null){
        if(!$dateSeen)
            $dateSeen=new \DateTime();
            
        $this->dateSeen=$dateSeen;

        return $this;
    }


}