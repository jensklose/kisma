<?php
/**
 * ResponseLike.php
 */
namespace Kisma\Core\Interfaces;
/**
 * ResponseLike
 */
interface ResponseLike extends BagLike
{
	//*************************************************************************
	//* Constants
	//*************************************************************************

	/**
	 * @var int
	 */
	const Failure = 0;
	/**
	 * @var int
	 */
	const Success = 1;

	//*************************************************************************
	//* Methods
	//*************************************************************************

	/**
	 * Returns true if the service call was successful
	 *
	 * @return bool
	 */
	public function success();
}