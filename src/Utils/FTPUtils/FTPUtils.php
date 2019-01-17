<?php

namespace CrosierSource\CrosierLibBaseBundle\Utils\FTPUtils;

class FTPUtils
{

    /**
     * Apaga todos os arquivos e subdiretórios de um diretório no ftp.
     *
     * @param $ftpConn
     * @param $dir
     */
    public function recursiveDeleteDirFTP($ftpConn, $dir)
    {
        // here we attempt to delete the file/directory
        if (!(@ftp_rmdir($ftpConn, $dir) || @ftp_delete($ftpConn, $dir))) {
            // if the attempt to delete fails, get the file listing
            $filelist = @ftp_nlist($ftpConn, $dir);
            if (!$filelist) return;
            // loop through the file list and recursively delete the FILE in the list
            foreach ($filelist as $ent) {
                if (pathinfo($ent)['basename'] == '.' or pathinfo($ent)['basename'] == '..') continue;
                $this->recursiveDeleteDirFTP($ftpConn, $ent);
            }

            // if the file list is empty, delete the DIRECTORY we passed
            $this->recursiveDeleteDirFTP($ftpConn, $dir);
        }
    }

}