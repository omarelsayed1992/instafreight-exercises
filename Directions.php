<?php

// function decodeRoute(encoded) {
//     var len = encoded.length;
//     var index = 0;
//     var array = [];
//     var lat = 0;
//     var lng = 0;
//     var ele = 0;
//
//     while (index < len) {
//         var b;
//         var shift = 0;
//         var result = 0;
//         do {
//             b = encoded.charCodeAt(index++) - 63;
//             result |= (b & 0x1f) << shift;
//             shift += 5;
//         } while (b >= 0x20);
//         var deltaLat = ((result & 1) ? ~(result >> 1) : (result >> 1));
//         lat += deltaLat;
//
//         shift = 0;
//         result = 0;
//         do {
//             b = encoded.charCodeAt(index++) - 63;
//             result |= (b & 0x1f) << shift;
//             shift += 5;
//         } while (b >= 0x20);
//         var deltaLon = ((result & 1) ? ~(result >> 1) : (result >> 1));
//         lng += deltaLon;
//
//         array.push([lng * 1e-5, lat * 1e-5]);
//     }

//     return array;
// }
class Point {
  /** @var float */
  private $lat;

  /** @var float */
  private $lng;

  /**
   * @param float $lat
   * @param float $lng
   */
  public function __construct($lat, $lng) {
    $this->lat = $lat;
    $this->lng = $lng;
  }

  /**
   * @return string
   */
  public function __toString() {
    return '(' . $this->lat . ', ' . $this->lng . ')';
  }
}

class RouteDecoder {

  /**
   * @param string $str
   * @param integer $index
   * @return integer $ord
   */
  private function getCharCode($str, $index) {
    list(, $ord) = unpack('N', mb_convert_encoding($str[$index], 'UCS-4BE', 'UTF-8'));
    return $ord;
  }

  /**
   * @param string $encodedRoute
   * @param float $cord
   * @param integer $index
   * @param integer $b
   * @return float $cord
   *
   */
  private function getPointCord($encodedRoute, $cord, &$index, &$b) {
    $shift = 0;
    $result = 0;
    do {
        $b = $this->getCharCode($encodedRoute, $index++) - 63;
        $result |= ($b & 0x1f) << $shift;
        $shift += 5;
    } while ($b >= 0x20);

    $delta = (($result & 1) ? ~($result >> 1) : ($result >> 1));
    $cord += $delta;

    return $cord;
  }

  /**
   * @param string $encodedRoute
   * @return Point[]
   */
  public function decode($encodedRoute) {
    $len = strlen($encodedRoute);
    $index = $lat = $lng = $ele = 0;
    $points = [];

    while ($index < $len) {
      $lat = $this->getPointCord($encodedRoute, $lat, $index, $b);
      $lng = $this->getPointCord($encodedRoute, $lng, $index, $b);

      $points[] = new Point($lat * 1e-5, $lng * 1e-5);
    }

    return $points;
  }
}

$routeDecoder = new RouteDecoder();
$points = $routeDecoder->decode('mkk_Ieg_qAiPePsHd[}CzMq@`CaAfCwCvLyApG[xBKZyCpPaDjQ');

echo 'Route has ', sizeof($points), ' points', PHP_EOL;

foreach ($points as $point) {
  echo $point, PHP_EOL;
}
