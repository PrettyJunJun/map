<?php
/**
 * 计算两点地理坐标之间的距离
 * @param  Decimal $longitude1 起点经度
 * @param  Decimal $latitude1 起点纬度
 * @param  Decimal $longitude2 终点经度
 * @param  Decimal $latitude2 终点纬度
 * @param  Int $unit 单位 1:米 2:公里
 * @param  Int $decimal 精度 保留小数位数
 * @return Decimal
 */
function getDistance($longitude1, $latitude1, $longitude2, $latitude2, $unit = 2, $decimal = 2)
{
    //>>地球半径系数
    $EARTH_RADIUS = 6370.996;
    //>>圆周率
    $PI = 3.1415926;

    $radLat1 = $latitude1 * $PI / 180.0;
    $radLat2 = $latitude2 * $PI / 180.0;

    $radLng1 = $longitude1 * $PI / 180.0;
    $radLng2 = $longitude2 * $PI / 180.0;

    $a = $radLat1 - $radLat2;
    $b = $radLng1 - $radLng2;

    //>>asin()返回不同数值的反正弦，返回的结果是介于 -PI/2 与 PI/2 之间的弧度值
    //>>sqrt()返回一个数的平方根
    //>>pow()返回 x 的 y 次方
    //>>sin()返回一个数的正弦
    //>>cos()返回一个数的余弦
    $distance = 2 * asin(sqrt(pow(sin($a / 2), 2) + cos($radLat1) * cos($radLat2) * pow(sin($b / 2), 2)));
    $distance = $distance * $EARTH_RADIUS * 1000;

    if ($unit == 2) {
        $distance = $distance / 1000;
    }

    //>>把数值字段舍入为指定的小数位数
    return round($distance, $decimal);

}

// 起点坐标
$longitude1 = 38.6398043879;
$latitude1 = 96.7852569489;

// 终点坐标
$longitude2 = 30.5704069353;
$latitude2 = 103.9632595604;

$distance = getDistance($longitude1, $latitude1, $longitude2, $latitude2, 1);
echo $distance . '米';

//$distance = getDistance($longitude1, $latitude1, $longitude2, $latitude2, 2);
//echo $distance . 'km';

?>