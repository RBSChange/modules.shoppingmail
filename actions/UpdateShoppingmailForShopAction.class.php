<?php
/**
 * shoppingmail_UpdateShoppingmailForShopAction
 * @package modules.shoppingmail.actions
 */
class shoppingmail_UpdateShoppingmailForShopAction extends generic_UpdateJSONAction
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
				
		$propertiesNames = explode(',', $request->getParameter('documentproperties', ''));	
		$propertiesNames[] = 'documentversion';
		$propertiesValue = array();
		foreach ($propertiesNames as $propertyName)
		{
			if ($request->hasParameter($propertyName))
			{
				$propertiesValue[$propertyName] = $request->getParameter($propertyName);
			}			
		}
		
		uixul_DocumentEditorService::getInstance()->importFieldsData($document, $propertiesValue);	
		$document->save();
		
		$this->logAction($document);

		$propertiesNames[] = 'id';
		$propertiesNames[] = 'lang';
		$data = $this->exportFieldsData($document, $propertiesNames);
		$data['id'] = $shop->getId();
		return $this->sendJSON($data);
	}
}