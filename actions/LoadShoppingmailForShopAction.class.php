<?php
/**
 * shoppingmail_LoadShoppingmailForShopAction
 * @package modules.shoppingmail.actions
 */
class shoppingmail_LoadShoppingmailForShopAction extends generic_LoadJSONAction
{
	/**
	 * @param Context $context
	 * @param Request $request
	 */
	public function _execute($context, $request)
	{
		$shop = catalog_persistentdocument_shop::getInstanceById($this->getDocumentIdFromRequest($request));
		$document = shoppingmail_ShoppingmailService::getInstance()->getByShop($shop);
		
		if ($document === null)
		{
			$document = shoppingmail_ShoppingmailService::getInstance()->getNewDocumentInstance();
			$document->setShop($shop);
		}
		
		$allowedProperties = array('id', 'lang', 'documentversion');
		$requestedProperties = explode(',', $request->getParameter('documentproperties', ''));
		foreach ($requestedProperties as $propertyName)
		{
			if (f_util_StringUtils::isEmpty($propertyName)) {continue;}
			if (in_array($propertyName, $allowedProperties)) {continue;}
			$allowedProperties[] = $propertyName;
		}
		
		$data = $this->exportFieldsData($document, $allowedProperties);
		$data['id'] = $shop->getId();
		return $this->sendJSON($data);
	}
}