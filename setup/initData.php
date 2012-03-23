<?php
/**
 * @package modules.shoppingmail.setup
 */
class shoppingmail_Setup extends object_InitDataSetup
{
	public function install()
	{
		$mbs = uixul_ModuleBindingService::getInstance();
		$mbs->addImportInActions('catalog', 'shoppingmail', 'catalog.actions');
		
		// Add injection of order_ModuleService.
		$this->addInjectionInProjectConfiguration('order_ModuleService', 'shoppingmail_InjectedOrderModuleService');
	}

	/**
	 * @return String[]
	 */
	public function getRequiredPackages()
	{
		// Return an array of packages name if the data you are inserting in
		// this file depend on the data of other packages.
		// Example:
		// return array('modules_website', 'modules_users');
		return array();
	}
}