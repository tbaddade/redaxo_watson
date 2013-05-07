<?php


class watson_calculator extends watson_searcher
{

    public function keywords()
    {
        return array('=');
    }

    public function search(watson_search_term $watson_search_term)
    {
        global $REX, $I18N;

        $watson_search_result = new watson_search_result();

        if ($watson_search_term->getTerms()) {
            $terms = $watson_search_term->getTermsAsString();

            $constants = array(
                'Pi'    => 3.141592653589793,       // Pi
                'G'     => 6.67384 * pow(10, -11)   // Gravitationskonstante
            );

            $terms = str_replace(array_keys($constants), str_replace(',', '.', array_values($constants)), $terms);

            $calc = new SimpleCalc();

            // Prozentrechnung
            // Eingabe "19% von 238" -> 38
            $pairs = explode('% ' . $I18N->msg('b_percent_of') . ' ', $terms);
            if (count($pairs) == 2) {
                $a = $pairs[0] . '%';
                $b = $calc->calculate($pairs[1]);
                $terms = $b . ' - ( ' . $b . ' / (1 + ' . $a . '))';
            }

            $terms = str_replace('%', '/100', $terms);

            // Ergebnis anzeigen, wenn keine Wörter vorhanden sind
            // Eingabe "name" -> 0
            if (!preg_match('@[a-zA-DF-Z]+@', $terms)) {
                $result = $calc->calculate($terms);
                $result = str_replace('.', ',', $result);

                $entry = new watson_search_entry();
                $entry->setValue('= ' . $result);
                $entry->setIcon('../' . $REX['MEDIA_ADDON_DIR'] . '/watson/icon_hat.png');

                $watson_search_result->addEntry($entry);
            }

        }

        return $watson_search_result;
    }

}
