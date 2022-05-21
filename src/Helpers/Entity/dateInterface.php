<?php
namespace App\Helpers\Entity;

/**
 * Interface dateInterface
 */
interface dateInterface
{
    /**
     * Set creation date
     *
     * @param \DateTime $date
     *
     * @return object
     */
    public function setDate(\DateTime $date = null);

    /**
     * Get creation date
     *
     * @return \DateTime
     */
    public function getDate();
}

