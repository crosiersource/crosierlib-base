<?php

namespace CrosierSource\CrosierLibBaseBundle\Utils\FileUtils;

/**
 * Class FileUtils
 *
 * @package CrosierSource\CrosierLibBaseBundle\Utils\FileUtils
 * @author Carlos Eduardo Pauluk
 */
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

    public static function keepOnlyTheNewest($dir)
    {
        if (is_dir($dir)) {
            $dir = rtrim($dir, '/\\');
            $files = scandir($dir);
            $files = array_diff($files, array('.', '..'));
            if (count($files) > 0) {
                $latestFile = null;
                $latestTime = 0;

                foreach ($files as $file) {
                    $filePath = $dir . '/' . $file;
                    $fileTime = filemtime($filePath);

                    if ($fileTime > $latestTime) {
                        $latestFile = $filePath;
                        $latestTime = $fileTime;
                    }
                }
                foreach ($files as $file) {
                    $filePath = $dir . '/' . $file;
                    if ($filePath !== $latestFile) {
                        unlink($filePath);
                    }
                }
                return $latestFile;
            } else {
                return null;
            }
        } else {
            return null;
        }
    }

}