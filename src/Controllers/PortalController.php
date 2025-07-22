<?php

namespace App\Controllers;

class PortalController extends BaseController
{

    public function index()
    {
        $this->render('Views/portal/index.php', [
            'pageTitle' => 'Portal del Colaborador'
        ]);
    }
}
