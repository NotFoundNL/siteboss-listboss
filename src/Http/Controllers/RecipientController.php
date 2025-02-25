<?php

namespace NotFound\ListBoss\Http\Controllers;

use NotFound\Framework\Http\Controllers\Controller;
use NotFound\Framework\Http\Requests\FormDataRequest;
use NotFound\Layout\Elements\Table\LayoutTable;
use NotFound\Layout\Elements\Table\LayoutTableColumn;
use NotFound\Layout\Elements\Table\LayoutTableHeader;
use NotFound\Layout\Elements\Table\LayoutTableRow;
use NotFound\Layout\Helpers\LayoutWidgetHelper;
use NotFound\ListBoss\Helpers\ListBoss;

class RecipientController extends Controller
{
    public function show(FormDataRequest $request, int $list, int $recipient)
    {
        if (! config('listboss.backend')) {
            abort(404);
        }

        $listBoss = new ListBoss();
        $recipient = $listBoss->recipient($list, $recipient);

        $table = new LayoutTable(sort: false, delete: false, create: false, edit: false);

        $table->addHeader(new LayoutTableHeader('Eigenschap', 'key'));
        $table->addHeader(new LayoutTableHeader('Waarde', 'value'));

        $table->addRow($this->addRow(1, ['E-mailadres', $recipient->email]));
        $table->addRow($this->addRow(2, ['Ontvangen', $recipient->delivered]));
        $table->addRow($this->addRow(3, ['Aantal keer geklikt', $recipient->clicks]));
        $table->addRow($this->addRow(4, ['Aantal keer geopend', $recipient->opens]));

        $widget = new LayoutWidgetHelper('E-mails', 'Status');
        $widget->addBreadcrumb('Verzendingen', '/app/listboss/');
        $widget->widget->addTable($table);

        $table = new LayoutTable(sort: false, delete: false, create: false, edit: false);

        $table->addHeader(new LayoutTableHeader('Status', 'status'));
        $table->addHeader(new LayoutTableHeader('Tijdstijd', 'timestamp'));
        $table->addHeader(new LayoutTableHeader('Details', 'payload'));

        $rowId = 1;
        foreach ($recipient->events as $event) {
            $row = new LayoutTableRow($rowId++);
            $row->addColumn(new LayoutTableColumn($event->type));
            $row->addColumn(new LayoutTableColumn(\Sb::formatDate($event->timestamp)));
            $row->addColumn(new LayoutTableColumn($event->payload ?? ''));
            $table->addRow($row);
        }

        $widget->widget->addTable($table);

        return $widget->response();
    }

    private function addRow($id, array $values): LayoutTableRow
    {
        $row = new LayoutTableRow($id);
        foreach ($values as $value) {
            $row->addColumn(new LayoutTableColumn($value));
        }

        return $row;
    }
}
