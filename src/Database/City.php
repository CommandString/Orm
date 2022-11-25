<?php

namespace CommandString\Orm\Database;

class City extends \CommandString\Orm\Table
{
	public const ID = 'city.id';
	public const NAME = 'city.name';
	public const COUNTRYCODE = 'city.countrycode';
	public const DISTRICT = 'city.district';
	public const POPULATION = 'city.population';

	public string $name = 'city';
}
