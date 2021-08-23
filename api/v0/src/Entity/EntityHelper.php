<?php

namespace App\Entity;

use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class EntityHelper{

         /** Serializa una entidad y la convierte a json para respuesta */
         public function getSerializedEntity($entidad)
         {
    
             $encoders = [new XmlEncoder(), new JsonEncoder()];
             $normalizers = [new ObjectNormalizer()];        
             $serializer = new Serializer($normalizers, $encoders);
             return json_decode($serializer->serialize($entidad, 'json'));
    
         }  

}