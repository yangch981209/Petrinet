<?php
/**
 * @package     Petrinet
 * @subpackage  Type
 *
 * @copyright   Copyright (C) 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

/**
 * Interface for custom types.
 *
 * @package     Petrinet
 * @subpackage  Type
 * @since       1.0
 */
interface PNType
{
	/**
	 * Check the given variable matches the type.
	 *
	 * @param   mixed  $var  A PHP variable.
	 *
	 * @return  boolean  True if the variable matches, false otherwise.
	 *
	 * @since   1.0
	 */
	public function check($var);

	/**
	 * Return a value compatible with this type.
	 *
	 * @return  mixed  A variable value.
	 *
	 * @since   1.0
	 */
	public function test();
}
