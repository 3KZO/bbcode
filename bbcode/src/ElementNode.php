<?php

namespace bbcode;

/**
 *	@author 3kZO
 */
class ElementNode extends Node
{
	
	/**
	 *	@var string
	 */
	private $tag;
	
	/**
	 *	@var string[]
	 */
	private $attrs = [];
	
	/**
	 *	@var string[]
	 */
	private $childrens = [];
	
	/**
	 *	@var callback
	 */
	private $handler;
	
	public function setTag($tag) {
		
		$this->tag = $tag;
		return $this;
		
	}
	
	public function getTag() {
		return $this->tag;
	}
	
	public function hasAttr($attr) {
		return isset($this->attrs[$attr]);
	}
	
	public function setAttr($attr, $value) {
		$this->attrs[$attr] = $value;
		return $this;
	}
	
	public function getAttr($attr) {
		return $this->hasAttr($attr) ? $this->attrs[$attr] : null;
	}
	
	public function getAttrs() {
		return $this->attrs;
	}
	
	public function getChildrens() {
		return $this->childrens;
	}
	
	public function addChildren(Node $node) {
		
		$node->setParent($this);
		$this->childrens[] = $node;
		
		return $this;
		
	}
	
	public function removeChildren(Node $node) {
		
		foreach ($this->childrens as $key => $children) {
			if ($node === $children)
				unset($this->childrens[$key]);
		}
		
		return $this;
		
	}
	
	public function find($tag) {
		
		$parent = $this;
		
		while($tag != $parent->getTag() && $parent->hasParent()) {
			$parent = $parent->getParent();
		}
		
		return $tag == $parent->getTag() ? $parent : null;
		
	}
	
	public function contains($tag) {
		return null !== $this->find($tag);
	}
	
	public function setHandler($callback) {
		$this->handler = $callback;
		return $this;
	}
	
	public function getHandler() {
		return $this->handler;
	}
	
	public function handle() {
		
		if (null === $this->getHandler()) {
			return $this->getContent();
		}
		
		return call_user_func($this->getHandler(), $this);
		
	}
	
	public function getContent() {
		
		$content = [];
		
		foreach ($this->childrens as $child) {
			$content[] = $child->isTextNode() ? $child->getValue() : $child->handle();
		}
		
		return implode('', $content);
		
	}
	
}