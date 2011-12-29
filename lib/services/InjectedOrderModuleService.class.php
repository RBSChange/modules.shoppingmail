<?php
/**
 * @package modules.shoppingmail.lib.services
 */
class shoppingmail_InjectedOrderModuleService extends order_ModuleService
{
	/**
	 * @param notification_persistentdocument_notification $configuredNotif
	 * @param order_persistentdocument_order $order
	 * @param order_persistentdocument_bill $bill
	 * @param order_persistentdocument_expedition $expedition
	 * @param array $specificParams
 	 * @return boolean
	 */
	protected function doSendCustomerNotification($configuredNotif, $order, $bill, $expedition, $specificParams)
	{
		if ($configuredNotif instanceof notification_persistentdocument_notification)
		{
			try
			{
				$shoppingmail = shoppingmail_ShoppingmailService::getInstance()->getByShop($order->getShop());
				
				if ($shoppingmail && $shoppingmail->getActive())
				{
					if ($shoppingmail->isOrderType())
					{
						if (strpos($configuredNotif->getCodename(), 'modules_order/bill_success') === 0)
						{
							$specificParams['shoppingmail'] = $shoppingmail;
							$specificParams['shoppingmail_type'] = shoppingmail_ShoppingmailService::TYPE_ORDER;
						}
						elseif (strpos($configuredNotif->getCodename(), 'modules_order/bill_waiting') === 0)
						{
							$specificParams['shoppingmail'] = $shoppingmail;
							$specificParams['shoppingmail_type'] = shoppingmail_ShoppingmailService::TYPE_ORDER;
						}
					}
					if ($shoppingmail->isShippingType())
					{
						if (strpos($configuredNotif->getCodename(), 'modules_order/expedition_shipped') === 0)
						{
							$specificParams['shoppingmail'] = $shoppingmail;
							$specificParams['shoppingmail_type'] = shoppingmail_ShoppingmailService::TYPE_SHIPPING;
						}
					}
					
					if (isset($specificParams['shoppingmail']))
					{
						 $configuredNotif->setFooter($configuredNotif->getFooter() . '{shoppingmailhtml}');		 		
					}
				}
			} 
			catch (Exception $e) 
			{
				Framework::exception($e);
			}
		}
		return parent::doSendCustomerNotification($configuredNotif, $order, $bill, $expedition, $specificParams);	
	}
	
	
	/**
	 * @param array $params an array containing the keys 'order', 'bill', 'expedition' and 'specificParams'
	 * @return array
	 */
	public function getNotificationParameters($params)
	{	
		if (isset($params['specificParams']) && isset($params['specificParams']['shoppingmail']))
		{
			$shoppingmail = $params['specificParams']['shoppingmail'];
			unset($params['specificParams']['shoppingmail']);
			$shoppingmailType = $params['specificParams']['shoppingmail_type'];
			unset($params['specificParams']['shoppingmail_type']);
		}
		else
		{
			$shoppingmail = null;
		}
		
		$parameters = parent::getNotificationParameters($params);
		if ($shoppingmail)
		{
			$parameters['shoppingmailhtml'] = shoppingmail_ShoppingmailService::getInstance()->buildEmailHTMLFooter($params['order'], $shoppingmail, $shoppingmailType);
		}
		
		return $parameters;
	}
}