<?php

namespace Photon\Generator\Cache;

use function Photon\Generator\Path\pathJoin;

define('PHOTON_CACHE_BASE_DIR', pathJoin(__DIR__, '..', 'cache'));

/* Generic Caches */
/** Get the cache path for a specific file.
 * @param string $file The file name to check.
 * @param string|null $intermediate The intermediate directory (for multi-level caches)
 * @return string Full path.
 */
function getCachePath(string $file, string $intermediate = null): string {
	$path = pathJoin(PHOTON_CACHE_BASE_DIR, $intermediate, $file);
	if (!file_exists(dirname($path))){mkdir(dirname($path), 0777, true);}
	return $path;
}

/** Returns if the cache has a given file.
 * @param string $file File name.
 * @param string|null $intermediate Intermediate dir.
 * @return bool If the cached file exists.
 */
function hasCachedFile(string $file, string $intermediate = null): bool {
	return file_exists(getCachePath($file, $intermediate));
}

/** Get the contents of a cached file.
 * @param string $file File name.
 * @param string|null $intermediate Intermediate dir.
 */
function getCachedFile(string $file, string $intermediate = null): void {
	$path = getCachePath($file, $intermediate);
	touch($path);
	readfile($path);
}

/* Livery Caches */
function getLiveryPath(string $car = 'unknown', string $idx = 'unknown', string $unitno = ''): string {
	return "{$car}-{$idx}-{$unitno}.png";
}
function hasCachedLivery(string $car = 'unknown', string $idx = 'unknown', string $unitno = ''): bool {
	return hasCachedFile(getLiveryPath($car, $idx, $unitno), 'livery');
}
function getCachedLivery(string $car = 'unknown', string $idx = 'unknown', string $unitno = ''): void {
	getCachedFile(getLiveryPath($car, $idx, $unitno), 'livery');
}
function getCachedLiveryPath(string $car = 'unknown', string $idx = 'unknown', string $unitno = ''): string {
	return getCachePath(getLiveryPath($car, $idx, $unitno), 'livery');
}

/* AirEL Caches */
function getAirElPath(string $idx = 'unknown', string $unitno = ''): string {
	return "{$idx}-{$unitno}.png";
}
function hasCachedAirEl(string $idx = 'unknown', string $unitno = ''): bool {
	return hasCachedFile(getAirElPath($idx, $unitno), 'airel');
}
function getCachedAirEl(string $idx = 'unknown', string $unitno = ''): void {
	getCachedFile(getAirElPath($idx, $unitno), 'airel');
}
function getCachedAirElPath(string $idx = 'unknown', string $unitno = ''): string {
	return getCachePath(getAirElPath($idx, $unitno), 'airel');
}