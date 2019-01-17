<?php

namespace CrosierSource\CrosierLibBaseBundle\Utils\FileUtils;

class FileUtils
{

    public static function getDirContents($dir, &$results = array())
    {
        $files = scandir($dir);

        foreach ($files as $key => $value) {
            $path = realpath($dir . DIRECTORY_SEPARATOR . $value);
            if (!is_dir($path)) {
                $results[] = $path;
            } else if ($value != "." && $value != "..") {
                FileUtils::getDirContents($path, $results);
                $results[] = $path;
            }
        }

        return $results;
    }

}