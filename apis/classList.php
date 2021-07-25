<?php
require 'members.php';
//固定課表
define("classList", array(
    "mon-1"=>"英文",
    "mon-2"=>"生物",
    "tue-1"=>"地科",
    "tue-2"=>"國文",
    "wed-1"=>"英文",
    "wed-2"=>"化學",
    "thu-1"=>"國文",
    "thu-2"=>"物理",
    "fri-1"=>"數學",
    "sun-1"=>"英文"
));

//固定課表時間
define("classInTime", array(
    "mon-1"=>"0800",
    "mon-2"=>"1000",
    "tue-1"=>"0800",
    "tue-2"=>"1000",
    "wed-1"=>"0800",
    "wed-2"=>"1000",
    "thu-1"=>"0800",
    "thu-2"=>"1000",
    "fri-1"=>"0800"
));

define("classOutTime", array(
    "mon-1"=>"0945",
    "mon-2"=>"1150",
    "tue-1"=>"0950",
    "tue-2"=>"1150",
    "wed-1"=>"0945",
    "wed-2"=>"1150",
    "thu-1"=>"0950",
    "thu-2"=>"1150",
    "fri-1"=>"1150"
));

?>
