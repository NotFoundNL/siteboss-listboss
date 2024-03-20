<?php

namespace NotFound\ListBoss\Http\Controllers;

use NotFound\Framework\Http\Controllers\Controller;
use NotFound\Layout\Elements\LayoutText;
use NotFound\Layout\Elements\LayoutTitle;
use NotFound\Layout\Helpers\LayoutWidgetHelper;

class DocumentationController extends Controller
{
    public function index()
    {
        $widget = new LayoutWidgetHelper('Resultaten van verzending', 'Uitleg');
        $widget->addBreadcrumb('Verzendingen', '/app/listboss/');
        $widget->widget->addTitle(new LayoutTitle('E-mails', 'Uitleg'));
        $widget->widget->addText(new LayoutText($this->doc()));

        return $widget->response();
    }

    private function doc()
    {
        $doc = [
            'Deze weergave is voornamelijk bedoeld om problemen op te sporen bij de verzending. Ontbrekende gegevens geven geen uistluitsel over de status van de verzending. Slechts als de status "dropped" is is duidelijk dat een mail niet ontvangen is.',

            'De kolom <strong>Verzonden</strong> geeft aan of de e-mail is verzonden.',
            'De kolom <strong>Geopend</strong> geeft aan of de e-mail is geopend.',
            'De kolom <strong>Geklikt</strong> geeft aan of er op een link in de e-mail is geklikt. Dat kan zijn door een gebruiker, maar helaas worden soms ook "automatische" kliks meegeteld. In de totalen wordt daarom niet het totaal aantal kliks weergegeven, maar hoeveel ontvangers een klik hebben geregistreerd.',
        ];

        return '<p>'.implode('</p><p>', $doc).'</p>';
    }
}
