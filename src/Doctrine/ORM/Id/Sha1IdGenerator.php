<?php

namespace App\Doctrine\ORM\Id;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Id\AbstractIdGenerator;

class Sha1IdGenerator extends AbstractIdGenerator{
    public static $cache = [];

    /**
     * Generate sha1
     *
     * @param EntityManager $entityManager
     * @param mixed $entity
     *
     * @return string Sha1
     */
    public function generate(EntityManager $entityManager, $entity){
        if($entity->getId())
            return $entity->getId();
            
        return $this->getIdentifier($entity);
    }

    /**
     * Gets whether this generator is a post-insert generator which means that
     *
     * @return bool
     */
    public function isPostInsertGenerator(){
        return false;
    }

    public static function getIdentifier($entity){
        return sha1(get_class($entity).microtime().self::getHash());
    }

    /**
     * Get hash
     *
     * @param int    $chars
     * @param string $items
     *
     * @return string
     */
    public static function getHash($chars = 8, $items = 'abcdefghijklmnpqrstuvwxyz0123456789-_'){
        $output = '';
        $chars = (int) $chars;
        $nbr = strlen($items);
        if ($chars > 0 && $nbr > 0) {
            for ($i = 0; $i < $chars; ++$i) {
                $output .= $items[mt_rand(0, ($nbr - 1))];
            }
        }

        return $output;
    }


    public static function uniqueId(){
        return sha1(uniqid(sha1(microtime(true)),true));
    }
}
