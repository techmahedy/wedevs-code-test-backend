<?php

namespace App\Helper;

use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;

Trait File {

    public $public_path = "/public/uploadedImages/";
    public $storage_path = "/storage/uploadedImages/";

    public function file($file,$path,$width,$height)
    {
       if ( $file ) {

           $extension       = $file->getClientOriginalExtension();
           $filenametostore = $path.'-'.Str::random(30).'.'.$extension;
           $url             = $file->storeAs($this->public_path,$filenametostore);
           $public_path     = public_path($this->storage_path.$filenametostore);
           $img             = Image::make($public_path)->resize($width, $height);
           
           return $img->save($public_path) ? $url : '';
       }
    }
}