<?php

class Object
{
	public function __construct($config = [])
	{
		if (!empty($config)) {
			foreach ($config as $k => $v) {
				$this->$k = $v;
			}
		}
		$this->init();
	}

	public function init(){}
}

class Config
{
	protected $_data = [];

	public function init()
	{
		if (file_exists($conf = __DIR__ . '/config.json')) {
			$this->_data = json_decode(file_get_contents($conf), true);
		}
	}

	public function get($key, $default = '')
	{
		return isset($this->_data[$key]) ? $this->_data[$key] : $default;
	}

	public function getBinPath($bin)
	{
		return $this->get($bin, $bin);
	}
}

$config = new Config();

