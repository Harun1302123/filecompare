<?php

namespace App\Libraries;

use Intervention\Image\Facades\Image;

class ImageProcessing {

    public static function resizeBase64WH($base64wh,$newwidth=100,$newheight = 110){
        $position = strpos($base64wh,',');
        if(!($position===false)){
            $base64wh=substr($base64wh,$position+1);
        }
        return ImageProcessing::resizeBase64Image($base64wh,$newwidth,$newheight);
    }

    public static function resizeBase64Image($base64_img,$newwidth=100,$newheight = 110){
        $image = base64_decode($base64_img);
        return ImageProcessing::resizeImg($image,$newwidth,$newheight);
    }

    public static function resizeImg($source_image,$newwidth,$newheight){
        list($width, $height) = @getimagesizefromstring($source_image);
        $thumb = @imagecreatetruecolor($newwidth, $newheight);
        $source = @imagecreatefromstring($source_image);
        @imagecopyresized($thumb, $source , 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
        ob_start();
        imagejpeg($thumb);
        $contents =  ob_get_contents();
        ob_end_clean();
        return $contents;
    }

    /*     * ****************************End of Class***************************** */
}
