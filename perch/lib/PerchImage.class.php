<?php

class PerchImage
{
    private $mode = false;
    
    // Compression quality for JPEGs
    private $jpeg_quality = 85;
    
    private $box_constrain = true;
    
    function __construct()
    {
        if (extension_loaded('gd')) {
            $this->mode = 'gd';
        }
        
        if (extension_loaded('imagick') && class_exists('Imagick')) {
            $this->mode = 'imagick';
        }
        
    }
    
    public function resize_image($image_path, $target_w=false, $target_h=false, $crop=false, $suffix=false)
    {
        if ($this->mode === false) return;
        
        if ($crop) {
            PerchUtil::debug('Resizing and cropping image... ('.$this->mode.')');
        }else{
            PerchUtil::debug('Resizing image... ('.$this->mode.')');
        }
        
        $save_as = $this->get_resized_filename($image_path, $target_w, $target_h, $suffix);
        
        $info = getimagesize($image_path);
        if (!is_array($info)) return false;
        
        
        $image_w = $info[0];
        $image_h = $info[1];
        
        $crop_x  = 0;
        $crop_y  = 0;
        $crop_w  = 0;
        $crop_h  = 0;
        
        $image_ratio = $image_w/$image_h;
        
        // Constrain by width
        if ($target_w && $image_w>$target_w) {
            $new_w = $target_w;
            $new_h = $target_w/$image_ratio;
        }
        
        // Constrain by height
        if ($target_h && $image_h>$target_h) {
            $new_h = $target_h;
            $new_w = $target_h*$image_ratio;
        }
        
        // Both specified, and crop set
        if ($target_w && $target_h && $crop) {

                $crop_w = $target_w;
                $crop_h = $target_h;
                
                $crop_ratio = $crop_w/$crop_h;
                            
                if ($image_ratio >= $crop_ratio) {
                    // Landscape or square crop 
                    $new_h = (int)$target_h;
                    $new_w = $target_h*$image_ratio;
                    $crop_y = 0;
                    $crop_x = ($new_w/2)-($target_w/2);
                }

                if ($crop_ratio > $image_ratio) {
                    // Portrait crop                   
                    $new_w = (int)$target_w;
                    $new_h = $target_w/$image_ratio;
                    $crop_x = 0;
                    $crop_y = ($new_h/2)-($target_h/2);

                }
                
            // Check we're not cropping upwardly
            if ($crop_w > $image_w || $crop_h > $image_h) {
                $crop_x  = 0;
                $crop_y  = 0;
                $crop_w  = 0;
                $crop_h  = 0;
                
                $crop    = false;
            }
        }
                
        if ($target_w && $target_h && !$crop) {

                // Normal resize
                if ($this->box_constrain) {
                    
                    if (($image_w / $target_w) > ($image_h / $target_h)) {
                        $new_w = $target_w;
                        $new_h = $target_w/$image_ratio;
                    } else {
                        $new_h = $target_h;
                        $new_w = $target_h*$image_ratio;
                    }
                }else{
                    if ($image_w > $image_h) {
                        $new_w = $target_w;
                        $new_h = $target_w/$image_ratio;
                    }

                    if ($image_h > $image_w) {
                        $new_h = $target_h;
                        $new_w = $target_h*$image_ratio;
                    }
                }
        
            
        }
        
        // Default
        if (!isset($new_w)) {
            $new_w = $image_w;
            $new_h = $image_h;
        }
        
        // Prepare returned array
        $out = array();
        $out['w'] = (int) $new_w;
        $out['h'] = (int) $new_h;
        $out['file_path'] = $save_as;
        $parts = explode('/', $save_as);
        $out['file_name'] = array_pop($parts);
        $out['web_path'] = str_replace(PERCH_RESFILEPATH.DIRECTORY_SEPARATOR, PERCH_RESPATH.'/', $save_as);
        
        // Check we're not upsizing
        if ($new_w > $image_w || $new_h > $image_h) {
            copy($image_path, $save_as);
            PerchUtil::set_file_permissions($save_as);
            return $out;
        }
        
        // Check we're not resizing to the same exact size, as this just kills quality
        if ($new_w == $image_w && $new_h == $image_h) {
            copy($image_path, $save_as);
            PerchUtil::set_file_permissions($save_as);
            return $out;
        }
        
        
        $r = false;
        
        if ($this->mode == 'gd') {
            $r = $this->resize_with_gd($image_path, $save_as, $new_w, $new_h, $crop_w, $crop_h, $crop_x, $crop_y); 
        }
        
        if ($this->mode == 'imagick') {
            $r =  $this->resize_with_imagick($image_path, $save_as, $new_w, $new_h, $crop_w, $crop_h, $crop_x, $crop_y);
        }
        
        PerchUtil::set_file_permissions($save_as);
        
        if ($r) return $out;
        
        return false;
    }
    
    
    public function get_resized_filename($image_path, $w=0, $h=0, $suffix=false)
    {
        if ($suffix) {
            $suffix = '-'.$suffix;
        }else{
            $suffix = '-';
            if ($w) $suffix .= 'w'.$w;
            if ($h) $suffix .= 'h'.$h;
        }
        
        return preg_replace('/(\.jpg|\.jpeg|\.gif|.png)\b/', $suffix.'$1', $image_path);
    }
    
    public function set_quality($quality)
    {
        $this->jpeg_quality = intval($quality);
    }
    
    private function resize_with_gd($image_path, $save_as, $new_w, $new_h, $crop_w, $crop_h, $crop_x, $crop_y)
    {
        $info = getimagesize($image_path);
        if (!is_array($info)) return false;
        
        $image_w = $info[0];
        $image_h = $info[1];
        $mime    = $info['mime'];
        
        $crop    = false;
        if ($crop_w != 0 && $crop_h != 0) $crop = true;
        
        if (function_exists('imagecreatetruecolor')) {
            $new_image = imagecreatetruecolor($new_w, $new_h);
            if ($crop) $crop_image = imagecreatetruecolor($crop_w, $crop_h);
        }else{
            $new_image = imagecreate($new_w, $new_h);
            if ($crop) $crop_image = imagecreate($crop_w, $crop_h);
        }
        
        
        switch ($mime) {
            case 'image/jpeg':
                $orig_image = imagecreatefromjpeg($image_path);
                
                if (function_exists('imagecopyresampled')) {
                    imagecopyresampled($new_image, $orig_image, 0, 0, 0, 0, $new_w, $new_h, $image_w, $image_h);
                }else{
                    imagecopyresized($new_image, $orig_image, 0, 0, 0, 0, $new_w, $new_h, $image_w, $image_h);
                }
                
                if ($crop) {
                    imagecopy($crop_image, $new_image, 0, 0, $crop_x, $crop_y, $new_w, $new_h);
                    imagejpeg($crop_image, $save_as, $this->jpeg_quality);
                }else{
                    imagejpeg($new_image, $save_as, $this->jpeg_quality);
                }
                
                
                break;
                
                
                
                
            case 'image/gif':
                $orig_image = imagecreatefromgif($image_path);
                $this->gd_set_transparency($new_image, $orig_image);
                imagetruecolortopalette($new_image, true, 256);
                
                if (function_exists('imagecopyresampled')) {
                    imagecopyresampled($new_image, $orig_image, 0, 0, 0, 0, $new_w, $new_h, $image_w, $image_h);
                }else{
                    imagecopyresized($new_image, $orig_image, 0, 0, 0, 0, $new_w, $new_h, $image_w, $image_h);
                }


                if ($crop) {
                    $this->gd_set_transparency($crop_image, $orig_image);
                    imagetruecolortopalette($crop_image, true, 256);
                    
                    
                    imagecopy($crop_image, $new_image, 0, 0, $crop_x, $crop_y, $new_w, $new_h);
                    imagegif($crop_image, $save_as);
                }else{
                    imagegif($new_image, $save_as);
                }

                
                
                break;
                
                
                
            case 'image/png':
                $orig_image = imagecreatefrompng($image_path);
                
                imagealphablending($new_image, false);
                imagesavealpha($new_image, true);
                                
                if (function_exists('imagecopyresampled')) {
                    imagecopyresampled($new_image, $orig_image, 0, 0, 0, 0, $new_w, $new_h, $image_w, $image_h);
                }else{
                    imagecopyresized($new_image, $orig_image, 0, 0, 0, 0, $new_w, $new_h, $image_w, $image_h);
                }


                if ($crop) {                    
                    imagealphablending($crop_image, false);
                    imagesavealpha($crop_image, true);
                    
                    imagecopy($crop_image, $new_image, 0, 0, $crop_x, $crop_y, $new_w, $new_h);
                    imagepng($crop_image, $save_as);
                }else{
                    imagepng($new_image, $save_as);
                }
                

                
                break;
            
            default: 
                $orig_image = imagecreatefromjpeg($image_path);
                break;
        }
        
        imagedestroy($orig_image);
        imagedestroy($new_image);    
        if ($crop) imagedestroy($crop_image);
        
        return $mime;
        
    }
    
    private function sharpen_with_gd($image)
    {
        $matrix = array(
            array(-1, -1, -1),
            array(-1, 16, -1),
            array(-1, -1, -1),
        );

        $divisor = array_sum(array_map('array_sum', $matrix));
        $offset = 0; 
        imageconvolution($image, $matrix, $divisor, $offset);

        return $image;
    }
    
    private function gd_set_transparency($new_image, $orig_image)
    {

        $transparencyIndex = imagecolortransparent($orig_image);
        $transparencyColor = array('red' => 255, 'green' => 255, 'blue' => 255);

        if ($transparencyIndex >= 0) {
            if ($transparencyIndex < imagecolorstotal($orig_image)) {
                $transparencyColor = imagecolorsforindex($orig_image, $transparencyIndex);
            }
        }

        $transparencyIndex = imagecolorallocate($new_image, $transparencyColor['red'], $transparencyColor['green'], $transparencyColor['blue']);
        imagefill($new_image, 0, 0, $transparencyIndex);
        imagecolortransparent($new_image, $transparencyIndex);        

    }
    
    
    private function resize_with_imagick($image_path, $save_as, $new_w=0, $new_h=0, $crop_w, $crop_h, $crop_x, $crop_y)
    {    
        $crop    = false;
        if ($crop_w != 0 && $crop_h != 0) $crop = true;
        
        $Image = new Imagick();
        $Image->readImage($image_path);
        $Image->thumbnailImage($new_w, $new_h);
        
        if ($crop) {
            $Image->cropImage($crop_w, $crop_h, $crop_x, $crop_y);
        }
        
        $Image->writeImage($save_as);
        $Image->destroy();        
        return true;    
    }
    
}

?>
