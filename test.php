
<?php

//header("Content-type:image/png");
$image_file = "./img/96.jpg";

$image = new Imagick($image_file);
//$image->newPseudoImage(200, 200, "magick:rose");
$image->setImageFormat("png");

$image->roundCorners(100,51);
$image->writeImage('./img/bd_logo4.png');
die;
$corner_radius =  252; // The default corner radius is set to 20px

$topleft = true; // Top-left rounded corner is shown by default

$bottomleft = true; // Bottom-left rounded corner is shown by default

$bottomright = true; // Bottom-right rounded corner is shown by default

$topright = true; // Top-right rounded corner is shown by default

$imagetype=strtolower('jpg');

$backcolor= "red";

$endsize=$corner_radius;

$startsize=$endsize*3-1;

$arcsize=$startsize*2+1;

if (($imagetype=='jpeg') or ($imagetype=='jpg')) {

    $image = imagecreatefromjpeg($image_file);

} else {

    if (($imagetype=='GIF') or ($imagetype=='gif')) {

        $image = imagecreatefromgif($image_file);

    } else {

        $image = imagecreatefrompng($image_file);

    }

}

$size = getimagesize($image_file);

// Top-left corner

$background = imagecreatetruecolor($size[0],$size[1]);

imagecopymerge($background,$image,0,0,0,0,$size[0],$size[1],100);

$startx=$size[0]*2-1;

$starty=$size[1]*2-1;

$im_temp = imagecreatetruecolor($startx,$starty);

imagecopyresampled($im_temp, $background, 0, 0, 0, 0, $startx, $starty, $size[0], $size[1]);

$bg = imagecolorallocate($im_temp, 255,255,255  );

//$fg = imagecolorallocate($im_temp, hexdec(substr($forecolor,0,2)),hexdec(substr($forecolor,2,2)),hexdec(substr($forecolor,4,2)));

if ($topleft == true) {

    imagearc($im_temp, $startsize, $startsize, $arcsize, $arcsize, 180,270,$bg);

    imagefilltoborder($im_temp,0,0,$bg,$bg);
 //   imagecopymerge($im_temp,$im_temp,0,0,$starty,$starty,0,0,100);

}

// Bottom-left corner

if ($bottomleft == true) {

    imagearc($im_temp,$startsize,$starty-$startsize,$arcsize,$arcsize,90,180,$bg);

    imagefilltoborder($im_temp,0,$starty,$bg,$bg);

}

// Bottom-right corner

if ($bottomright == true) {

    imagearc($im_temp, $startx-$startsize, $starty-$startsize,$arcsize, $arcsize, 0,90,$bg);

    imagefilltoborder($im_temp,$startx,$starty,$bg,$bg);

}

// Top-right corner

if ($topright == true) {

    imagearc($im_temp, $startx-$startsize, $startsize,$arcsize, $arcsize, 270,360,$bg);

    imagefilltoborder($im_temp,$startx,0,$bg,$bg);

}

$newimage = imagecreatetruecolor($size[0],$size[1]);

imagecopyresampled($image,$im_temp,0,0,0,0,$size[0],$size[1],$startx,$starty);

// Output final image

header("Content-type: image/png");

//imagepng($image,'./img/test.png');
imagepng($image);

imagedestroy($image);

imagedestroy($background);

imagedestroy($im_temp);