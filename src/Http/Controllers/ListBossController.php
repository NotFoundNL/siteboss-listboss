<?php

namespace NotFound\ListBoss\Http\Controllers;

use NotFound\Framework\Http\Controllers\Controller;
use NotFound\Framework\Http\Requests\FormDataRequest;
use NotFound\Layout\Elements\LayoutText;
use NotFound\Layout\Elements\Table\LayoutTable;
use NotFound\Layout\Elements\Table\LayoutTableColumn;
use NotFound\Layout\Elements\Table\LayoutTableHeader;
use NotFound\Layout\Elements\Table\LayoutTableRow;
use NotFound\Layout\Helpers\LayoutWidgetHelper;
use NotFound\ListBoss\Helpers\Job;
use NotFound\ListBoss\Helpers\ListBoss;

class ListBossController extends Controller
{
    public function index()
    {
        if (! config('listboss.backend')) {
            abort(500, 'ListBoss settings are not set');
        }

        $widget = new LayoutWidgetHelper('E-mails', 'Verzendingen');
        $widget->widget->addTable($this->selectJob());

        return $widget->response();
    }

    public function status(FormDataRequest $request, int $list)
    {
        if (! config('listboss.backend')) {
            abort(404);
        }

        $job = new Job($list);

        $widget = new LayoutWidgetHelper('E-mails', 'Status');
        $widget->addBreadcrumb('Verzendingen', '/app/listboss/');
        $widget->widget->addText(new LayoutText('Status: '.$job->status()->getReadableName()));

        $widget->widget->addText($this->statusText($job->statusInfo()));

        $table = new LayoutTable(sort: false, delete: false, create: false);

        $table->addHeader(new LayoutTableHeader('E-mailadres', 'email'));
        $table->addHeader(new LayoutTableHeader('Ontvangen', 'received'));
        $table->addHeader(new LayoutTableHeader('Geklikt', 'clicks'));
        $table->addHeader(new LayoutTableHeader('Geopend', 'opens'));

        $rowId = 1;
        foreach ($job->result()->results as $result) {
            $row = new LayoutTableRow($rowId++, '/app/listboss/'.$job->id().'/'.$result->id);
            $row->addColumn(new LayoutTableColumn($result->email));
            $row->addColumn(new LayoutTableColumn(\Sb::formatDate($result->delivered_at)));
            $row->addColumn(new LayoutTableColumn($result->clicks));
            $row->addColumn(new LayoutTableColumn($result->opens));
            $table->addRow($row);
        }

        $widget->widget->addTable($table);

        return $widget->response();
    }

    private function statusText(object $statusInfo): LayoutText
    {
        $text = '<p>Aantal ontvangers: '.($statusInfo->recipients ?? '-').
        '<p>Voortgang: <strong>'.($statusInfo->progress ?? '0').'%</strong>'.
        '<p>Fouten: '.($statusInfo->errors ?? '0');

        return new LayoutText($text);
    }

    private function selectJob(): LayoutTable
    {
        $listBoss = new ListBoss();
        $jobs = $listBoss->list();

        $table = new LayoutTable(delete: false, create: false, edit: true, sort: false);
        $table->addHeader(new LayoutTableHeader('Onderwerp', 'email'));
        foreach ($jobs as $job) {
            $row = new LayoutTableRow($job->id(), '/app/listboss/'.$job->id().'/');
            $row->addColumn(new LayoutTableColumn($job->subject()));
            $table->addRow($row);
        }

        return $table;
    }
}
