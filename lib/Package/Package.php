<?php namespace Package;

ini_set('display_errors', 1);



class Package
{

    protected static $message;

    protected $package;

    protected static $packages;

    protected static $config_file = '/../../package.php';
    

    public function __construct($start_dir)
    {

        $this->package['start_dir'] = $start_dir;
        $this->package['config_file'] = realpath($this->package['start_dir'] . self::$config_file);

        $this->boot();

    }



    protected function bindPaths($paths)
    {
        foreach ($paths as $key => $value) {

            self::arraySet($this->package, 'path.' . $key, $value);

        }
    }



    public function boot()
    {
        $this->bindPaths(require $this->package['start_dir'] . '/paths.php');
        
        $this->loadConfig();

        $this->loadClasses();
        $this->loadAliases();
        $this->loadProviders();
        $this->loadSubpages();
        
        $this->configurePackage();

        

        $this->registerExtensions();
    }
    


    public function run()
    {

        $this->registerPackage();

    }



    protected function loadConfig()
    {

        $this->package['config'] = require $this->package['config_file'];

        foreach ($this->package['config'] as $key => $value) {

            $this->package[$key] = $value;

        }
    }



    protected function loadClasses()
    {

        $classes = $this->package['classes'];

        if (count($classes) > 0) {

            $package_path = self::arrayGet($this->package, 'path.package');

            foreach ($classes as $class => $class_file) {

                if (! class_exists($class)) {
                    
                    require $package_path . '/' . $class_file;

                }
            }
        }
        
    }


    protected function loadAliases()
    {
        $aliases = $this->package['aliases'];

        if (count($aliases) > 0) {

            foreach ($aliases as $alias => $class) {

                class_alias($class, $alias);
            }

        }
    }


    protected function loadProviders()
    {
        $providers = $this->package['providers'];

        $loaded_providers = array();
        $i18n_providers   = array();

        if (count($providers) > 0) {

            foreach ($providers as $provider) {

                $instance = new $provider();

                if (is_dir($instance->i18n())) {

                    $i18n_providers[] = $instance->i18n();

                }

                $register = $instance->register();
                if (is_array($register)) {

                    $loaded_providers = array_merge($loaded_providers, $register);

                } else {

                    $loaded_providers[] = $register;

                }

                
            }

        }

        self::arraySet($this->package, 'package.providers', $loaded_providers);
        self::arraySet($this->package, 'package.providers.i18n', $i18n_providers);
    }



    protected function loadSubpages()
    {
        $subpages = $this->package['subpages'];

        $pages = array();

        if (count($subpages) > 0) {

            foreach ($subpages as $title => $data) {

                $page = new \rex_be_page(
                    self::getI18N($this->package['name'] . '_' . $title), 
                    array(
                        'page'      =>  $this->package['name'],
                        'subpage'   =>  $data,
                    )
                );


                $subpage = is_array($data) ? $data[0] : $data;
                $subpage = ($subpage != '') ? '&subpage=' . $subpage : '';

                $page->setHref('index.php?page=' . $this->package['name'] . $subpage);

                $pages[] = $page;
            }

        }

        self::arraySet($this->package, 'package.pages', $pages);
    }



    protected function configurePackage()
    {
        global $REX, $I18N;

        self::arraySet($this->package, 'rex.backend', $REX['REDAXO']);
        self::arraySet($this->package, 'rex.user', $REX['USER']);


        $REX['ADDON']['rxid']           [$this->package['name']] = '';
        $REX['ADDON']['name']           [$this->package['name']] = $this->package['title'];
        $REX['ADDON']['version']        [$this->package['name']] = $this->package['version'];
        $REX['ADDON']['author']         [$this->package['name']] = $this->package['author'];
        $REX['ADDON']['supportpage']    [$this->package['name']] = $this->package['supportpage'];
        $REX['ADDON']['perm']           [$this->package['name']] = $this->package['permission_startpage'];
        $REX['ADDON']['pages']          [$this->package['name']] = self::arrayGet($this->package, 'package.pages');
        
        if ($this->package['permission'] != '') {

            $REX['PERM'][] = $this->package['permission'];

        }

        if (count($this->package['permission_options']) > 0) {

            $REX['EXTPERM'][] = $this->package['permission_options'];

        }




        
        if (! is_object($I18N)) {

            $I18N = rex_create_lang($REX['LANG']);

        }

        $package_lang_dirs   = self::arrayGet($this->package, 'package.providers.i18n');
        $package_lang_dirs[] = self::arrayGet($this->package, 'path.package') . '/lang';

        if (count($package_lang_dirs)) {

            foreach ($package_lang_dirs as $package_lang_dir) {

                if (is_dir($package_lang_dir)) {

                    $I18N->appendFile($package_lang_dir);

                }
            }

        }

    }


    protected function registerExtensions()
    {

        if (self::arrayGet($this->package, 'rex.user')) {

            if (count($this->package['stylesheets']) > 0) {

                rex_register_extension('PAGE_HEADER', '\Package\Package::stylesheetExtension', $this->package);

            }
            
            if (count($this->package['javascripts']) > 0) {

                rex_register_extension('PAGE_HEADER', '\Package\Package::javascriptExtension', $this->package);

            }


            
            if (count($this->package['extensions']) > 0) {

                foreach ($this->package['extensions'] as $extension => $data) {

                    if (! is_array($data) || ! isset($data[0]) || $data[0] == '') {

                        /*
                         * Extension Point is missing
                        */
                        continue;

                    }

                    switch (count($data)) {

                        case 1:
                            rex_register_extension($data[0], $extension);
                            break;

                        case 2:
                            rex_register_extension($data[0], $extension, $data[1]);
                            break;

                        case 3:
                            rex_register_extension($data[0], $extension, $data[1], $data[2]);
                            break;

                    }

                }
            }

        }

/*
        if ($this->application_is_activated && $this->rex_user) {

            rex_register_extension('OUTPUT_FILTER'  , '\Watson\Feature\FeatureAgent::panel');
            rex_register_extension('OUTPUT_FILTER'  , '\Watson\Base\Extension::panel');
            
            rex_register_extension('ADDONS_INCLUDED', '\Watson\Feature\FeatureAgent::register', array(), REX_EXTENSION_LATE);
            rex_register_extension('ADDONS_INCLUDED', '\Watson\Feature\FeatureAgent::run', array(), REX_EXTENSION_LATE);
            
            //rex_register_extension('ADDONS_INCLUDED', 'watson_extensions::console', array(), REX_EXTENSION_LATE);
            //rex_register_extension('ADDONS_INCLUDED', 'watson_extensions::terminal', array(), REX_EXTENSION_LATE);
            
            rex_register_extension('ADDONS_INCLUDED', '\Watson\Base\Extension::callUserFunc', array(), REX_EXTENSION_LATE);


            $object = new \watson_core_articles();
            rex_register_extension('WATSON_FEATURE', array('\Watson\Feature\FeatureAgent', 'register'), array('feature' => $object));
        }
        */
    }
    

    protected function registerPackage()
    {

        self::$packages[$this->package['name']] = $this->package;

    }


    /**
     */
    public static function stylesheetExtension($params)
    {

        foreach ($params['stylesheets'] as $file) {

            $params['subject'] .= "\n" . '<link type="text/css" rel="stylesheet" media="screen" href="../' . self::arrayGet($params, 'path.package_assets') . '/' . $file . '" />';

        }

        return $params['subject'];

    }

    /**
     */
    public static function javascriptExtension($params)
    {

        foreach ($params['javascripts'] as $file) {

            $params['subject'] .= "\n" . '<script type="text/javascript" src="../' . self::arrayGet($params, 'path.package_assets') . '/' . $file . '"></script>';

        }
        
        return $params['subject'];
    }



    public static function getSubpages()
    {

        global $REX;

        return $REX['ADDON']['pages'][self::get('name')];

    }


    public static function getRequest($varname, $vartype = '', $default = '', $request = 'request')
    {

        $function = 'rex_' . $request;

        return $function($varname, $vartype, $default);

    }


    public static function getCurrentPage()
    {

        return self::getRequest('page');

    }


    public static function getI18N($key)
    {
        global $I18N;

        return $I18N->msg($key);
    }



    public static function getRegisteredProviders()
    {

        return self::arrayGet('package.providers');

    }



    public static function set($key, $value = null, $package = null)
    {
        if (is_null($package)) {
        
            $package = self::getRequest('page');
        
        }

        if ($package == '') {

            throw new \Exception('Package could not be determined.');
            
        }

        $key = self::extendsKeyForDotNotation($package, $key);

        self::arraySet(self::$packages, $key, $value);
    }



    public static function get($key, $default = null, $package = null)
    {

        if (is_null($package)) {
        
            $package = self::getRequest('page');
        
        }

        if ($package == '') {

            throw new \Exception('Package could not be determined.');
            
        }
        
        $key = self::extendsKeyForDotNotation($package, $key);

        return self::arrayGet(self::$packages, $key, $default);

    }



    /**
     * Get an item from an array using "dot" notation.
     *
     * @param  array   $array       The search array
     * @param  string  $key         The dot-notated key or array of keys
     * @param  mixed   $default     The default value
     * @return mixed
     */
    public static function arrayGet($array, $key, $default = null)
    {
        if (is_null($key)) {
        
            return $array;

        }

        if (is_array($key)) {

            $return = array();
            
            foreach ($key as $k) {

                $return[$k] = static::get($array, $k, $default);

            }

            return $return;

        }

        if (isset($array[$key])) {

            return $array[$key];

        }



        foreach (explode('.', $key) as $segment) {

            if ( ! is_array($array) || ! array_key_exists($segment, $array)) {

                return ($value instanceof \Closure) ? $value() : $value;

            }

            $array = $array[$segment];
        }

        return $array;
    }


    /**
     * Set an array item to a given value using "dot" notation.
     *
     * If no key is given to the method, the entire array will be replaced.
     *
     * @param   array   $array  The array to insert it into
     * @param   mixed   $key    The dot-notated key to set or array of keys
     * @param   mixed   $value  The value
     * @return  void
     */
    public static function arraySet(&$array, $key, $value = null)
    {
        if (is_null($key)) {

            return $array = $value;

        }

        if (is_array($key)) {

            foreach ($key as $k => $v) {

                static::set($array, $k, $v);

            }

        } else {

            $keys = explode('.', $key);

            while (count($keys) > 1) {
                $key = array_shift($keys);

                if ( ! isset($array[$key]) || ! is_array($array[$key])) {

                    $array[$key] = array();

                }

                $array =& $array[$key];
            }

            $array[array_shift($keys)] = $value;

            return $array;
        }
    }


    /**
     * Remove an array item from a given array using "dot" notation.
     *
     * @param  array   $array
     * @param  string  $key
     * @return void
     */
    public static function arrayDelete(&$array, $key)
    {
        $keys = explode('.', $key);

        while (count($keys) > 1)
        {
            $key = array_shift($keys);

            if ( ! isset($array[$key]) || ! is_array($array[$key]))
            {
                return;
            }

            $array =& $array[$key];
        }

        unset($array[array_shift($keys)]);
    }



    protected static function extendsKeyForDotNotation($package, $key)
    {
        if (array_key_exists($package, self::$packages)) {

            if (is_array($key)) {

                $key = array($package => array($key));

            } else {

                $key = $package . '.' . $key;

            }
        
        }

        return $key;
    }


    /**
     * Normalizes a string
     *
     * Makes the string lowercase, replaces umlauts by their ascii representation (ä -> ae etc.), and replaces all
     * other chars that do not match a-z, 0-9 or $allowedChars by $replaceChar.
     *
     * @param string $string       Input string
     * @param string $replaceChar  Character that is used to replace not allowed chars
     * @param string $allowedChars Character whitelist
     * @return string
     */
    public static function normalize($string, $replaceChar = '_', $allowedChars = '')
    {
        // replace UTF-8 NFD umlauts
        $string = preg_replace("/(?<=[aou])\xcc\x88/i", 'e', $string);

        $string = mb_strtolower($string);

        // replace UTF-8 NFC umlauts
        $string = str_replace(array('ä', 'ö', 'ü', 'ß'), array('ae', 'oe', 'ue', 'ss'), $string);

        $string = preg_replace('/[^a-z\d' . preg_quote($allowedChars, '/') . ']+/ui', $replaceChar, $string);
        return trim($string, $replaceChar);
    }



    public static function requirePage($layout = true, $fallback = '')
    {
        global $REX, $I18N;

        $fallback = $fallback ?: self::get('name');

        $subpage = self::normalize(self::getRequest('subpage', 'string', $fallback));


        if ($layout) {

            require $REX['INCLUDE_PATH'] . '/layout/top.php';

            rex_title(self::get('title'), self::getSubPages());

            require self::get('path.package') . '/pages/' . $subpage . '.php';

            require $REX['INCLUDE_PATH'] . '/layout/bottom.php';

        } else {

            require self::get('path.package') . '/pages/' . $subpage . '.php';

        }
    }


    /**
     * Install the application.
     *
     */
    public static function install()
    {
        global $REX;

        $config = require realpath(__DIR__ . self::$config_file);

        if (self::checkRequirements($config)) {

            $REX['ADDON']['install'][ $config['name'] ] = 1;

        } else {

            $REX['ADDON']['installmsg'][ $config['name'] ] = self::$message;

        }

    }


    /**
     * Uninstall the application.
     *
     */
    public static function uninstall()
    {
        global $REX;

        $config = require realpath(__DIR__ . self::$config_file);

        $REX['ADDON']['install'][ $config['name'] ] = 0;
    }


    /**
     * Checks whether the requirements are met.
     *
     * @return boolean
     */
    protected static function checkRequirements($config)
    {
        global $REX;

        $errors = array();

        if ($config['rex_version'] != '') {

            $rex_version = $REX['VERSION'] . '.' . $REX['SUBVERSION'] . '.' . $REX['MINORVERSION'];

            if (version_compare($rex_version, $config['rex_version'], '<')) {

                $errors['de_de'][] = 'Die REDAXO Version reicht nicht aus. Es wird mindestens Version ' . $config['rex_version']. ' benötigt. Sie nutzen aktuell die Version ' . $rex_version;
                $errors['en_en'][] = 'The REDAXO version is not sufficient. At least version ' . $config['rex_version'] . ' is needed. Currently version ' . $rex_version . ' is installed.';
            
            }

        }


        if ($config['php_version'] != '') {

            if (version_compare(PHP_VERSION, $config['php_version']) < 0) {

                $errors['de_de'][] = 'PHP version >=' . $config['php_version'] . ' wird gebraucht!';
                $errors['en_en'][] = 'PHP version >=' . $config['php_version'] . ' needed!';

            }

        }


        if (count($config['required_addons']) >= 1) {

            foreach ($config['required_addons'] as $addon_name => $addon_version) {

                if (is_numeric($addon_name)) {

                    $addon_name = $addon_version;
                    $addon_version = '';

                }

                if (! \OOAddon::isAvailable($addon_name)) {

                    $de = ($addon_version != '') ? ' in der Version "' . $addon_version . '"' : '';
                    $en = ($addon_version != '') ? ' in version "' . $addon_version . '"' : '';

                    $errors['de_de'][] = 'Installiere und aktiviere das AddOn "' . $addon_name . '"' . $de . '.';
                    $errors['en_en'][] = 'Install and activate the addon "' . $addon_name . '"' . $en . '.';

                }

                if (\OOAddon::isAvailable($addon_name) && $addon_version != '' && version_compare(\OOAddon::getVersion($addon_name), $addon_version, '<')) {

                    $errors['de_de'][] = 'Die Version des AddOns "' . $addon_name . '" reicht nicht aus. Es wird mindestens Version ' . $addon_version . ' benötigt. Sie nutzen aktuell die Version ' . \OOAddon::getVersion($addon_name);
                    $errors['en_en'][] = 'The version of the addon "' . $addon_name . '" is not sufficient. At least version ' . $addon_version . ' is needed. Currently version ' . \OOAddon::getVersion($addon_name) . ' is installed.';

                }

            }

        }



        if (count($errors) >= 1) {

            $lang = $REX['LOGIN']->getLanguage();
            $lang = $lang != 'de_de' ? 'en_en' : $lang;

            $style  = ' style="position: relative; display: block; padding-left: 10px; font-weight: 400;"';
            $bullet = '<i style="position: absolute; left: 0;">&bullet;</i>';
            $warning = '<b' . $style. '>' . $bullet . implode('</b><b' . $style. '>' . $bullet, $errors[$lang]) . '</b>';

            self::$message = $warning;

            return false;

        }

        return true;
    }





    /**
     * Dump the passed variables and end the script.
     *
     * @param  dynamic  mixed
     * @return void
     */
    public static function debug()
    {

        array_map(function ($x) {

            $style = 'style="
                width: 500px;
                margin-left: auto;
                padding: 90px;
                background-color: rgb(237, 239, 238);
                font-family: Menlo;
                font-size: 12px;
                line-height: 16px;
                text-align: left;
                cursor: default;"';

            echo '<pre ' . str_replace("\n", '', $style) . '>'; print_r($x); echo '</pre>';

        }, func_get_args());
    }


    /**
     * Handle dynamic static method calls into the method.
     *
     * @param  string  $method
     * @param  array   $parameters
     * @return mixed
     */
    public static function __callStatic($method, $parameters)
    {
        echo $method;
        //\Iffi\Support\Debug::print_r($parameters);
        
        //$instance = new static;

        //return call_user_func_array(array($instance, $method), $parameters);
    }



    /**
     * Simple helper to debug to the console
     * 
     * @param  Array, String $data
     * @return String
     */
    public static function console($data)
    {
        if (! is_array($data)) {
            $data = array($data);
        }

        $output = "<script>console.log( 'Debug Objects: " . implode( ',', $data) . "' );</script>";
        
        echo $output;
    }
}
