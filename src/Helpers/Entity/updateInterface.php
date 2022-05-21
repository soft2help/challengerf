<?php
namespace App\Helpers\Entity;

/**
 * Interface 
 */
interface updateInterface
{
    /**
     * Set creation date
     *
     * @param \DateTime $date
     *
     * @return object
     */
    public function setUpdate(\DateTime $date = null);

    /**
     * Get creation date
     *
     * @return \DateTime
     */
    public function getUpdate();
}

