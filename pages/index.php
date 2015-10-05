<?php

/**
 *
 * @author Thomas Blum
 * @author thomas.blum[at]redaxo[dot]de
 *
 */

$subpage = rex_be_controller::getCurrentPagePart(2);

echo rex_view::title(rex_i18n::msg('watson_title'));

include rex_be_controller::getCurrentPageObject()->getSubPath();
