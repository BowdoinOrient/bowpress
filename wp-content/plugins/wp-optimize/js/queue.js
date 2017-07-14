/*

Adapted and extended from the work of Stephen Morley - http://code.stephenmorley.org/javascript/queues/

Queue.js

A function to represent a queue

Created by Stephen Morley - http://code.stephenmorley.org/ - and released under
the terms of the CC0 1.0 Universal legal code:

http://creativecommons.org/publicdomain/zero/1.0/legalcode

*/

/**
 * Creates a new queue. A queue is a first-in-first-out (FIFO) data structure -
 * items are added to the end of the queue and removed from the front.
 * @returns {void}
 */
function Updraft_Queue() {

	// initialise the queue and offset
	var queue  = [];
	var offset = 0;
	var locked = false;

	/**
	* Returns the length of the queue.
	* 
	* @returns {number} - the length of the queue
	*/
	this.get_length = function() {
		return (queue.length - offset);
	}

	/**
	* Query whether the queue is empty or not
	* 
	* @returns {boolean} - returns true if the queue is empty, and false otherwise.
	*/
	this.is_empty = function() {
		return (queue.length == 0);
	}

	/**
	* 
	* Enqueues the specified item. The parameter is:
	*
	* @param {*} item - the item to enqueue
	* @returns {void}
	*/
	this.enqueue = function(item) {
		queue.push(item);
	}
	
	/**
	 * Returns the queue lock status
	 * 
	 * @returns {boolean} - whether the queue is locked or not
	 */
	this.is_locked = function() {
		return locked;
	}
	
	/**
	 * Attempt to get the queue lock
	 * 
	 * @returns {boolean} - whether the attempt succeeded or not
	 */
	this.get_lock = function() {
		if (locked) { return false; }
		this.lock();
		return true;
	}

	/**
	* Dequeues an item and returns it. If the queue is empty, the value
	* 'undefined' is returned.
	* 
	* @returns {*} - returns and removes the item at the front of the queue, or undefined if the queue is empty
	*/
	this.dequeue = function() {

		// if the queue is empty, return immediately
		if (queue.length == 0) return undefined;

		// store the item at the front of the queue
		var item = queue[offset];

		// increment the offset and remove the free space if necessary
		if (++ offset * 2 >= queue.length) {
			queue  = queue.slice(offset);
			offset = 0;
		}

		// return the dequeued item
		return item;

	}

	/**
	 * Lock the queue
	 * @returns {void}
	 */
	this.lock = function() {
		locked = true;
	}
	
	/**
	 * Unlock the queue
	 * @returns {void}
	 */
	this.unlock = function() {
		locked = false;
	}
	
	/**
	* Returns the item at the front of the queue (without dequeuing it). If the
	* queue is empty then undefined is returned.
	* 
	* @returns {*} - returns the item at the front of the queue, or undefined if the queue is empty
	*/
	this.peek = function() {
		return (queue.length > 0 ? queue[offset] : undefined);
	}
	
	/**
	 * Replaces the item at the front of the queue (if any)
	 * 
	 * @param {*} item - the item to put at the front of the queue
	 * 
	 * @returns {boolean} - whether or not the item was successfully replaced
	 */
	this.replace_front = function(item) {
		if (queue.length < 1) { return false; }
		queue[offset] = item;
		return true;
	}

}
