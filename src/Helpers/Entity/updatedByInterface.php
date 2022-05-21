<?php
namespace App\Helpers\Entity;

use App\Entity\User;

/**
 * Interface UpdatedBy
 */
interface updatedByInterface
{
    /**
     * Set updatedBy
     *
     * @param User $updatedBy
     *
     * @return object
     */
    public function setUpdatedBy(User $updatedBy);

    /**
     * Get updatedBy
     *
     * @return User
     */
    public function getUpdatedBy();
}

