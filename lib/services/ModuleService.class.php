<?php
/**
 * @package modules.shoppingmail.lib.services
 */
class shoppingmail_ModuleService extends ModuleBaseService
{
	/**
	 * Singleton
	 * @var shoppingmail_ModuleService
	 */
	private static $instance = null;

	/**
	 * @return shoppingmail_ModuleService
	 */
	public static function getInstance()
	{
		if (is_null(self::$instance))
		{
			self::$instance = self::getServiceClassInstance(get_class());
		}
		return self::$instance;
	}
}