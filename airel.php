<?php

namespace Photon\Generator;

use function Photon\Generator\Cache\{hasCachedAirEl, getCachedAirElPath};
use function Photon\Generator\Path\pathJoin;

require __DIR__ . '/vendor/autoload.php';

$id = preg_replace('/\.\.|\\\\|\//', '', $_GET['id'] ?? die());
$unitNo = preg_replace('/\.\.|\\\\|\//', '', $_GET['num'] ?? '000');
$noCache = ($_GET['clear'] ?? 'no') == 'sld';

if (!$noCache && hasCachedAirEl($id, $unitNo)){
	header("Content-Type: image/png");
	$image = imagecreatefrompng(getCachedAirElPath($id, $unitNo));
	imagepng($image);
	die();
}

define('PHOTON_GENERATOR', pathJoin(__DIR__, 'airel.json'));
if (!file_exists(PHOTON_GENERATOR)){die('1');}
$generator = json_decode(file_get_contents(PHOTON_GENERATOR), true);
if (!$generator){die('2');}
if (!isset($generator[$id])){die('3');}
$data = $generator[$id];

define('PHOTON_FONT_DIR', pathJoin(__DIR__, 'fonts'));
$font = pathJoin(PHOTON_FONT_DIR, $data['font']);

$image = imagecreate(512, 512);
$bg = $data['colors'][0];
$background = imagecolorallocate($image, $bg['r'], $bg['g'], $bg['b']);
$fg = $data['colors'][1];
$foreground = imagecolorallocate($image, $fg['r'], $fg['g'], $fg['b']);
imagefill($image, 0, 0, $background);

[$x, $y] = $data['pos'];
$size = $data['size'];
$points = imagettfbbox($size, 0, $font, $unitNo);
$w = $points[4] - $points[0];
$h = $points[5] - $points[1];
$x -= ($w / 2);
imagettftext($image, $size, 0, $x, $y, $foreground, $font, $unitNo);

imagepng($image, getCachedAirElPath($id, $unitNo));

header("Content-Type: image/png");
echo imagepng($image);
