<?php

namespace FilipSedivy\EET\Schema;

class Wsdl
{
	public static function playground () {
		return __DIR__.'/PlaygroundService.wsdl';
	}

	public static function production () {
		return __DIR__.'/ProductionService.wsdl';
	}

}
