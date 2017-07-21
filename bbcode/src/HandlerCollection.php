<?php

namespace bbcode;

/**
 *	@author 3kZO
 */
class HandlerCollection
{
	
	/**
	 *	@var callback[]
	 */
	private $handlers = [];
	
	public function has($tag) {
		return isset($this->handlers[$tag]);
	}
	
	public function set($tag, $callback) {
		
		if (is_object($callback) && !method_exists($callback, '__invoke')) {
		//	throw new \InvalidArgumentException();
		}
		
		$this->handlers[$tag] = $callback;
		
		return $this;
		
	}
	
	public function get($tag) {
		return $this->has($tag) ? $this->handlers[$tag] : null;
	}
	
}