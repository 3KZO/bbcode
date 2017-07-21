<?php

namespace bbcode;

/**
 *	@author 3kZO
 */
class Node
{
	
	/**
	 *	@var Node
	 */
	private $parent;
	
	public function setParent(Node $parent) {
		$this->parent = $parent;
		return $this;
	}
	
	public function getParent() {
		return $this->parent;
	}
	
	public function hasParent() {
		return null !== $this->parent;
	}
	
	public function isTextNode() {
		return false;
	}
	
}