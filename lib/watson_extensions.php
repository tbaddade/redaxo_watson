<?php

class watson_extensions
{
    public static function watson($params)
    {
        $panel = '
            <div id="watson">
                <form action="">
                    <fieldset>
                        <input class="typeahead" type="text" name="q" value="" />
                    </fieldset>
                </form>
            </div>';

        $params['subject'] = str_replace('</body>', $panel . '</body>', $params['subject']);
        return $params['subject'];
    }


    public static function page_header($params)
    {
        global $REX;
        $myaddon = 'watson';

        $css_files      = $params['css'];
        $js_files       = $params['js'];
        $js_properties  = json_encode(array('backend' => true, 'backendUrl' => parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)));

        $addon_assets = '../' . $REX['MEDIA_ADDON_DIR'] . '/' . $myaddon . '/';

        foreach ($css_files as $media => $files) {
            foreach ($files as $file) {
                $params['subject'] .= "\n" . '<link rel="stylesheet" type="text/css" media="' . $media . '" href="' . $addon_assets . $file . '" />';
            }
        }

        if ($js_properties) {
            $params['subject'] .= "\n" . '

                    <script type="text/javascript">
                        <!--
                        if (typeof(rex) == "undefined") {
                            var rex = ' . $js_properties . ';
                        }
                        //-->
                    </script>';
        }

        foreach ($js_files as $file) {
            $params['subject'] .= "\n" . '<script type="text/javascript" src="' . $addon_assets . $file . '"></script>';
        }

        return $params['subject'];
    }
}
