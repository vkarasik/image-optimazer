:: Clear image folders (%CD% is current directory)
cd ..
set folder="%CD%\img_source\"
cd "%folder%" && rd /s /q "%folder%" 2>NUL

cd ..
set folder="%CD%\img_optimized\"
cd "%folder%" && rd /s /q "%folder%" 2>NUL

echo 'Folders are clean ...'

:: Call php script to create BAT file (%~dp0 is bat script directory)
cd ..
php.exe %~dp0\ucen_photo.php

:: Call BAT script for copy folders
cd scripts\
call copy_ucen.bat

:: Call php script for optimizing images
cd ..
php.exe %~dp0\resize.php

pause