<?php
/**
 * shoppingmail_BlockTrackerAction
 * @package modules.shoppingmail.lib.blocks
 */
class shoppingmail_BlockTrackerAction extends website_BlockAction
{
	/**
	 * @param f_mvc_Request $request
	 * @param f_mvc_Response $response
	 * @return String
	 */
	public function execute($request, $response)
	{
		if ($this->isInBackofficeEdition())
		{
			return website_BlockView::NONE;
		}
		
		if (order_CartService::getInstance()->hasCartInSession())
		{
			$cartInfo = order_CartService::getInstance()->getDocumentInstanceFromSession();
			$order = $cartInfo->getOrder();
			if ($order)
			{
				$trackerUrl = shoppingmail_ShoppingmailService::getInstance()->buildTrackerUrl($order);
				if ($trackerUrl)
				{
					$request->setAttribute('trackerUrl', $trackerUrl);
					return website_BlockView::SUCCESS;
				}
			}
		}
		return  website_BlockView::NONE;	
	}
}