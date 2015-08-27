<?php

global $REX;

$directories = explode(DIRECTORY_SEPARATOR, __DIR__);
$package_dir = '';
$addon = array_search('addons', $directories);
if ($addon > 0) {
    if (isset($directories[ $addon + 1 ])) {
        
        $package_dir .= '/' . $directories[ $addon + 1 ];

    }
}
$plugin = array_search('plugins', $directories);
if ($plugin > 0) {
    if (isset($directories[ $plugin + 1 ])) {
        
        $package_dir .= '/plugins/' . $directories[ $plugin + 1 ];

    }
}


return array (

	/**
	 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     * Redaxo base path
	 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     */

    'base' => $REX['FRONTEND_PATH'],


	/**
	 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     * Redaxo path to the frontend
	 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     */

    'frontend' => $REX['FRONTEND_PATH'],


	/**
	 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     * Redaxo path to the backend
	 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     */

    'backend' => $REX['FRONTEND_PATH'] . '/redaxo',


	/**
	 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     * Redaxo path to the media dir
	 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     */

    'media' => $REX['MEDIA_DIR'],


	/**
	 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     * Redaxo path to the assets dir
	 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     */

    'assets' => $REX['MEDIA_DIR'],


    /**
     * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     * Redaxo path to the data dir
     * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     */

    'data' => $REX['INCLUDE_PATH'] . '/data',


    /**
     * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     * Redaxo path to the cache dir
     * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     */

    'cache' => $REX['GENERATED_PATH'],


    /**
     * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     * Redaxo path to the core dir
     * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     */

    'core' => $REX['INCLUDE_PATH'],


    /**
     * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     * Redaxo path to the package dir
     * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     */

    'package' => $REX['INCLUDE_PATH'] . '/addons' . $package_dir,


    /**
     * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     * Redaxo path to the package assets dir
     * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     */

    'package_assets' => $REX['MEDIA_DIR'] . '/addons' . $package_dir,


    /**
     * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     * Redaxo path to the package cache dir
     * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     */

    'package_cache' => $REX['GENERATED_PATH'] . '/addons' . $package_dir,


    /**
     * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     * Redaxo path to the package data dir
     * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     */

    'package_data' => $REX['INCLUDE_PATH'] . '/data/addons' . $package_dir,

);