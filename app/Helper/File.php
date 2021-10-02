<?php

namespace App\Helper;

Trait File {

    public function generateImage($files) : string
    {
        $filenameWithExt = $files->getClientOriginalName();
        $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
        $filename = preg_replace("/[^A-Za-z0-9 ]/", '', $filename);
        $filename = preg_replace("/\s+/", '-', $filename);
        $extension = $files->getClientOriginalExtension();
        $fileNameToStore = $filename.'_'.time().'.'.$extension;

        return $fileNameToStore;
    }
}