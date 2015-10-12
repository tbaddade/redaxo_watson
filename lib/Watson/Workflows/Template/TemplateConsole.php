<?php

/**
 * This file is part of the Watson package.
 *
 * @author (c) Thomas Blum <thomas@addoff.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Watson\Template;

use \Watson\Foundation\Console;
use \Watson\Foundation\ConsoleCommand;
use \Watson\Foundation\Documentation;
use \Watson\Foundation\Watson;

class TemplateConsole extends Console
{
    /**
     * Provide the commands of the search.
     *
     * @return array
     */
    public function commands()
    {
        return array('t:copy', 't:make');
    }



    /**
     *
     * @return Documentation
     */
    public function documentation()
    {
        $documentations = array();
        
        $documentation = new Documentation('t:make');
        $documentation->setDescription( Watson::translate('watson_documentation_template_console_make_description'));
        $documentation->setUsage(       Watson::translate('watson_documentation_template_console_make_usage'));
        $documentation->setExamples(array(
                                        Watson::translate('watson_documentation_template_console_make_example_1'), 
                                        Watson::translate('watson_documentation_template_console_make_example_2'), 
                                    ));

        $documentations[] = $documentation;


        $documentation = new Documentation('t:copy');
        $documentation->setDescription( Watson::translate('watson_documentation_template_console_copy_description'));
        $documentation->setUsage(       Watson::translate('watson_documentation_template_console_copy_usage'));
        $documentation->setExamples(array(
                                        Watson::translate('watson_documentation_template_console_copy_example_1'), 
                                        Watson::translate('watson_documentation_template_console_copy_example_2'), 
                                    ));
        
        $documentations[] = $documentation;

        return $documentations;
    }



    public function copy(ConsoleCommand $consoleCommand)
    {
        $id = Watson::arrayCastVar($consoleCommand->getArguments(), '1', 'int', 0);

        if ($id > 0) {

        //    $sql = \
        $params = array();
        $params['templatename'] = $consoleCommand->getArgument(2);   

        }


        return $this->setSuccess('Template kopiert');
    }



    public function make(ConsoleCommand $consoleCommand)
    {

        $params = array();
        $params['active']       = (! is_null($consoleCommand->getOption('active'))) ? 1 : 0;
        $params['templatename'] = $consoleCommand->getArgument(1);

        $return = $this->createTemplate($params);

        if ($return['success'] != '') {

            return $this->setSuccess($return['success']);

        } else {

            return $this->setError($return['success']);

        }

    }



    protected function createTemplate($params)
    {
        global $REX, $I18N;

        $function     = 'add';
        $active       = Watson::arrayCastVar($params, 'active'      , 'int'     , 0);
        $templatename = Watson::arrayCastVar($params, 'templatename', 'string'  , '');
        $content      = Watson::arrayCastVar($params, 'content'     , 'string'  , '');
        $ctypes       = Watson::arrayCastVar($params, 'ctype'       , 'array'   , array());
        $categories   = Watson::arrayCastVar($params, 'categories'  , 'array'   , array());
        $modules      = Watson::arrayCastVar($params, 'modules'     , 'array'   , array());


        /*
         * following Code is from REDAXO
         * include/pages/template.inc.php
         * 
         * change rex_sql > \rex_sql
         */

        $num_ctypes   = count($ctypes);

        if ($ctypes[$num_ctypes] == "") {
            unset ($ctypes[$num_ctypes]);
            if (isset ($ctypes[$num_ctypes -1]) && $ctypes[$num_ctypes -1] == '') {
                unset ($ctypes[$num_ctypes -1]);
            }
        }

        // Daten wieder in den Rohzustand versetzen, da für serialize()/unserialize()
        // keine Zeichen escaped werden dürfen
        for($i=1;$i<count($ctypes)+1;$i++) {
            $ctypes[$i] = stripslashes($ctypes[$i]);
        }

        // leerer eintrag = 0
        if(count($categories) == 0 || !isset($categories["all"]) || $categories["all"] != 1) {
            $categories["all"] = 0;
        }

        // leerer eintrag = 0
        if(count($modules) == 0) {
            $modules[1]["all"] = 0;
        }

        foreach($modules as $k => $module) {
            if(!isset($module["all"]) ||$module["all"] != 1) {
                $modules[$k]["all"] = 0;
            }
        }

        $TPL = \rex_sql::factory();
        $TPL->setTable($REX['TABLE_PREFIX'] . "template");
        $TPL->setValue("name", $templatename);
        $TPL->setValue("active", $active);
        $TPL->setValue("content", $content);
        $attributes = rex_setAttributes("ctype", $ctypes, "");
        $attributes = rex_setAttributes("modules", $modules, "");
        $attributes = rex_setAttributes("categories", $categories, "");
        $TPL->setValue("attributes", addslashes($attributes));
        $TPL->addGlobalCreateFields();

        if ($function == "add") {
            $attributes = rex_setAttributes("ctype", $ctypes, "");
            $attributes = rex_setAttributes("modules", $modules, $attributes);
            $attributes = rex_setAttributes("categories", $categories, $attributes);
            $TPL->setValue("attributes", addslashes($attributes));
            $TPL->addGlobalCreateFields();

            if($TPL->insert()) {
                $template_id = $TPL->getLastId();
                $info = $I18N->msg("template_added");
            } else {
                $warning = $TPL->getError();
            }
        } else {
            $attributes = rex_setAttributes("ctype", $ctypes, $attributes);
            $attributes = rex_setAttributes("modules", $modules, $attributes);
            $attributes = rex_setAttributes("categories", $categories, $attributes);

            $TPL->setWhere("id='$template_id'");
            $TPL->setValue("attributes", addslashes($attributes));
            $TPL->addGlobalUpdateFields();

            if($TPL->update())
                $info = $I18N->msg("template_updated");
            else
                $warning = $TPL->getError();
        }
        
        // werte werden direkt wieder ausgegeben
        $templatename = stripslashes($templatename);
        $content = stripslashes($content);

        rex_deleteDir($REX['GENERATED_PATH']."/templates", 0);

        if ($goon != "") {
            $function = "edit";
            $save = "nein";
        } else {
            $function = "";
        }



        return array('success' => $info, 'error' => $warning);

    }

}
