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
namespace Kisma\Core\Utility;

use Kisma\Core\Enums\CoreSettings;
use Kisma\Core\Enums\Verbosity;
use Kisma\Core\Interfaces\Levels;
use Kisma\Core\Interfaces\UtilityLike;
use Kisma\Core\Seed;
use Kisma\Kisma;

/**
 * Log
 * A generic log helper
 */
class Log extends Seed implements UtilityLike, Levels
{
	//*************************************************************************
	//* Constants
	//*************************************************************************

	/**
	 * @var string The default log line format
	 */
	const DefaultLogFormat = '%%date%% %%time%% %%level%% %%message%% %%extra%%';
	/**
	 * @var string The relative path (from the Kisma base path) for the default log
	 */
	const DefaultLogFile = '/kisma.log';

	//********************************************************************************
	//* Members
	//********************************************************************************

	/**
	 * @var string Prepended to each log entry before writing.
	 */
	protected static $_prefix = null;
	/**
	 * @var integer The current indent level
	 */
	protected static $_currentIndent = 0;
	/**
	 * @var array The strings to watch for at the beginning of a log line to control the indenting
	 */
	protected static $_indentTokens
		= array(
			true  => '<*',
			false => '*>',
		);
	/**
	 * @var string
	 */
	protected static $_defaultLog = null;
	/**
	 * @var string The format of the log entries
	 */
	protected static $_logFormat = self::DefaultLogFormat;
	/**
	 * @var bool If true, pid, uid, and hostname are added to log entry
	 */
	protected static $_includeProcessInfo = false;
	/**
	 * @var bool Set when log file has been validated
	 */
	protected static $_logFileValid = false;

	//********************************************************************************
	//* Methods
	//********************************************************************************

	/**
	 * {@InheritDoc}
	 */
	public static function log( $message, $level = self::Info, $context = array(), $extra = null, $tag = null )
	{
		static $_firstRun = true;

		//	If we're not debugging, don't log debug statements
		if ( static::Debug == $level )
		{
			$_debug = Kisma::get( CoreSettings::VERBOSITY );

			if ( is_callable( $_debug ) )
			{
				$_debug = call_user_func( $_debug, $message, $level, $context, $extra, $tag );
			}

			if ( Verbosity::DEBUG != $_debug )
			{
				return true;
			}
		}

		if ( $_firstRun || !static::$_logFileValid )
		{
			static::$_logFileValid = static::_checkLogFile();

			if ( !static::$_logFileValid || empty( static::$_defaultLog ) || !file_exists( static::$_defaultLog ) )
			{
				static::setDefaultLog( LOG_SYSLOG );
				static::$_logFileValid = true;
			}

			$_firstRun = false;
		}

		$_timestamp = time();

		//	Get the indent, if any
		$_unindent = ( ( $_newIndent = static::_processMessage( $message ) ) > 0 );

		//	Indent...
		$_tempIndent = static::$_currentIndent;

		if ( $_unindent )
		{
			$_tempIndent--;
		}

		if ( $_tempIndent < 0 )
		{
			$_tempIndent = 0;
		}

		$_levelName = static::_getLogLevel( $level );

		$_entry = static::formatLogEntry(
						array(
							'level'     => $_levelName,
							'message'   => static::$_prefix . str_repeat( '  ', $_tempIndent ) . $message,
							'timestamp' => $_timestamp,
							'context'   => $context,
							'extra'     => $extra,
						)
		);

		if ( static::$_logFileValid || is_numeric( static::$_defaultLog ) )
		{
			error_log( $_entry );
		}
		else
		{
			error_log( $_entry, 3, static::$_defaultLog );
		}

		//	Set indent level...
		static::$_currentIndent += $_newIndent;

		//	Anything over a warning returns false so you can chain
		return ( static::Warning > $level );
	}

	/**
	 * Formats the log entry. You can override this method to provide you own formatting.
	 * It will strip out any console escape sequences as well
	 *
	 * @param array $entry Read the code, data in the array
	 * @param bool  $newline
	 *
	 * @return string
	 */
	public static function formatLogEntry( array $entry, $newline = true )
	{
		$_level = Option::get( $entry, 'level' );
		$_timestamp = Option::get( $entry, 'timestamp' );
		$_message = preg_replace( '/\033\[[\d;]+m/', null, Option::get( $entry, 'message' ) );
		$_context = Option::get( $entry, 'context' );
		$_extra = Option::get( $entry, 'extra' );

		$_blob = new \stdClass();

		if ( static::$_includeProcessInfo )
		{
			$_blob->pid = getmypid();
			$_blob->uid = getmyuid();
			$_blob->hostname = gethostname();
		}

		if ( !empty( $_context ) || !empty( $_extra ) )
		{
			if ( null !== $_context )
			{
				$_blob->context = $_context;
			}

			if ( null !== $_extra )
			{
				$_context->extra = $_extra;
			}
		}

		$_replacements = array(
			0 => $_level,
			1 => date( 'M d', $_timestamp ),
			2 => date( 'H:i:s', $_timestamp ),
			3 => $_message,
			4 => json_encode( $_blob ),
		);

		return str_ireplace(
			array(
				'%%level%%',
				'%%date%%',
				'%%time%%',
				'%%message%%',
				'%%extra%%',
			),
			$_replacements,
			static::$_logFormat
		) . ( $newline ? PHP_EOL : null );
	}

	/**
	 * Creates an 'error' log entry
	 *
	 * @param string $message The message to send to the log
	 * @param array  $context
	 * @param mixed  $extra
	 *
	 * @return bool
	 */
	public static function error( $message, $context = array(), $extra = null )
	{
		return static::log( $message, static::Error, $context, $extra, static::_getCallingMethod() );
	}

	/**
	 * Creates a 'warning' log entry
	 *
	 * @param string $message The message to send to the log
	 * @param array  $context
	 * @param mixed  $extra
	 *
	 * @return bool
	 */
	public static function warning( $message, $context = array(), $extra = null )
	{
		return static::log( $message, static::Warning, $context, $extra, static::_getCallingMethod() );
	}

	/**
	 * Creates a 'notice' log entry
	 *
	 * @param string $message The message to send to the log
	 * @param array  $context
	 * @param mixed  $extra
	 *
	 * @return bool
	 */
	public static function notice( $message, $context = array(), $extra = null )
	{
		return static::log( $message, static::Notice, $context, $extra, static::_getCallingMethod() );
	}

	/**
	 * Creates an 'info' log entry
	 *
	 * @param string $message The message to send to the log
	 * @param array  $context
	 * @param mixed  $extra
	 *
	 * @return bool
	 */
	public static function info( $message, $context = array(), $extra = null )
	{
		return static::log( $message, static::Info, $context, $extra, static::_getCallingMethod() );
	}

	/**
	 * Creates a 'debug' log entry
	 *
	 * @param string $message The message to send to the log
	 * @param array  $context
	 * @param mixed  $extra
	 *
	 * @return bool
	 */
	public static function debug( $message, $context = array(), $extra = null )
	{
		return static::log( $message, static::Debug, $context, $extra, static::_getCallingMethod() );
	}

	/**
	 * Safely decrements the current indent level
	 *
	 * @param int $howMuch
	 */
	public static function decrementIndent( $howMuch = 1 )
	{
		static::$_currentIndent -= $howMuch;

		if ( static::$_currentIndent < 0 )
		{
			static::$_currentIndent = 0;
		}
	}

	/**
	 * Makes the system log path if not there...
	 */
	public static function checkSystemLogPath()
	{
		if ( null !== ( $_path = getenv( 'KISMA_SYSTEM_LOG_PATH' ) ) )
		{
			@mkdir( $_path, 0777, true );
		}
	}

	/**
	 * @param int  $level
	 * @param bool $fullName
	 *
	 * @return string
	 */
	protected static function _getLogLevel( $level = self::Info, $fullName = false )
	{
		static $_logLevels = null;

		if ( empty( $_logLevels ) )
		{
			$_logLevels = \Kisma\Core\Enums\Levels::getDefinedConstants();
		}

		$_levels = ( is_string( $level ) ? $_logLevels : array_flip( $_logLevels ) );

		if ( null === ( $_tag = Option::get( $_levels, $level ) ) )
		{
			$_tag = 'INFO';
		}

		return ( false === $fullName ? substr( strtoupper( $_tag ), 0, 4 ) : $_tag );
	}

	/**
	 * Returns the name of the method that made the call
	 *
	 * @return string
	 */
	protected static function _getCallingMethod()
	{
		$_backTrace = debug_backtrace();

		$_thisClass = get_called_class();
		$_type = $_class = $_method = null;

		for ( $_i = 0, $_size = sizeof( $_backTrace ); $_i < $_size; $_i++ )
		{
			if ( isset( $_backTrace[$_i]['class'] ) )
			{
				$_class = $_backTrace[$_i]['class'];
			}

			if ( $_class == $_thisClass )
			{
				continue;
			}

			if ( isset( $_backTrace[$_i]['method'] ) )
			{
				$_method = $_backTrace[$_i]['method'];
			}
			else if ( isset( $_backTrace[$_i]['function'] ) )
			{
				$_method = $_backTrace[$_i]['function'];
			}
			else
			{
				$_method = 'Unknown';
			}

			$_type = $_backTrace[$_i]['type'];
			break;
		}

		if ( $_i >= 0 )
		{
			return str_ireplace( 'Kisma\\Core\\', 'Core\\', $_class ) . $_type . $_method;
		}

		return 'Unknown';
	}

	/**
	 * Processes the indent level for the messages
	 *
	 * @param string $message
	 *
	 * @return integer The indent difference AFTER this message
	 */
	protected static function _processMessage( &$message )
	{
		$_newIndent = 0;

		foreach ( static::$_indentTokens as $_key => $_token )
		{
			if ( $_token == substr( $message, 0, strlen( $_token ) ) )
			{
				$_newIndent = ( false === $_key ? -1 : 1 );
				$message = substr( $message, strlen( static::$_indentTokens[true] ) );
			}
		}

		return $_newIndent;
	}

	/**
	 * Makes sure we have a log file name and path
	 */
	protected static function _checkLogFile()
	{
		//	Set a name for the default log
		if ( null === static::$_defaultLog )
		{
			$_logPath = Kisma::get( 'app.log_path', Kisma::get( 'app.base_path' ) ) ? : getcwd();
			static::$_defaultLog = $_logPath . static::DefaultLogFile;
		}
		else
		{
			if ( !is_file( static::$_defaultLog ) && is_dir( static::$_defaultLog ) )
			{
				$_logPath = static::$_defaultLog;
				static::$_defaultLog .= static::DefaultLogFile;
			}
			else
			{
				$_logPath = dirname( static::$_defaultLog );
			}
		}

		//	Try and create the path
		if ( !is_dir( $_logPath ) )
		{
			if ( false === @mkdir( $_logPath, 0777, true ) )
			{
				return false;
			}
		}

		return true;
	}

	/**
	 * @static
	 *
	 * @param int $currentIndent
	 *
	 * @return void
	 */
	public static function setCurrentIndent( $currentIndent = 0 )
	{
		static::$_currentIndent = $currentIndent;
	}

	/**
	 * @static
	 * @return int
	 */
	public static function getCurrentIndent()
	{
		return static::$_currentIndent;
	}

	/**
	 * @static
	 *
	 * @param string $prefix
	 *
	 * @return void
	 */
	public static function setPrefix( $prefix = null )
	{
		static::$_prefix = $prefix;
	}

	/**
	 * @static
	 * @return null|string
	 */
	public static function getPrefix()
	{
		return static::$_prefix;
	}

	/**
	 * @param array $indentTokens
	 */
	public static function setIndentTokens( $indentTokens )
	{
		static::$_indentTokens = $indentTokens;
	}

	/**
	 * @return array
	 */
	public static function getIndentTokens()
	{
		return static::$_indentTokens;
	}

	/**
	 * @param string $defaultLog
	 */
	public static function setDefaultLog( $defaultLog )
	{
		static::$_defaultLog = $defaultLog;

		if ( null !== $defaultLog )
		{
			@mkdir( dirname( static::$_defaultLog ), 0777, true );
		}
	}

	/**
	 * @return null|string
	 */
	public static function getDefaultLog()
	{
		return static::$_defaultLog;
	}

	/**
	 * @param string $logFormat
	 */
	public static function setLogFormat( $logFormat )
	{
		static::$_logFormat = $logFormat;
	}

	/**
	 * @return string
	 */
	public static function getLogFormat()
	{
		return static::$_logFormat;
	}

	/**
	 * @param boolean $includeProcessInfo
	 */
	public static function setIncludeProcessInfo( $includeProcessInfo )
	{
		self::$_includeProcessInfo = $includeProcessInfo;
	}

	/**
	 * @return boolean
	 */
	public static function getIncludeProcessInfo()
	{
		return self::$_includeProcessInfo;
	}
}

Log::checkSystemLogPath();
