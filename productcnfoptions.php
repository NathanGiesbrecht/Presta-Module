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

		return !parent::install() &&
			$this->registerHook('leftColumn') &&
			$this->registerHook('header') &&
			Configuration::updateValue('CNF_OPTION', 'CNF Product Options');
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

	public function displayForm()
	{
		// Get default language
		$default_lang = (int)Configuration::get('PS_LANG_DEFAULT');

		// Init Fields form array
		$fields_form[0]['form'] = array(
			'legend' => array(
				'title' => $this->l('CNF Settings'),
			),
			'input' => array(
				array(
					'type' => 'text',
					'label' => $this->l('Configuration value'),
					'name' => 'CNF_OPTION',
					'size' => 20,
					'required' => true
				)
			),
			'submit' => array(
				'title' => $this->l('Save'),
				'class' => 'button'
			)
		);

		$helper = new HelperForm();

		// Module, token and currentIndex
		$helper->module = $this;
		$helper->name_controller = $this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;

		// Language
		$helper->default_form_language = $default_lang;
		$helper->allow_employee_form_lang = $default_lang;

		// Title and toolbar
		$helper->title = $this->displayName;
		$helper->show_toolbar = true; // false -> remove toolbar
		$helper->toolbar_scroll = true; // yes - > Toolbar is always visible on the top of the screen.
		$helper->submit_action = 'submit'.$this->name;
		$helper->toolbar_btn = array(
			'save' =>
			array(
				'desc' => $this->l('Save'),
				'href' => AdminController::$currentIndex.'&configure='.$this->name.'&save'.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules'),
			),
			'back' => array(
				'href' => AdminController::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminModules'),
				'desc' => $this->l('Back to list')
			)
		);

		// Load current value
		$helper->fields_value['CNF_OPTION'] = Configuration::get('CNF_OPTION');

		return $helper->generateForm($fields_form);
	}

	public function hookDisplayLeftColumn($params)
	{
		$this->context->smarty->assign(
			array(
				'my_module_name' => Configuration::get('CNF_OPTION'),
				'my_module_link' => $this->context->link->getModuleLink('mymodule', 'display')
			)
		);
		return $this->display(__FILE__, 'productcnfoptions.tpl');
	}

	public function hookDisplayRightColumn($params)
	{
		return $this->hookDisplayLeftColumn($params);
	}

	public function hookDisplayHeader()
	{
		$this->context->controller->addCSS($this->_path.'css/productcnfoptions.css', 'all');
	}
}
