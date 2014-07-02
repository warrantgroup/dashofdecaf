<?php

namespace Library;

class Foo {

	private $bar;
	
	public function __construct($bar)
	{
		$this->bar = $bar;
	}
	
	public function getBar()
	{
		return $this->bar;
	}
	
	public function setBar($bar)
	{
		$this->bar = $bar;
	}
}

?>