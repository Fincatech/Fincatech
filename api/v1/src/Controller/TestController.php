<?php

namespace Fincatech\Controller;

use Fincatech\Controller\RemesaDetalleController;
use Fincatech\Entity\RemesaDetalle;
use phpseclib3\Net\SFTP;

class TestController extends FrontController{


    public function __construct($params = null)
    {
    }

    public function Test()
    {
        $r = new RemesaDetalleController();
        $r1 = $r->GetByUniqueId('0000910009d84F24/000001');


    }

}