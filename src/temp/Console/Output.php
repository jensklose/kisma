<?php
/**
 * Output.php
 *
 * @copyright Copyright (c) 2012 Silverpop Systems, Inc.
 * @link      http://www.silverpop.com Silverpop Systems, Inc.
 * @author    Jerry Ablan <jablan@silverpop.com>
 *
 * @filesource
 */
namespace CIS\Services\Console;

/**
 * Output
 */
class Output extends \CIS\Services\BaseService implements \CIS\Interfaces\Services\ConsoleEvents, \CIS\Interfaces\AnsiColor, \CIS\Interfaces\AnsiConsole
{
	//**************************************************************************
	//* Private Members
	//**************************************************************************

	/**
	 * @var resource Our output stream
	 */
	protected $_stream;
	/**
	 * @var int The mode with which to output
	 */
	protected $_mode = self::PlainText;

	//**************************************************************************
	//* Public Methods
	//**************************************************************************

	/**
	 * @param array $options
	 */
	public function __construct( $options = array() )
	{
		parent::__construct( $options );

		if ( empty( $this->_stream ) )
		{
			$this->_stream = self::StdOut;
		}

		if ( !is_resource( $this->_stream ) )
		{
			$this->_stream = fopen( $this->_stream, 'w' );
		}
	}

	/**
	 * Choose your destructor!
	 *
	 */
	public function __destruct()
	{
		try
		{
			if ( is_resource( $this->_stream ) )
			{
				@fclose( $this->_stream );
			}
		}
		catch ( \Exception $_ex )
		{
			//	Nada
		}
	}

	/**
	 * @param string   $text
	 * @param bool|int $lineFeed
	 *
	 * @return bool
	 */
	public function write( $text, $lineFeed = true )
	{
		$_text = implode( PHP_EOL, ( !empty( $text ) && !is_array( $text ) ? array( $text ) : $text ) );

		return $this->_writeToStream(
			$this->_colorize(
				$_text .
					( str_repeat( PHP_EOL, ( true === $lineFeed ? 1 : ( false === $lineFeed ? 0 : $lineFeed ) ) ) )
			)
		);

	}

	/**
	 * @param string $text
	 *
	 * @return string
	 */
	protected function _colorize( $text )
	{
		switch ( $this->_mode )
		{
			case self::PlainText:
				$_styles = implode( '|', array_keys( Styles::getStyles() ) );
				return preg_replace( '#</?(?:' . $_styles . ')>#', '', $text );

			case self::ColorizedText:
				return preg_replace_callback(
					'/<(?P<tag>[a-z0-9-_]+)>(?P<text>.*?)<\/(\1)>/ims',
					array( $this, '_parseMatches' ),
					$text
				);
		}

		return $text;
	}

	/**
	 * @param array $matches.
	 *
	 * @return string
	 */
	protected function _parseMatches( $matches )
	{
		$_style = Styles::getStyle( $matches['tag'] );

		if ( empty( $_style ) )
		{
			return '<' . $matches['tag'] . '>' . $matches['text'] . '</' . $matches['tag'] . '>';
		}

		return "\033[" . implode( $_style, ';' ) . 'm' . $matches['text'] . "\033[0m";
	}

	/**
	 * @param string $text
	 *
	 * @return boolean
	 */
	protected function _writeToStream( $text )
	{
		return fwrite( $this->_stream, $text );
	}

	//**************************************************************************
	//* Properties
	//**************************************************************************

	/**
	 * @param int $mode
	 *
	 * @return int
	 */
	public function setMode( $mode )
	{
		$this->_mode = $mode;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getMode()
	{
		return $this->_mode;
	}

	/**
	 * @param resource $stream
	 *
	 * @return resource
	 */
	public function setStream( $stream )
	{
		$this->_stream = $stream;
		return $this;
	}

	/**
	 * @return resource
	 */
	public function getStream()
	{
		return $this->_stream;
	}

}

class ConsoleOutput
{
	/**
	 * Outputs a single or multiple messages to stdout. If no parameters
	 * are passed, outputs just a newline.
	 *
	 * @param string|array $message  A string or a an array of strings to output
	 * @param integer      $newlines Number of newlines to append
	 *
	 * @return integer Returns the number of bytes returned from writing to stdout.
	 */
	public function write( $message, $newlines = 1 )
	{
		if ( is_array( $message ) )
		{
			$message = implode( self::LF, $message );
		}
		return $this->_write( $this->styleText( $message . str_repeat( self::LF, $newlines ) ) );
	}

	/**
	 * Apply styling to text.
	 *
	 * @param string $text Text with styling tags.
	 *
	 * @return string String with color codes added.
	 */
	public function styleText( $text )
	{
		if ( $this->_outputAs == self::RAW )
		{
			return $text;
		}
		if ( $this->_outputAs == self::PLAIN )
		{
			$tags = implode( '|', array_keys( self::$_styles ) );
			return preg_replace( '#</?(?:' . $tags . ')>#', '', $text );
		}
		return preg_replace_callback(
			'/<(?P<tag>[a-z0-9-_]+)>(?P<text>.*?)<\/(\1)>/ims', array( $this, '_replaceTags' ), $text
		);
	}

}
