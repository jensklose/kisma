<?php
/**
 * Kisma(tm) : PHP Nanoframework (http://github.com/Pogostick/kisma/)
 * Copyright 2011, Pogostick, LLC. (http://www.pogostick.com/)
 *
 * Dual licensed under the MIT License and the GNU General Public License (GPL) Version 2.
 * See {@link http://github.com/Pogostick/kisma/licensing/} for complete information.
 *
 * @copyright		Copyright 2011, Pogostick, LLC. (http://www.pogostick.com/)
 * @link			http://github.com/Pogostick/kisma/ Kisma(tm)
 * @license			http://github.com/Pogostick/kisma/licensing/
 * @author			Jerry Ablan <kisma@pogostick.com>
 * @category		Kisma_Components
 * @package			kisma.components
 * @since			v1.0.0
 * @filesource
 */
namespace Kisma\Components;

use Kisma\K;
use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Controller
 * The base class for controllers
 *
 * Provides two event handlers:
 *
 * before_action and after_action which are called before and after the action is called, respectively.
 */
abstract class Controller extends Seed implements \Silex\ControllerProviderInterface, \Kisma\IController
{
	//*************************************************************************
	//* Private Members
	//*************************************************************************

	/**
	 * @var string The default action when none specified
	 */
	protected $_defaultAction = 'index';
	/**
	 * @var array The actions of this controller
	 */
	protected $_actions = null;
	/**
	 * @var string The name of this controller
	 */
	protected $_controllerName = null;
	/**
	 * @var null|array The custom routes for this controller
	 * @todo implement
	 */
	protected $_routes = null;
	/** @var mixed The current database */
	protected $_db = null;
	/**
	 * @var bool True if request was a post vs. get
	 */
	protected $_isPost = false;

	//*************************************************************************
	//* Public Methods
	//*************************************************************************

	/**
	 * actions requests to actions
	 *
	 * @param \Kisma\Kisma|\Silex\Application $app
	 *
	 * @return ControllerCollection A ControllerCollection instance
	 */
	public function connect( Application $app )
	{
		$_defaultRoute = null;
		$_controllers = new \Silex\ControllerCollection();
		$_actions = $this->_discoverActions();
		$_tag = 'controller.' . $this->_controllerName;

		$app[$_tag] = $this;

		//	Set up a route for each discovered action...
		foreach ( $_actions as $_action => $_method )
		{
			//	Build the route, along with default if specified...
			$_route = ( '/' != $_action ? '/' . $_action : '/' );

			$_controllers->match( $_route,
				function( Application $app, Request $request ) use( $_action, $_method, $_tag )
				{
					$_event = new \Kisma\Event\ControllerEvent( $app[$_tag] );

					$app[$_tag]->setIsPost(
						( \Kisma\HttpMethod::Post == $request->getMethod() )
					)->dispatch( \Kisma\Event\ControllerEvent::BeforeAction, $_event );

					$_event->setResult(
						$_result = call_user_func( array( $app[$_tag], $_method ), $app, $request )
					);

					$app[$_tag]->dispatch(
						\Kisma\Event\ControllerEvent::AfterAction,
						$_event
					);

					return $_result;

				}
			);
		}

		//	Return the collection...
		return $_controllers;
	}

	/**
	 * @return array
	 */
	protected function _discoverActions()
	{
		if ( null !== $this->_actions )
		{
			return $this->_actions;
		}

		$_actions = array();
		$_mirror = new \ReflectionClass( $this );

		foreach ( $_mirror->getMethods( \ReflectionMethod::IS_PUBLIC ) as $_method )
		{
			if ( 'action' == strtolower( substr( $_method->name, strlen( $_method->name ) - 6,
				6 ) ) && 'on' != strtolower( substr( $_method->name, 0, 2 ) )
			)
			{
				$_routeName =
					lcfirst( \Kisma\Utility\Inflector::camelize( str_ireplace( 'Action', null, $_method->name ) ) );

				$_actions[$_routeName] = $_method->name;

				//	Add a default action/route to the discovered list if wanted
				if ( !empty( $this->_defaultAction ) && 0 == strcasecmp( $this->_defaultAction, $_routeName ) )
				{
					$_actions['/'] = $_method->name;
				}
			}
		}

		$this->_controllerName =
			lcfirst( \Kisma\Utility\Inflector::camelize( str_ireplace( array( 'ControllerProvider', 'Controller' ),
				null,
				$_mirror->getShortName() ) ) );

		return $this->_actions = $_actions;
	}

	//*************************************************************************
	//* Event Handlers
	//*************************************************************************

	/**
	 * @param \Kisma\Event\ComponentEvent $event
	 *
	 * @return bool
	 */
	public function onAfterConstruct( \Kisma\Event\ComponentEvent $event )
	{
		$this->_discoverActions();

		return parent::onAfterConstruct( $event );
	}

	/**
	 * @param \Kisma\Event\ControllerEvent $event
	 *
	 * @return bool
	 */
	public function onBeforeAction( \Kisma\Event\ControllerEvent $event )
	{
		//	Default implementation
		return true;
	}

	/**
	 * @param \Kisma\Event\ControllerEvent $event
	 *
	 * @return bool
	 */
	public function onAfterAction( \Kisma\Event\ControllerEvent $event )
	{
		//	Default implementation
		return true;
	}

	//*************************************************************************
	//* Properties
	//*************************************************************************

	/**
	 * @param array $actions
	 *
	 * @return \Kisma\Components\Controller
	 */
	public function setActions( $actions )
	{
		$this->_actions = $actions;
		return $this;
	}

	/**
	 * @return array
	 */
	public function getActions()
	{
		return $this->_actions;
	}

	/**
	 * @param string $controllerName
	 *
	 * @return \Kisma\Components\Controller
	 */
	public function setControllerName( $controllerName )
	{
		$this->_controllerName = $controllerName;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getControllerName()
	{
		return $this->_controllerName;
	}

	/**
	 * @param array|null $routes
	 *
	 * @return \Kisma\Components\Controller
	 */
	public function setRoutes( $routes )
	{
		$this->_routes = $routes;
		return $this;
	}

	/**
	 * @return array|null
	 */
	public function getRoutes()
	{
		return $this->_routes;
	}

	/**
	 * @param boolean $isPost
	 *
	 * @return \Kisma\Components\Controller
	 */
	public function setIsPost( $isPost )
	{
		$this->_isPost = $isPost;
		return $this;
	}

	/**
	 * @return boolean
	 */
	public function getIsPost()
	{
		return $this->_isPost;
	}

	/**
	 * @return boolean
	 */
	public function isPost()
	{
		return $this->_isPost;
	}

	/**
	 * @param mixed $db
	 *
	 * @return \Kisma\Components\Controller
	 */
	public function setDb( $db )
	{
		$this->_db = $db;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getDb()
	{
		return $this->_db;
	}

	/**
	 * @param string $defaultAction
	 *
	 * @return \Kisma\Components\Controller
	 */
	public function setDefaultAction( $defaultAction )
	{
		$this->_defaultAction = $defaultAction;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getDefaultAction()
	{
		return $this->_defaultAction;
	}

}