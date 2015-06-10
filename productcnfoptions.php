<?php
if (!defined('_PS_VERSION_'))
  exit;


class ProductCNFOptions extends Module
{
	public function __construct()
	{
		$this->name = 'productcnfoptions';
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

		if (!Configuration::get('CNF_OPTION'))
			$this->warning = $this->l('No name provided');
	}

	public function install()
	{
		if (Shop::isFeatureActive()) // Check if Multistore is enabled
			Shop::setContext(Shop::CONTEXT_ALL); // Set the "context" to all stores

		if(!parent::install() || // Check that the Module parent class is installed (this is the "Module" that our current class extends
		  !$this->registerHook('leftColumn') || // Check that we can hook into the leftColumn
		  !$this->registerHook('header') || // Check that we can hook into the header
		  !Configuration::updateValue('CNF_OPTION', 'CNF Product Options')) // Check that we were able to successfully install a config value for key CNF_OPTION
			return false; // If any of the above fail, return false

		return true; // Successfully installed
	}

	public function uninstall()
	{
		if (!parent::uninstall() || // Check that the parent Module class
		   !Configuration::deleteByName('CNF_OPTION'))
			return false;

		return true;
	}

	public function getContent()
	{
		$output = null;

		if (Tools::isSubmit('submit'.$this->name)) // Checks if the form has been "validated", ie. submitted
		{
			$my_module_name = strval(Tools::getValue('CNF_OPTION')); // Gets our option as a string

			if (!$my_module_name // Checks if the value is false
			  || empty($my_module_name) // Checks if the value is empty
			  || !Validate::isGenericName($my_module_name)) // Checks if the value is a "name"
				$output .= $this->displayError($this->l('Invalid Configuration value')); // Outputs an error
			else
			{
				Configuration::updateValue('CNF_OPTION', $my_module_name); // Updates the configuration value
				$output .= $this->displayConfirmation($this->l('Settings updated')); // Let the user know everything worked
			}
		}
		return $output.$this->displayForm(); // Output the config pages form
	}
}
