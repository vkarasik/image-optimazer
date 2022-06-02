<?php

function optimize_img()
{
    $src_dir = './images';
    $dst_dir = './images_opt';
    $dirs = array_slice(scandir($src_dir), 2);
    
    foreach ($dirs as $dir) {
        mkdir("$dst_dir/$dir");
        $files = array_slice(scandir("$src_dir/$dir"), 2);
        $files = array_filter($files, function ($f) {
            return preg_match('/.+\.(jpg|png|jpeg|webp)$/i', $f);
        });
        
        foreach ($files as $file) {
            resize_img($file, $dir, $src_dir, $dst_dir);
        }
    }
}


function resize_img($file, $dir, $src_dir, $dst_dir)
{

    $file_path = "$src_dir/$dir/$file";

    $file_info = getimagesize($file_path);
    $file_name = preg_replace('/(.+)\.\w+$/', '${1}', $file);
    $src_w  = $file_info[0];
    $src_h = $file_info[1];
    $ext = image_type_to_extension($file_info[2], false);

    if ($ext == 'png') {
        $src_image = imagecreatefrompng($file_path);
    } elseif ($ext == 'jpeg') {
        $src_image = imagecreatefromjpeg($file_path);
    } elseif ($ext == 'webp') {
        $src_image = imagecreatefromwebp($file_path);
    } else {
        return false;
    }

    $dst_w = 800;
    $dst_h = 550;
    $padding = 10;
    $quality = 75;
    $dst_image = imagecreatetruecolor($dst_w, $dst_h);
    $background = imagecolorallocate($dst_image, 255, 255, 255);
    imagefill($dst_image, 0, 0, $background);


    if ($src_h <= $dst_h && $src_w <= $dst_w) {
        // Высота и ширина области на холсте согласно пропорциям исходного изображения
        $dst_tmp_h = ($dst_h / $src_h) * $src_h;
        $dst_tmp_w = ($dst_h / $src_h) * $src_w;
    }
	
    elseif ($src_h > $dst_h && $src_w > $dst_w) {
        $dst_tmp_h = $src_h / ($src_h / $dst_h) - $padding;
        $dst_tmp_w = $src_w / ($src_h / $dst_h) - $padding;

        if ($dst_tmp_w > $dst_w) {
            $scale_factor = $dst_tmp_w / $dst_w;
            $dst_tmp_w = $dst_tmp_w / $scale_factor;
            $dst_tmp_h = $dst_tmp_h / $scale_factor;
        }
    }
	
    elseif ($src_h > $dst_h && $src_w <= $dst_w) {
        $dst_tmp_h = $src_h / ($src_h / $dst_h) - $padding;
        $dst_tmp_w = $src_w / ($src_h / $dst_h) - $padding;
    }

    $dst_x = ($dst_w - $dst_tmp_w) / 2;
    $dst_y = ($dst_h - $dst_tmp_h) / 2;

    imagecopyresampled(
        $dst_image,
        $src_image,
        $dst_x,
        $dst_y,
        0,
        0,
        $dst_tmp_w,
        $dst_tmp_h,
        $src_w,
        $src_h
    );

    imagejpeg($dst_image, "./$dst_dir/$dir/$file_name.jpg", $quality);


    imagedestroy($dst_image);
    imagedestroy($src_image);
	
	echo "./$dst_dir/$dir/$file_name.jpg\n";
}

optimize_img();
