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
}
