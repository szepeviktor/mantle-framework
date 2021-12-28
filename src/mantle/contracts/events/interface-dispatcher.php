<?php
/**
 * Dispatcher interface file.
 *
 * @package Mantle
 */

namespace Mantle\Contracts\Events;

/**
 * Event Dispatcher Contract
 */
interface Dispatcher {
	/**
	 * Register an event listener with the dispatcher.
	 *
	 * @param  string|array    $events
	 * @param  \Closure|string $listener
	 * @return void
	 */
	public function listen( $events, $listener );

	/**
	 * Determine if a given event has listeners.
	 *
	 * @param  string $event_name
	 * @return bool
	 */
	public function has_listeners( $event_name );

	/**
	 * Register an event subscriber with the dispatcher.
	 *
	 * @param  object|string $subscriber
	 * @return void
	 */
	public function subscribe( $subscriber );

	/**
	 * Dispatch an event and call the listeners.
	 *
	 * @param  string|object $event
	 * @param  mixed         $payload
	 * @param  bool          $halt
	 * @return array|null
	 */
	public function dispatch( $event, $payload = [] );

	/**
	 * Register an event and payload to be fired later.
	 *
	 * @param  string $event
	 * @param  array  $payload
	 * @return void
	 */
	public function push( $event, $payload = [] );

	/**
	 * Flush a set of pushed events.
	 *
	 * @param  string $event
	 * @return void
	 */
	public function flush( $event );

	/**
	 * Remove a set of listeners from the dispatcher.
	 *
	 * @param  string $event
	 * @return void
	 */
	public function forget( $event );

	/**
	 * Forget all of the queued listeners.
	 *
	 * @return void
	 */
	public function forget_pushed();
}
