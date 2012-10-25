<?php
/**
 * Styles.php
 *
 * @copyright Copyright (c) 2012 Silverpop Systems, Inc.
 * @link      http://www.silverpop.com Silverpop Systems, Inc.
 * @author    Jerry Ablan <jablan@silverpop.com>
 *
 * @filesource
 */
namespace CIS\Services\Console;

/**
 * Styles
 * Pre-defined output styles for colorized text
 */
class Styles extends \CIS\Components\BaseConstant implements \CIS\Interfaces\AnsiColor, \CIS\Interfaces\AnsiConsole
{
	//*************************************************************************
	//* Constants
	//*************************************************************************

	/**
	 * Styles that are available as tags in console output.
	 * You can modify these styles with ConsoleOutput::styles()
	 *
	 * @var array
	 */
	protected static $_predefinedStyles = array(
		'emergency' => array( self::Red, self::Underscore ),
		'alert'     => array( self::Red, self::Underscore ),
		'critical'  => array( self::Red, self::Underscore ),
		'error'     => array( self::Red, self::Underscore ),
		'warning'   => array( self::Yellow ),
		'info'      => array( self::Cyan ),
		'debug'     => array( self::Yellow ),
		'success'   => array( self::Green ),
		'comment'   => array( self::Blue ),
		'question'  => array( self::Magenta ),
	);

	/**
	 * Retrieves the definition for the style of $name
	 *
	 * @param string $name
	 *
	 * @return array|false
	 */
	public static function getStyle( $name )
	{
		return \CIS\Utility\Option::get( self::$_predefinedStyles, $name, false );
	}

	/**
	 * @return array Returns all predefined styles
	 */
	public static function getStyles()
	{
		return self::$_predefinedStyles;
	}
}
