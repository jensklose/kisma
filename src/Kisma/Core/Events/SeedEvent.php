<?php
/**
 * SeedEvent.php
 */
namespace Kisma\Core\Events;

/**
 * SeedEvent
 * The base class for Kisma events
 *
 * It encapsulates the parameters associated with an event.
 * The {@link source} property describes who raised the event.
 *
 * If an event handler calls the kill() method, propagation will halt.
 */
class SeedEvent
{
	//**************************************************************************
	//* Private Members
	//**************************************************************************

	/**
	 * @var object The source of this event
	 */
	protected $_source;
	/**
	 * @var boolean Set to true to stop the bubbling of events at any point
	 */
	protected $_kill = false;
	/**
	 * @var mixed Any event data the sender wants to convey
	 */
	protected $_data;

	//**************************************************************************
	//* Public Methods
	//**************************************************************************

	/**
	 * Constructor.
	 *
	 * @param object $source
	 * @param mixed  $data
	 */
	public function __construct( $source = null, $data = null )
	{
		$this->_source = $source;
		$this->_data = $data;
		$this->_kill = false;
	}

	/**
	 * Kills propagation immediately
	 *
	 * @return SeedEvent
	 */
	public function kill()
	{
		$this->_kill = true;

		return $this;
	}

	/**
	 * @return bool
	 */
	public function wasKilled()
	{
		return ( false !== $this->_kill );
	}

	//**************************************************************************
	//* Properties
	//**************************************************************************

	/**
	 * @param mixed $data
	 *
	 * @return SeedEvent
	 */
	public function setData( $data )
	{
		$this->_data = $data;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getData()
	{
		return $this->_data;
	}

	/**
	 * @param \Kisma\Core\Seed $source
	 *
	 * @return SeedEvent
	 */
	public function setSource( $source )
	{
		$this->_source = $source;

		return $this;
	}

	/**
	 * @return \Kisma\Core\Seed
	 */
	public function getSource()
	{
		return $this->_source;
	}
}
