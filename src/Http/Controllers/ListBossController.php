<?php

namespace NotFound\ListBoss\Http\Controllers;

use NotFound\Framework\Http\Controllers\Controller;
use NotFound\Framework\Http\Requests\FormDataRequest;
use NotFound\Layout\Elements\LayoutButton;
use NotFound\Layout\Elements\LayoutForm;
use NotFound\Layout\Elements\LayoutText;
use NotFound\Layout\Helpers\LayoutWidgetHelper;
use NotFound\Layout\Inputs\LayoutInputDropdown;
use NotFound\ListBoss\Helpers\Job;
use NotFound\ListBoss\Helpers\ListBoss;

class ListBossController extends Controller
{
    public function index()
    {
        if( !config('listboss.backend'))
        {
            abort(404);
        }

        $widget = new LayoutWidgetHelper('Mailings', 'Resultaten van verzendingen inzien');
        $widget->addBreadcrumb('Verzendingen');

        $widget->widget->addForm($this->selectJob());

        return $widget->response();
    }

    public function status(FormDataRequest $request)
    {
        if( !config('listboss.backend'))
        {
            abort(404);
        }

        $request->validate([
            'list' => 'required|integer',
        ]);
        $job = new Job($request->get('list'));

        $widget = new LayoutWidgetHelper('Verzending', $job->subject() ?? 'Verzending');
        $widget->addBreadcrumb('Mailings', '/app/listboss/');
        $widget->addBreadcrumb('Resultaten van verzendingen inzien', '/app/listboss/');
        $widget->widget->addText(new LayoutText('Status: '.$job->status()->getReadableName()));

        $widget->widget->addText($this->statusText($job->statusInfo()));
        $widget->widget->addForm($this->selectJob());

        return $widget->response();
    }

    private function statusText(object $statusInfo): LayoutText
    {
        $text = '<p>Aantal ontvangers: '.($statusInfo->recipients ?? '-').
        '<p>Voortgang: <strong>'.($statusInfo->progress ?? '0').'%</strong>'.
        '<p>Fouten: '.($statusInfo->errors ?? '0');

        return new LayoutText($text);
    }

    private function selectJob(): LayoutForm
    {
        $listBoss = new ListBoss();
        $jobs = $listBoss->list();

        $form = new LayoutForm('app/listboss/details');

        $dropDown = new LayoutInputDropdown('list', 'Kies een mailing');
        $dropDown->setRequired();

        foreach ($jobs as $job) {
            if ($job->started()) {
                $dropDown->addOption($job->id(), $job->subject());
            }
        }

        $form->addInput($dropDown);

        $form->addButton(new LayoutButton('Bekijk resultaten', 'submit', 'primary'));

        return $form;
    }
}
