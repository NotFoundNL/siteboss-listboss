<?php

namespace NotFound\ListBoss\Http\Controllers;

use NotFound\Framework\Http\Controllers\Controller;
use NotFound\Framework\Http\Requests\FormDataRequest;
use NotFound\Layout\Elements\LayoutBar;
use NotFound\Layout\Elements\LayoutPager;
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
        $widget->widget->noPadding();
        $widget->widget->addTable($this->selectJob());

        return $widget->response();
    }

    public function status(FormDataRequest $request, int $list)
    {
        if (! config('listboss.backend')) {
            abort(404);
        }

        // Create job
        $job = new Job($list);

        // Get results
        $validated = $request->validate([
            'sort' => 'string|in:opens,clicks,send_status',
            'asc' => 'string|in:true,false',
            'page' => 'integer|min:1',
        ]);

        $jobResults = $job->result(
            sort: $validated['sort'] ?? 'opens',
            page: $validated['page'] ?? 1,
            direction: (isset($validated['asc']) && $validated['asc'] === 'true') ? 'asc' : 'desc',
        );

        $widget = new LayoutWidgetHelper('E-mails', 'Status: '.$jobResults->message);
        $widget->widget->noPadding();
        $widget->addBreadcrumb('Verzendingen', '/app/listboss/');

        $bar = new LayoutBar();
        $widget->widget->addText(new LayoutText('Aantal ontvangers: '.$jobResults->recipients));
        $widget->widget->addText(new LayoutText('Aantal afgeleverd: '.$jobResults->delivered));
        $widget->widget->addText(new LayoutText('Aantal fouten: '.$jobResults->failed));
        $widget->widget->addText(new LayoutText('Aantal ontvangers geopend: '.$jobResults->opens));
        $widget->widget->addText(new LayoutText('Aantal ontvangers geklikt: '.$jobResults->clicks));

        $pager = new LayoutPager($jobResults->recipients, 100);

        $bar->addPager($pager);
        $widget->widget->addBar($bar);

        $table = new LayoutTable(sort: false, delete: false, create: false);

        $table->addHeader(new LayoutTableHeader('E-mailadres', 'email'));
        $table->addHeader(new LayoutTableHeader('Ontvangen', 'received'));
        $table->addHeader((new LayoutTableHeader('Status', 'send_status'))->sortable());
        $table->addHeader((new LayoutTableHeader('Geopend', 'opens'))->sortable());
        $table->addHeader((new LayoutTableHeader('Geklikt', 'clicks'))->sortable());

        $rowId = 1;

        foreach ($jobResults->results as $result) {
            $row = new LayoutTableRow($rowId++, '/app/listboss/'.$job->id().'/'.$result->id);
            $row->addColumn(new LayoutTableColumn($result->email));
            $row->addColumn(new LayoutTableColumn(\Sb::formatDate($result->delivered_at)));
            $row->addColumn(new LayoutTableColumn($result->send_status ?? '-'));
            $row->addColumn(new LayoutTableColumn($result->opens));
            $row->addColumn(new LayoutTableColumn($result->clicks));
            $table->addRow($row);
        }

        $widget->widget->addTable($table);

        return $widget->response();
    }

    private function statusText(object $statusInfo): LayoutText
    {
        $text =
            '<p>Aantal ontvangers: '.($statusInfo->recipients ?? '-').'</p>'.
            '<p>Ontvangers dat mail in mailprogramma heeft geopend: '.($statusInfo->opens ?? '-').'</p>'.
            '<p>Ontvangers dat op links heeft geklikt: '.($statusInfo->clicks ?? '-').'</p>'.
            '<p>Voortgang: <strong>'.($statusInfo->progress ?? '0').'%</strong></p>'.
            '<p>Fouten: '.($statusInfo->errors ?? '0').'</p>';

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
