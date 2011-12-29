<?php
/**
 * Class where to put your custom methods for document shoppingmail_persistentdocument_shoppingmail
 * @package modules.shoppingmail.persistentdocument
 */
class shoppingmail_persistentdocument_shoppingmail extends shoppingmail_persistentdocument_shoppingmailbase 
{
	
	/**
	 * @return boolean
	 */
	public function isOrderType()
	{
		return ($this->getNotificationTypes() & shoppingmail_ShoppingmailService::TYPE_ORDER) == shoppingmail_ShoppingmailService::TYPE_ORDER;
	}
	
	/**
	 * @return boolean
	 */
	public function isShippingType()
	{
		return ($this->getNotificationTypes() & shoppingmail_ShoppingmailService::TYPE_SHIPPING) == shoppingmail_ShoppingmailService::TYPE_SHIPPING;
	}
	
	/**
	 * @return string
	 */
	public function getBoNotifType()
	{
		$result = array();
		$type = $this->getNotificationTypes();
		if ($type & shoppingmail_ShoppingmailService::TYPE_ORDER)
		{
			$result[] = shoppingmail_ShoppingmailService::TYPE_ORDER;
		}
		if ($type & shoppingmail_ShoppingmailService::TYPE_SHIPPING)
		{
			$result[] = shoppingmail_ShoppingmailService::TYPE_SHIPPING;
		}
		return count($result) ? implode(',', $result) : null;
	}
	
	/**
	 * @param string $string
	 */
	public function setBoNotifType($string)
	{
		$notificationTypes = 0;
		$parts = explode(',', $string);
		foreach ($parts as $value)
		{
			$notificationTypes += intval($value);
		}
		$this->setNotificationTypes($notificationTypes);
	}
}