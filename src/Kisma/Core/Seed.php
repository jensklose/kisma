<?php
/**
 * This file is part of Kisma(tm)
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
namespace Kisma\Core;

use Kisma\Core\Enums\Events\SeedEvents;
use Kisma\Core\Events\SeedEvent;
use Kisma\Core\Interfaces\PublisherLike;
use Kisma\Core\Interfaces\SeedLike;
use Kisma\Core\Interfaces\SubscriberLike;
use Kisma\Core\Utility\Inflector;
use Kisma\Core\Utility\Option;
use Kisma\Kisma;

/**
 * Seed
 * A nugget of goodness that grows into something wonderful
 *
 * Seed provides a simple publish/subscribe service. You're free to use it or not. Never required.
 *
 * Publish/Subscribe
 * =================
 * A simple publish/subscribe service. Yeah, fancy name for event system.
 *
 * An event is defined by the presence of a method whose name starts with 'on'.
 * The event name is the method name. When an event is triggered, event handlers attached
 * to the event will be invoked automatically.
 *
 * An event is triggered by calling the {@link publish} method. Attached event
 * handlers will be invoked automatically in the order they were attached to the event.
 *
 * Event handlers should have the following signature:
 * <pre>
 * public|protected|private function [_]onEventName( $event = null ) { ... }
 * </pre>
 *
 * $event (\Kisma\Core\Events\SeedEvent) will contain details about the event in question.
 *
 * To subscribe to an event, call the {@link EventManager::subscribe} method.
 *
 * You may also use closures for event handlers, ala jQuery
 *
 * This class has a two default events:
 *   - after_construct
 *   - before_destruct
 *
 * Unless otherwise specified, the object will automatically search for and
 * attach any event handlers that exist in your object.
 *
 * To disable this feature, set $discoverEvents to false before calling the parent constructor.
 *
 * Properties
 * ==========
 *
 * The properties below are default in every Seed object. In addition, when you constract a Seed
 * object, any values passed to the constructor will be set in the created object. There are no
 * checks for invalid properties. If the property does not exist, it will be added as public. No
 * getter or setter will be created however. Use of the new property is entirely up to you.
 *
 * @property-read string $id              A unique ID assigned to this object, the last part of which is the creation time
 * @property string      $tag             The tag of this object. Defaults to the base name of the class
 * @property string      $name            The name of this object. Defaults tot he class name
 * @property bool        $discoverEvents  Defaults to true.
 * @property string      $eventManager    Defaults to \Kisma\Core\Utility\EventManager
 */
class Seed implements SeedLike, PublisherLike
{
	//********************************************************************************
	//* Variables
	//********************************************************************************

	/**
	 * @var string The unique ID of this seed
	 */
	private $_id;
	/**
	 * @var string A "key" quality tag for this object. Defaults to the key-inflected base class name (i.e. "seed")
	 */
	protected $_tag;
	/**
	 * @var string A display quality name for this object. Defaults to the full class name (i.e. "\Kisma\Core\Seed")
	 */
	protected $_name;
	/**
	 * @var bool If false, event handlers must be defined manually
	 */
	protected $_discoverEvents = false;
	/**
	 * @var \Symfony\Component\EventDispatcher\EventDispatcher The class name of the event manager
	 */
	protected $_eventManager = null;

	//********************************************************************************
	//* Methods
	//********************************************************************************

	/**
	 * Base constructor
	 *
	 * @param array|object $settings An array of key/value pairs that will be placed into storage
	 */
	public function __construct( $settings = array() )
	{
		//	Since $_id is read-only we remove if you try to set it
		if ( null !== ( $_id = Option::get( $settings, 'id' ) ) )
		{
			Option::remove( $settings, 'id' );
		}

		//	Otherwise, set the rest
		if ( is_array( $settings ) || is_object( $settings ) || $settings instanceof \Traversable )
		{
			foreach ( $settings as $_key => $_value )
			{
				if ( property_exists( $this, $_key ) )
				{
					try
					{
						Option::set( $this, $_key, $_value );
						unset( $settings, $_key );
						continue;
					}
					catch ( \Exception $_ex )
					{
						//	Ignore...
					}
				}

				$_setter = Inflector::tag( 'set_' . $_key );

				if ( method_exists( $this, $_setter ) )
				{
					call_user_func( array( $this, $_setter ), $_value );
					unset( $settings, $_key, $_setter );
				}
			}
		}

		//	Wake-up the events
		$this->__wakeup();
	}

	/**
	 * When unserializing an object, this will re-attach any event handlers...
	 */
	public function __wakeup()
	{
		//	This is my hash. There are many like it, but this one is mine.
		$this->_id = hash( 'sha256', spl_object_hash( $this ) . getmypid() . microtime( true ) );

		//	Auto-set tag and name if they're empty
		if ( null === $this->_tag )
		{
			$this->_tag = Inflector::neutralize( get_called_class(), true );
		}

		$this->_name = $this->_name ? : $this->_tag;

		$this->_eventManager = Kisma::getDispatcher();

		if ( empty( $this->_eventManager ) || !( $this instanceof SubscriberLike ) )
		{
			//	Ignore event junk later
			$this->_eventManager = false;
			$this->_discoverEvents = false;
		}

		//	Add the event service and attach any event handlers we find...
		if ( $this->_eventManager && false !== $this->_discoverEvents )
		{
			//	Subscribe to events...
			call_user_func(
				array( $this->_eventManager, 'subscribe' ),
				$this
			);
		}

		//	Publish after_construct event
		$this->publish( SeedEvents::AFTER_CONSTRUCT );
	}

	/**
	 * Choose your destructor!
	 */
	public function __destruct()
	{
		try
		{
			//	Publish after_destruct event
			$this->publish( SeedEvents::BEFORE_DESTRUCT );
		}
		catch ( \Exception $_ex )
		{
			//	Does nothing, like the goggles.,,
			//	Well, may stop those bogus frame 0 errors too...
		}
	}

	/**
	 * Triggers an object event to all subscribers. Convenient wrapper on EM::publish
	 *
	 * @param string $eventName
	 * @param mixed  $eventData
	 *
	 * @return bool|int
	 */
	public function publish( $eventName, $eventData = null )
	{
		if ( $this->_eventManager )
		{
			return $this->_eventManager->dispatch( $eventName, new SeedEvent( $this, $eventName, $eventData ) );
		}
	}

	/**
	 * @param string        $tag
	 * @param callable|null $listener
	 *
	 * @return bool
	 */
	public function on( $tag, $listener = null )
	{
		$_dispatcher = Kisma::getDispatcher();

		//  Add our event handlers
		if ( $_dispatcher && $this instanceof SubscriberLike )
		{
			$_dispatcher->addListener( $tag, $listener );
		}

		return false;
	}

	/**
	 * @param string        $tag
	 * @param callable|null $listener
	 */
	public function off( $tag, $listener = null )
	{
		$_dispatcher = Kisma::getDispatcher();

		//  Add our event handlers
		if ( $_dispatcher && $this instanceof SubscriberLike )
		{
			if ( null === $listener )
			{
				$_listeners = $_dispatcher->getListeners( $tag );
			}
			else
			{
				$_listeners = array( $listener );
			}

			foreach ( $_listeners as $_listener )
			{
				$_dispatcher->removeListener( $tag, $_listener );
			}
		}
	}

	/**
	 * @param boolean $discoverEvents
	 *
	 * @return Seed
	 */
	public function setDiscoverEvents( $discoverEvents )
	{
		$this->_discoverEvents = $discoverEvents;

		return $this;
	}

	/**
	 * @return boolean
	 */
	public function getDiscoverEvents()
	{
		return $this->_discoverEvents;
	}

	/**
	 * @param string $eventManager
	 *
	 * @return Seed
	 */
	public function setEventManager( $eventManager )
	{
		$this->_eventManager = $eventManager;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getEventManager()
	{
		return $this->_eventManager;
	}

	/**
	 * @return string
	 */
	public function getId()
	{
		return $this->_id;
	}

	/**
	 * @param string $name
	 *
	 * @return Seed
	 */
	public function setName( $name )
	{
		$this->_name = $name;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->_name;
	}

	/**
	 * @param string $tag
	 *
	 * @return Seed
	 */
	public function setTag( $tag )
	{
		$this->_tag = $tag;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getTag()
	{
		return $this->_tag;
	}
}
