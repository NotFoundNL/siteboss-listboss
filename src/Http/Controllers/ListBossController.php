<?php

namespace NotFound\ListBoss\Http\Controllers;

use NotFound\Framework\Http\Controllers\Controller;
use NotFound\Framework\Http\Requests\FormDataRequest;
use NotFound\Layout\Elements\LayoutBar;
use NotFound\Layout\Elements\LayoutBarButton;
use NotFound\Layout\Elements\LayoutBreadcrumb;
use NotFound\Layout\Elements\LayoutPage;
use NotFound\Layout\Elements\LayoutPager;
use NotFound\Layout\Elements\LayoutSearchBox;
use NotFound\Layout\Elements\LayoutText;
use NotFound\Layout\Elements\LayoutWidget;
use NotFound\Layout\Elements\Table\LayoutTable;
use NotFound\Layout\Elements\Table\LayoutTableColumn;
use NotFound\Layout\Elements\Table\LayoutTableHeader;
use NotFound\Layout\Elements\Table\LayoutTableRow;
use NotFound\Layout\Helpers\LayoutWidgetHelper;
use NotFound\Layout\LayoutResponse;
use NotFound\ListBoss\Helpers\Job;
use NotFound\ListBoss\Helpers\ListBoss;

class ListBossController extends Controller
{
    public function index()
    {
        if (! config('listboss.backend')) {
            $widget = new LayoutWidgetHelper('Probleem', 'Configuratie niet correct ingesteld');
            $widget->widget->addText(new LayoutText('De configuratie voor ListBoss is niet correct ingesteld.'));

            return $widget->response();
        }

        $widget = new LayoutWidgetHelper('Resultaten van verzending', 'Verzendingen');
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
            'search' => 'string|nullable',
        ]);

        $currentPage = $validated['page'] ?? 1;

        $jobResults = $job->result(
            sort: $validated['sort'] ?? 'opens',
            page: $currentPage,
            query: $validated['search'] ?? null,
            direction: (isset($validated['asc']) && $validated['asc'] === 'true') ? 'asc' : 'desc',
        );

        $response = new LayoutResponse();

        $page = new LayoutPage('Resultaten van verzending');
        $breadcrumb = new LayoutBreadcrumb();
        $breadcrumb->addHome();
        $breadcrumb->addItem('Verzendingen', '/app/listboss/');

        $breadcrumb->addItem('Resultaat');
        $page->addBreadcrumb($breadcrumb);
        if ($currentPage == 1) {
            $widget = new LayoutWidget('Samenvatting resultaten', 12);

            $bar = new LayoutBar();
            $bar->removePadding();

            $bar->addBarButton((new LayoutBarButton('Uitleg'))->setLink('/app/listboss/docs'));

            $widget->addBar($bar);

            $widget->addText(new LayoutText('Status: '.$jobResults->message));
            if (isset($job->result()->results)) {

                $widget->addText(new LayoutText('Aantal ontvangers: '.$jobResults->recipients));
                $widget->addText(new LayoutText('Aantal afgeleverd: '.$jobResults->delivered));
                $widget->addText(new LayoutText('Aantal fouten: '.$jobResults->failed));
                $widget->addText(new LayoutText('Aantal ontvangers geopend: '.$jobResults->opens));
                $widget->addText(new LayoutText('Aantal ontvangers geklikt: '.$jobResults->clicks));
            }
            $page->addWidget($widget);
        }

        $widget = new LayoutWidget('Technische details', 12);
        $widget->noPadding();

        $bar = new LayoutBar();

        $pager = new LayoutPager($jobResults->recipients, 100);
        $bar->addPager($pager);
        $search = new LayoutSearchBox('Zoek e-mailadres');
        $bar->addSearchBox($search);

        $widget->addBar($bar);

        if (! isset($job->result()->results)) {
            $widget->addText(new LayoutText($job->result()->message ?? 'De status is onbekend, er is mogelijk iets misgegaan.'));
        } else {
            $rowId = 1;

            $table = new LayoutTable(sort: false, delete: false, create: false);

            $table->addHeader(new LayoutTableHeader('E-mailadres', 'email'));
            $table->addHeader(new LayoutTableHeader('Ontvangen', 'received'));
            $table->addHeader(new LayoutTableHeader('Geklikt', 'clicks'));
            $table->addHeader(new LayoutTableHeader('Geopend', 'opens'));
            foreach ($job->result()->results as $result) {
                $row = new LayoutTableRow($rowId++, '/app/listboss/'.$job->id().'/'.$result->id);
                $row->addColumn(new LayoutTableColumn($result->email));
                $row->addColumn(new LayoutTableColumn(\Sb::formatDate($result->delivered_at)));
                $row->addColumn(new LayoutTableColumn($result->send_status ?? '-'));
                $row->addColumn(new LayoutTableColumn($result->opens));
                $row->addColumn(new LayoutTableColumn($result->clicks));
                $table->addRow($row);
            }

            $widget->addTable($table);
        }

        $page->addWidget($widget);

        $response->addUIElement($page);

        return $response->build();
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
