<?php
namespace App\Helpers\Entity;

use App\Entity\User;

/**
 * Interface CreatedInterface
 */
interface madeByInterface
{
    /**
     * Set madeBy
     *
     * @param User $madeBy
     *
     * @return object
     */
    public function setMadeBy(User $madeBy);

    /**
     * Get madeBy
     *
     * @return User
     */
    public function getMadeBy();
}

