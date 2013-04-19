<?php

if (!class_exists('b_check')) {

    class b_check
    {

        static function install($min_redaxo_version = '', $min_php_version = '', array $addons = array())
        {
            global $REX;

            $errors = array();

            if (!is_null($min_redaxo_version)) {
                $sys_redaxo_version = $REX['VERSION'] . '.' . $REX['SUBVERSION'] . '.' . $REX['MINORVERSION'];

                if (version_compare($sys_redaxo_version, $min_redaxo_version, '<')) {
                    $errors['de_de'][] = 'Die REDAXO Version reicht nicht aus. Es wird mindestens Version ' . $min_redaxo_version . ' gebraucht. Sie nutzen aktuell die Version ' . $sys_redaxo_version;
                    $errors['en_en'][] = 'The REDAXO version is not sufficient. At least version ' . $min_redaxo_version . ' is needed. Currently version ' . $sys_redaxo_version . ' is installed.';
                }
            }

            if (!is_null($min_php_version)) {

                if (version_compare(PHP_VERSION, $min_php_version) < 0) {
                    $errors['de_de'][] = 'PHP version >=' . $min_php_version . ' wird gebraucht!';
                    $errors['en_en'][] = 'PHP version >=' . $min_php_version . ' needed!';
                }
            }

            if (count($addons) >= 1) {
                foreach ($addons as $addon) {
                    if (!OOAddon::isAvailable($addon)) {
                        $errors['de_de'][] = 'Installiere und aktiviere das AddOn "' . $addon . '".';
                        $errors['en_en'][] = 'Install and activate the addon "' . $addon . '".';
                    }
                }
            }

            if (count($errors) >= 1) {
                $lang = $REX['LOGIN']->getLanguage();
                $lang = $lang != 'de_de' ? 'en_en' : $lang;
                $warning = '<ul><li>' . implode('</li><li>', $errors[$lang]) . '</li></ul>';

                echo rex_warning_block($warning);

                return false;
            }

            return true;
        }
    }
}
