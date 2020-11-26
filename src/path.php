<?php

namespace Photon\Generator\Path;

function pathJoin(...$paths){
	$paths = array_filter($paths);
	return join(DIRECTORY_SEPARATOR, $paths);
}
