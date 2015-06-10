<?php
if (!defined('_PS_VERSION_'))
  exit;


class ProductCNFOptions extends Module
{
	public function __construct()
	{
		$this->name = 'mymodule';
		$this->tab = 'cnf_modules';
		$this->version = '0.0.1';
		$this->author = 'CNF Marketing';
		$this->need_instance = 0;
		$this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
		$this->bootstrap = true;

		parent::__construct();

		$this->displayName = $this->l('CNF Product Options');
		$this->description = $this->l('Adds extra data fields for products.');

		$this->confirmUninstall = $this->l('Are you sure you want to uninstall? Any data saved with this module will be lost!');

		if (!Configuration::get('MYMODULE_NAME'))
			$this->warning = $this->l('No name provided');
	}

	public function install()
	{
		if(!parent::install())
			return false;

		return true;
	}
}
