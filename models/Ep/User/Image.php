<?php
/**
 * Image.php
 * Image Functions*
 * @author Arun
 * @editor Arun
 * @version 1.0
 */
class Ep_User_Image {

   var $image;
   var $image_type;

   function load($filename) {

      $image_info = getimagesize($filename);
      $this->image_type = $image_info[2];

      if( $this->image_type == IMAGETYPE_JPEG ) {

            $this->image = imagecreatefromjpeg($filename);

      } elseif( $this->image_type == IMAGETYPE_GIF ) {

            $this->image = imagecreatefromgif($filename);

      } elseif( $this->image_type == IMAGETYPE_PNG ) {

            $this->image = imagecreatefrompng($filename);

      }

   }

   function save($filename, $image_type=IMAGETYPE_JPEG, $compression=90, $permissions=null) {

      if( $image_type == IMAGETYPE_JPEG ) {
		
		imagejpeg($this->image,$filename,$compression);
      } elseif( $image_type == IMAGETYPE_GIF ) {

         imagegif($this->image,$filename);

      } elseif( $image_type == IMAGETYPE_PNG ) {

        imagealphablending($this->image, false);
		imagesavealpha($this->image,true);
		imagepng($this->image,$filename);
      }

      if( $permissions != null) {

         chmod($filename,$permissions);

      }

   }

   function output($image_type=IMAGETYPE_JPEG) {

      if( $image_type == IMAGETYPE_JPEG ) {

         imagejpeg($this->image);

      } elseif( $image_type == IMAGETYPE_GIF ) {

         imagegif($this->image);

      } elseif( $image_type == IMAGETYPE_PNG ) {

        // imagealphablending($this->image, false);
		 //imagesavealpha($this->image,true);
		 imagepng($this->image);

      }

   }

   function getWidth() {

      return imagesx($this->image);

   }

   function getHeight() {

      return imagesy($this->image);

   }

   function resizeToHeight($height) {

      $ratio = $height / $this->getHeight();

      $width = $this->getWidth() * $ratio;

      $this->resize($width,$height);

   }

   function resizeToWidth($width) {

      $ratio = $width / $this->getWidth();

      $height = $this->getheight() * $ratio;

      $this->resize($width,$height);

   }

   function scale($scale) {

      $width = $this->getWidth() * $scale/100;

      $height = $this->getheight() * $scale/100;

      $this->resize($width,$height);

   }

   function resize($width,$height) {

      $new_image = imagecreatetruecolor($width, $height);

      imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());

      $this->image = $new_image;

   }
    
	function cropImage($x,$y,$twidth,$theight,$pwidth,$pheight){

      $new_image = imagecreatetruecolor($twidth, $theight);
      imagecopyresampled($new_image, $this->image, 0, 0, $x, $y, $twidth, $theight, $pwidth,$pheight);
      $this->image = $new_image;

    }
	
	function imageskewantialiased($img,$skew_val,$new_x,$new_y,$post_width,$post_height)
	{
		//$width = imagesx($img);
		//$height = imagesy($img);
		$width=$post_width;
		$height=$post_height;
		// See below for definition of imagecreatealpha
		$imgdest = $this->imagecreatealpha($width, $height);
    
		// Process the image
		for($x = 0, $level = 0; $x < $width - 1; $x++)
		{
			$floor = floor($level);
			
			// To go faster, some lines are being copied at once
			if ($level == $floor)
				imagecopy($imgdest, $img, $x, $level, $x, 0, 1, $height - 1);
			else
			{
				$temp = $level - $floor;
				
				// The first pixel of the line
				// We get the color then apply a fade on it depending on the level
				$color1 = imagecolorsforindex($img, imagecolorat($img, $x, 0));
				$alpha = $color1['alpha'] + ($temp * 127);
				if ($alpha < 127)
				{
					$color = imagecolorallocatealpha($imgdest, $color1['red'], $color1['green'], $color1['blue'], $alpha);
					imagesetpixel($imgdest, $x, $floor, $color);
				}
				
				// The rest of the line
				for($y = 0; $y < $height - 1; $y++)
				{
					// Merge this pixel and the upper one
					$color2 = imagecolorsforindex($img, imagecolorat($img, $x, $y));
					$alpha = ($color1['alpha'] * $temp) + ($color2['alpha'] * (1 - $temp));
					if ($alpha < 127)
					{
						$red   = ($color1['red']   * $temp) + ($color2['red']   * (1 - $temp));
						$green = ($color1['green'] * $temp) + ($color2['green'] * (1 - $temp));
						$blue  = ($color1['blue']  * $temp) + ($color2['blue']  * (1 - $temp));
						$color = imagecolorallocatealpha($imgdest, $red, $green, $blue, $alpha);
						imagesetpixel($imgdest, $x, $floor + $y, $color);
					}
					
					$color1 = $color2;
				}
				
				// The last pixel of the line
				$color1 = imagecolorsforindex($img, imagecolorat($img, $x, $height - 1));
				$alpha = $color1['alpha'] + ((1 - $temp) * 127);
				if ($alpha < 127)
				{
					$color = imagecolorallocatealpha($imgdest, $color1['red'], $color1['green'], $color1['blue'], $alpha);
					imagesetpixel($imgdest, $x, $floor + $height - 1, $color);
				}
			}
			
			// The line is finished, the next line will be lower
			$level += $skew_val;
		}
		
		// Finished processing, return the skewed image
		return $imgdest;
	}

	// Creates a new image of the size specified with a blank background (transparent)
	function imagecreatealpha($width, $height)
	{
		// Create a normal image and apply required settings
		$img = imagecreatetruecolor($width, $height);
		imagealphablending($img, false);
		imagesavealpha($img, true);
		
		// Apply the transparent background
		$trans = imagecolorallocatealpha($img, 0, 0, 0, 127);
		for ($x = 180; $x < $width; $x++)
		{
			for ($y = 229; $y < $height; $y++)
			{
				imagesetpixel($img, $x, $y, $trans);
			}
		}
		
		return $img;
	}
}