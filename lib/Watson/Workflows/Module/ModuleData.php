<?php

/**
 * This file is part of the Watson package.
 *
 * @author (c) Thomas Blum <thomas@addoff.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Watson\Workflows\Module;

use Watson\Foundation\Watson;
use Watson\Parsers\OptionFieldsParser;

class ModuleData
{
    /**
     * The max number of fields in module table.
     *
     * @var array
     */
    private $maxFields = [
            'VALUE' => 20,
            'LINK' => 10,
            'LINKLIST' => 10,
            'MEDIA' => 10,
            'MEDIALIST' => 10,
        ];

    /**
     * Count the fields from user input.
     *
     * @var array
     */
    private $countFields = [
            'VALUE' => 0,
            'LINK' => 0,
            'LINKLIST' => 0,
            'MEDIA' => 0,
            'MEDIALIST' => 0,
        ];

    /**
     * Save the fields, which removed from user input.
     */
    private $removeFields = [];

    /**
     * Replace variables in templates.
     *
     * @var array
     */
    private $replaceVariables = [
        '$LABEL$' => '',
        '$VALUE$' => '',
        '$VARIABLE$' => '',
        '$ARGS_ATTRIBUTE_CLASS$' => '',
        '$ARGS_ATTRIBUTES_ARRAY$' => '',
        '$ARGS_ATTRIBUTES_STRING$' => '',
        '$ARGS_OPTIONS_ARRAY$' => '',
        '$ARGS_OPTIONS_QUERY$' => '',
    ];

    /**
     * Fields.
     *
     * @var array
     */
    private $fields = [];

    /**
     * Tabs.
     *
     * @var int
     */
    private $inputTabs = 0;

    /**
     * Module id.
     *
     * @var int
     */
    private $id = '';

    /**
     * Module name.
     *
     * @var string
     */
    private $name = '';

    /**
     * Module input.
     *
     * @var string
     */
    private $input = '';

    /**
     * Module output.
     *
     * @var string
     */
    private $output = '';

    /**
     * Template Path.
     *
     * @var string
     */
    private $templatePath = '';

    /**
     * Execute the command for the given Command.
     *
     * @param string $moduleName
     * @param array  $moduleFields
     * @param int    $moduleInputTabs
     */
    public function __construct($name, $fields)
    {
        $this->name = trim($name) == '' ? 'Watson loves you.' : $name;

        $fieldParser = new OptionFieldsParser();
        $this->fields = $fieldParser->parse($fields);
    }

    public function create()
    {
        $this->fields = $this->checkFieldsAndRemoveWhenTooMuch($this->fields);
        if ($this->inputTabs >= 1) {
            $this->buildTabModule();
        } else {
            $this->buildNormalModule();
        }

        $sql = \rex_sql::factory();
        $sql->setTable(Watson::getTable('module'));
        $sql->setValue('name', $this->name);
        $sql->setValue('input', $this->input);
        $sql->setValue('output', $this->output);
        $sql->addGlobalCreateFields();
        $sql->insert();
        $this->id = $sql->getLastId();
        echo json_encode(['url' => \rex_url::backendPage('modules/modules', ['function' => 'edit', 'module_id' => $this->id], false)]);
    }

    public function setInputTabs($tabs)
    {
        $tabs = (int) $tabs;
        if ($tabs >= 1 && $tabs <= max($this->maxFields)) {
            $this->inputTabs = $tabs;
            foreach ($this->maxFields as $fieldName => $maxAmount) {
                switch ($fieldName) {
                    case 'LINK':
                    case 'LINKLIST':
                    case 'MEDIA':
                    case 'MEDIALIST':
                        $this->maxFields[$fieldName] = floor($maxAmount / $this->inputTabs);
                        break;
                }
            }
        }
    }

    /**
     * Set the path to the module templates.
     */
    public function setTemplatePath($templatePath)
    {
        $this->templatePath = $templatePath;
    }

    private function checkFieldsAndRemoveWhenTooMuch($fields)
    {
        if (count($fields)) {
            foreach ($fields as $index => $field) {
                $lowerType = strtolower($field['type']);
                $fieldTemplateDir = $lowerType;

                // Check for SQL Statement and set another template_path
                if ($lowerType == 'select' && isset($field['args'][0]) && is_string($field['args'][0]) && \rex_sql::getQueryType(trim(trim($field['args'][0], "'"), '"'))) {
                    $fieldTemplateDir = 'select_sql';
                    $fields[$index]['internal']['template']['$ARGS_OPTIONS_QUERY$'] = $field['args'][0];
                }

                if ($this->moduleFileExists($fieldTemplateDir)) {
                    $upperType = strtoupper($field['type']);
                    $upperType = isset($this->countFields[$upperType]) ? $upperType : 'VALUE';
                    ++$this->countFields[$upperType];
                    if ($this->countFields[$upperType] > $this->maxFields[$upperType]) {
                        $fields[$index]['internal']['message'] = 'max. Feldanzahl überschritten';
                        $this->removeFields[] = $field;
                        unset($fields[$index]);
                    } else {
                        $fields[$index]['internal']['field_id'] = $this->countFields[$upperType];
                        $fields[$index]['internal']['field_type'] = $upperType;
                        $fields[$index]['internal']['field_template_dir'] = $fieldTemplateDir;
                        $fields[$index]['internal']['template']['$LABEL$'] = $field['label'];
                        $fields[$index]['internal']['template']['$VALUE$'] = $this->countFields[$upperType];
                        $fields[$index]['internal']['template']['$VARIABLE$'] = strtolower(\rex_string::normalize($field['label']));

                        switch ($lowerType) {
                            case 'select':
                                if (isset($field['args'][0]) && is_array($field['args'][0])) {
                                    // args[0] = Options > must be an array. If this is a string, then it is a SQL statement.
                                    $fields[$index]['internal']['template']['$ARGS_OPTIONS_ARRAY$'] = $field['args_input'][0];
                                }

                                if (isset($field['args'][1]) && is_array($field['args'][1])) {
                                    // args[1] = Attributes > must be an array.
                                    if (isset($field['args'][1]['class'])) {
                                        $fields[$index]['internal']['template']['$ARGS_ATTRIBUTE_CLASS$'] = ' '.$field['args'][1]['class'];
                                        unset($field['args'][1]['class']);
                                    }
                                    $fields[$index]['internal']['template']['$ARGS_ATTRIBUTES_ARRAY$'] = $field['args_input'][1];
                                    $fields[$index]['internal']['template']['$ARGS_ATTRIBUTES_STRING$'] = \rex_string::buildAttributes($field['args'][1]);
                                } else {
                                    // set an empty Array
                                    $fields[$index]['internal']['template']['$ARGS_ATTRIBUTES_ARRAY$'] = '[]';
                                }
                                break;

                            default:
                                if (isset($field['args'][0]) && is_array($field['args'][0])) {
                                    if (isset($field['args'][0]['class'])) {
                                        $fields[$index]['internal']['template']['$ARGS_ATTRIBUTE_CLASS$'] = ' '.$field['args'][0]['class'];
                                        unset($field['args'][0]['class']);
                                    }
                                    $fields[$index]['internal']['template']['$ARGS_ATTRIBUTES_ARRAY$'] = $field['args_input'][0];
                                    $fields[$index]['internal']['template']['$ARGS_ATTRIBUTES_STRING$'] = \rex_string::buildAttributes($field['args'][0]);
                                }
                                break;
                        }
                    }
                } else {
                    $fields[$index]['internal']['message'] = 'Template existiert nicht';
                    $this->removeFields[] = $field;
                    unset($fields[$index]);
                }
            }
        }
        return $fields;
    }

    private function moduleFileExists($fieldTemplateDir)
    {
        return file_exists($this->getInputTemplate($fieldTemplateDir)) && file_exists($this->getOutputTemplate($fieldTemplateDir));
    }

    private function getInputTemplate($fieldTemplateDir)
    {
        return $this->getTemplatePath().$fieldTemplateDir.'/input.txt';
    }

    private function getOutputTemplate($fieldTemplateDir)
    {
        return $this->getTemplatePath().$fieldTemplateDir.'/output.txt';
    }

    private function buildNormalModule()
    {
        $search = array_keys($this->replaceVariables);
        $this->input .= '<div class="form-horizontal">'."\n";
        $this->output .= '<?php'."\n";
        foreach ($this->fields as $field) {
            $templateParams = $field['internal']['template'];
            $templateDir = $field['internal']['field_template_dir'];
            $templateInput = $this->getTemplateData($this->getInputTemplate($templateDir));
            $templateOutput = $this->getTemplateData($this->getOutputTemplate($templateDir));
            $replaceVariables = array_merge($this->replaceVariables, $templateParams);
            $replace = array_values($replaceVariables);
            $this->input .= str_replace($search, $replace, $templateInput);
            $this->output .= str_replace($search, $replace, $templateOutput);
        }
        $this->input .= '</div>';
        $this->output .= '?>';
    }

    private function buildTabModule()
    {
        echo '<pre>';
        echo print_r($this->fields).'<br />';
        echo '</pre>';
    }

    /**
     * Fetch the template data.
     *
     * @return array
     */
    private function getTemplateData($file)
    {
        return \rex_file::get($file);
    }

    /**
     * Return the path to the module template.
     *
     * @return mixed
     */
    private function getTemplatePath()
    {
        return $this->templatePath;
    }
}
