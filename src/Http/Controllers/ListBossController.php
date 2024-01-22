<?php

namespace NotFound\ListBoss\Http\Controllers;

use NotFound\Framework\Http\Controllers\Controller;
use NotFound\Layout\Elements\LayoutButton;
use NotFound\Layout\Elements\LayoutForm;
use NotFound\Layout\Helpers\LayoutWidgetHelper;

class ListBossController extends Controller
{
    public function index()
    {
        $widget = new LayoutWidgetHelper('Mailings', 'Resultaten van nieuwsbrieven inzien');
        $widget->addBreadcrumb('Mailings');

        $form = new LayoutForm('app/listboss/');

        $form->addButton(new LayoutButton('Bekijk resultaten', 'submit', 'primary'));
        $widget->widget->addForm($form);

        return $widget->response();
    }
}
