<?php
/**
 * Created by PhpStorm.
 * User: jomady
 * Date: 24/07/21
 * Time: 20:42
 */

namespace App\EventListener;


use App\Entity\Cama;
use App\Entity\EstadoCama;
use App\Entity\Ingreso;
use Doctrine\ORM\Event\PreUpdateEventArgs;

class UpdateIngreso
{

    public function preUpdate(PreUpdateEventArgs  $args)
    {
        $entity = $args->getObject();
        if (!$entity instanceof Ingreso) {
            return;
        }

        $entityManager = $args->getObjectManager();
        if ($args->hasChangedField("cama")) {
            $cama_old = $args->getOldValue("cama");
            if ($cama_old != null) {
                $estado_cama = $entityManager->getRepository(EstadoCama::class)->Obtener("Disponible");
                $entityManager->getRepository(Cama::class)->ActualizarEstadoCama($cama_old->getId(), $estado_cama->getId());
            }

        }
    }
}