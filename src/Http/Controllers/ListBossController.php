<?php

namespace NotFound\ListBoss\Http\Controllers;

use NotFound\Framework\Http\Controllers\Controller;
use NotFound\Layout\Elements\LayoutButton;
use NotFound\Layout\Elements\LayoutForm;
use NotFound\Layout\Helpers\LayoutWidgetHelper;
use NotFound\Layout\Inputs\LayoutInputDropdown;
use NotFound\ListBoss\Helpers\ListBoss;

class ListBossController extends Controller
{
    public function index()
    {

        $listBoss = new ListBoss();

        $jobs = $listBoss->list();

        $widget = new LayoutWidgetHelper('Mailings', 'Resultaten van nieuwsbrieven inzien');
        $widget->addBreadcrumb('Mailings');

        $form = new LayoutForm('app/listboss/');

        $dropDown = new LayoutInputDropdown('list', 'Kies een mailing');

        foreach ($jobs as $job) {
            $dropDown->addOption($job->id(), $job->subject());
        }

        $dropDown
            ->setRequired();

        $form->addInput($dropDown);

        $widget->widget->addForm($form);

        $form->addButton(new LayoutButton('Bekijk resultaten', 'submit', 'primary'));

        return $widget->response();
    }
}
