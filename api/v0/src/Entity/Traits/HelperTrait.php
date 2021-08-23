<?php

namespace App\Entity\Traits;

trait HelperTrait{

    public static function getFechaActual(): \DateTimeInterface
    {
        return $date = new \DateTime('@'.strtotime('now'));
    }

}