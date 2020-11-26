<?php

namespace Photon\Generator;

use function Photon\Generator\Cache\{hasCachedLivery, getCachedLiveryPath};
use function Photon\Generator\Path\pathJoin;

require __DIR__ . '/vendor/autoload.php';

$car = preg_replace('/\.\.|\\\\|\//', '', $_GET['car'] ?? die());
$id = preg_replace('/\.\.|\\\\|\//', '', $_GET['id'] ?? die());
$unitNo = preg_replace('/\.\.|\\\\|\//', '', $_GET['num'] ?? '000');
$noCache = ($_GET['clear'] ?? 'no') == 'sld';

if (!$noCache && hasCachedLivery($car, $id, $unitNo)){
	header("Content-Type: image/png");
	$image = imagecreatefrompng(getCachedLiveryPath($car, $id, $unitNo));
	imagepng($image);
	die();
}

define('PHOTON_GENERATOR_DIR', pathJoin(__DIR__, 'generators'));
define('PHOTON_LIVERY_DIR', pathJoin(__DIR__, 'liveries'));
define('PHOTON_FONT_DIR', pathJoin(__DIR__, 'fonts'));

$generatorPath = pathJoin(PHOTON_GENERATOR_DIR, $car . '.json');
if (!file_exists($generatorPath)){die();}

$generator = json_decode(file_get_contents($generatorPath), true);
if (!$generator){die();}

if (!isset($generator['colors'][$id])){die();}
$colors = @$generator['colors'][$id];

$baseImgPath = pathJoin(PHOTON_LIVERY_DIR, "{$car}_{$id}.png");
if (!file_exists($baseImgPath)){die();}

$image = imagecreatefrompng($baseImgPath);

/* Roof */
if (isset($generator['roof'])){
	$font = pathJoin(PHOTON_FONT_DIR, $generator['roof']['font']);
	[$size, $x, $y, $ang] = $generator['roof']['pos'];
	$color = $colors[0];

	$points = imagettfbbox($size, $ang, $font, $unitNo);
	$w = $points[4] - $points[0];
	$h = $points[5] - $points[1];
	$x -= ($w / 2);
	$y -= ($h / 2);
	imagettftext($image, $size, $ang, $x, $y, hexdec($color), $font, $unitNo);
}

/* Sides */
if (isset($generator['side'])){
	$font = pathJoin(PHOTON_FONT_DIR, $generator['side']['font']);
	$size = $generator['side']['size'];
	$color = $colors[1];

	[$x, $y, $ang] = $generator['side']['left'];
	imagettftext($image, $size, $ang, $x, $y, hexdec($color), $font, $unitNo);

	[$x, $y, $ang] = $generator['side']['right'];
	imagettftext($image, $size, $ang, $x, $y, hexdec($color), $font, $unitNo);
}

imagepng($image, getCachedLiveryPath($car, $id, $unitNo));

header("Content-Type: image/png");
echo imagepng($image);
