<?php

#Setup
$file_url = 'http://**************/price-ucen/get_photo.php';   # list of products from server
$src = '"Z:\\Общая\\1C_share\\discount';                        # shared folder where ERP stores image folders
$dest = '"C:\\Users\\v.karasik\\Desktop\\ImgMin\\img_source';   # local folder to copy folders
$bat_file_path = 'D:/800x550/ImgMin/scripts/copy_ucen.bat';     # path to store BAT script

# Download and open CSV file
copy($file_url, 'report.csv');
$csv_file = file('./report.csv');

$data = [];
foreach ($csv_file as $line) {
    $data[] = str_getcsv($line);
}

# Filter rows where ID only is filled: UT0712181015165344;http://... will not match
$data = array_filter($data, function ($item) {
    return preg_match('/^UT\d+$/', $item[0]);
});

# Create BAT script for copy from shared to local folder
$bat = '';
$command = 'xcopy /S /E /Y';

foreach ($data as $ucen_num) {
    $bat .= "$command $src\\$ucen_num[0]\" $dest\\$ucen_num[0]\\\"\n";
}

# Set BAT script encoding
$bat = iconv("UTF-8", "CP866", $bat);

$bat_file = fopen($bat_file_path, "w+");
fwrite($bat_file, $bat);
fclose($bat_file);

echo 'Folders to copy: ' . count($data);
