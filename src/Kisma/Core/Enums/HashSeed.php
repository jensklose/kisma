<?php
/**
 * This file is part of Kisma(tm).
 *
 * Kisma(tm) <https://github.com/kisma/kisma>
 * Copyright 2009-2014 Jerry Ablan <jerryablan@gmail.com>
 *
 * Kisma(tm) is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Kisma(tm) is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Kisma(tm).  If not, see <http://www.gnu.org/licenses/>.
 */
namespace Kisma\Core\Enums;

/**
 * HashSeed
 * The various supported hash types for the Hash utility class
 */
class HashSeed extends SeedEnum implements \Kisma\Core\Interfaces\HashSeed
{
	//*************************************************************************
	//* Constants
	//*************************************************************************

	/**
	 * @var int Default value
	 */
	const __default = self::ALL;
	//*************************************************************************
	//* Constants
	//*************************************************************************
	/**
	 * @const int The various supported hash types for Utility\Hash
	 */
	const ALL = 0;
	/**
	 * @const int The various supported hash types for Utility\Hash
	 */
	const ALPHA_LOWER = 1;
	/**
	 * @const int The various supported hash types for Utility\Hash
	 */
	const ALPHA_UPPER = 2;
	/**
	 * @const int The various supported hash types for Utility\Hash
	 */
	const ALPHA = 3;
	/**
	 * @const int The various supported hash types for Utility\Hash
	 */
	const ALPHA_NUMERIC = 4;
	/**
	 * @const int The various supported hash types for Utility\Hash
	 */
	const ALPHA_LOWER_NUMERIC = 5;
	/**
	 * @const int The various supported hash types for Utility\Hash
	 */
	const NUMERIC = 6;
	/**
	 * @const int The various supported hash types for Utility\Hash
	 */
	const ALPHA_LOWER_NUMERIC_IDIOT_PROOF = 7;
}
