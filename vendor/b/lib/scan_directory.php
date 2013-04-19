<?php

class b_scan_directory
{
    /**
     * dirnames
     * @var array
     */
    private $directories;

    /**
     * extension
     * @var array
     */
    private $extensions;


    public function __construct($dir = '', $recursive = false)
    {
        if ($dir != '') {
            self::addDirectory($dir, $recursive);
        }
    }


    protected function getExtensions()
    {
        return $this->extensions;
    }


    public function setExtension($extension)
    {
        if (is_null($extension)) {
            throw new rex_exception(sprintf('Expecting $extension to be not null!'));
        }

        $this->extensions[] = $extension;
    }


    public function setExtensions(array $extensions)
    {
        if (is_array($extensions) && count($extensions) > 0) {
            foreach ($extensions as $extension) {
                self::setExtension($extension);
            }
        }
    }


    public function addDirectory($dir, $recursive = false)
    {
        if (is_null($dir)) {
            throw new rex_exception(sprintf('Expecting $dir to be not null!'));
        }

        $this->directories[$dir] = $recursive;
    }


    public function addDirectories(array $dirs)
    {
        if (is_array($dirs) && count($dirs) > 0) {
            $associative = self::isAssociative($dirs);
            foreach ($dirs as $key => $value) {
                if ($associative) {
                    self::addDirectory($key, $value);
                } else {
                    self::addDirectory($value);
                }
            }
        }
    }


    protected function getDirectories()
    {
        return $this->directories;
    }


    public function get()
    {
        $extensions = self::getExtensions();

        $check_extension = false;
        if (count($extensions) > 0) {
            $check_extension = true;
        }

        $files = array();
        foreach (self::getDirectories() as $dir => $recursive) {
            $iterator = rex_finder::factory($dir)->recursive($recursive)->filesOnly();

            foreach ($iterator as $object) {
                if (is_object($object)) {

                    if ($check_extension && in_array($object->getExtension(), $extensions)) {
                        $files[] = $object->getPathname();
                    }

                    if (!$check_extension) {
                        $files[] = $object->getPathname();
                    }
                }
            }
        }

        return $files;
    }


    protected function isAssociative($array)
    {
        return array_keys($array) !== range(0, count($array) - 1);
    }
}
