<?php
/**
 * shoppingmail_ShoppingmailService
 * @package modules.shoppingmail
 */
class shoppingmail_ShoppingmailService extends f_persistentdocument_DocumentService
{
	
	const TYPE_ORDER = 1;
	
	const TYPE_SHIPPING = 2;
	
	
	/**
	 * @var shoppingmail_ShoppingmailService
	 */
	private static $instance;

	/**
	 * @return shoppingmail_ShoppingmailService
	 */
	public static function getInstance()
	{
		if (self::$instance === null)
		{
			self::$instance = self::getServiceClassInstance(get_class());
		}
		return self::$instance;
	}

	/**
	 * @return shoppingmail_persistentdocument_shoppingmail
	 */
	public function getNewDocumentInstance()
	{
		return $this->getNewDocumentInstanceByModelName('modules_shoppingmail/shoppingmail');
	}

	/**
	 * Create a query based on 'modules_shoppingmail/shoppingmail' model.
	 * Return document that are instance of modules_shoppingmail/shoppingmail,
	 * including potential children.
	 * @return f_persistentdocument_criteria_Query
	 */
	public function createQuery()
	{
		return $this->pp->createQuery('modules_shoppingmail/shoppingmail');
	}
	
	/**
	 * Create a query based on 'modules_shoppingmail/shoppingmail' model.
	 * Only documents that are strictly instance of modules_shoppingmail/shoppingmail
	 * (not children) will be retrieved
	 * @return f_persistentdocument_criteria_Query
	 */
	public function createStrictQuery()
	{
		return $this->pp->createQuery('modules_shoppingmail/shoppingmail', false);
	}
	
	/**
	 * @param catalog_persistentdocument_shop $shop
	 * @return shoppingmail_persistentdocument_shoppingmail or null
	 */
	public function getByShop($shop)
	{
		if ($shop instanceof catalog_persistentdocument_shop)
		{
			return $this->createQuery()->add(Restrictions::eq('shop', $shop))->findUnique();
		}
		return null;
	}
	
	/**
	 * @param integer $shopId
	 * @return shoppingmail_persistentdocument_shoppingmail or null
	 */
	public function getByShopId($shopId)
	{
		if (intval($shopId) > 0)
		{
			return $this->createQuery()->add(Restrictions::eq('shop.id', intval($shopId)))->findUnique();
		}
		return null;
	}
	
	/**
	 * @param order_persistentdocument_order $order
	 * @return string or null
	 */
	public function buildTrackerUrl($order)
	{
		if (($order instanceof order_persistentdocument_order) &&  $order->getShopId())
		{
			$shop = $order->getShop();
			$document = $this->getByShop($shop);
			if ($document && $document->getActive())
			{
				$token= $document->getToken();
				$amount = strval(catalog_PriceFormatter::getInstance()->round($order->getTotalAmountWithTax(), $order->getCurrencyCode()));
				return 'http://www.shopping-mail.com/service/track.php?token='.$token.'&amount='.$amount.'&order_id=' . $order->getId();
			}
		}
		return null;
	}
	
	/**
	 * @param order_persistentdocument_order $order
	 * @param shoppingmail_persistentdocument_shoppingmail $order
	 * @param integer $type
	 * @return string
	 */
	public function buildEmailHTMLFooter($order, $shoppingmail, $type)
	{
		if (($order instanceof order_persistentdocument_order) &&  $order->getShopId())
		{
			$shop = $order->getShop();
			$document = $this->getByShop($shop);
			if ($document && $document->getActive())
			{
				$params = array(
					"token"=>$document->getToken(),
					"order_id"=>$order->getId(),
					"mail_type"=> ($type === self::TYPE_ORDER) ? 'order': 'shipping',
					"code"=>time(),
				);			
				$testData = Framework::getConfigurationValue('modules/shoppingmail/test', false);
				if ($testData)
				{
					return $this->callWSTest($params, $testData);
				}
				return $this->callWS($params);
			}
		}
		return null;		
	}
	
	/**
	 * @param array $parameters
	 * @return string
	 */
	protected function callWS($parameters)
	{
		try 
		{
			$serviceUrl = 'http://www.shopping-mail.com/service/work.php';
			$curl_get_data = http_build_query($parameters);
			$curl = curl_init($serviceUrl."?".$curl_get_data);
			curl_setopt($curl, CURLOPT_POST, false);
			curl_setopt($curl, CURLOPT_HEADER, false);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
			$curl_response = curl_exec($curl);
			if ($curl_response === false)
			{
				Framework::error(__METHOD__ . ' curl error: ' . curl_errno($curl) . ', ' . curl_error($curl));
				$curl_response = null;
			}
			curl_close($curl);
			return $curl_response;
		} 
		catch (Exception $e) 
		{
			Framework::exception($e);
		}
		return null;
	}
	
	/**
	 *
	 * @param array $parameters
	 * @param string $testData
	 * @return string
	 */
	protected function callWSTest($parameters, $testData)
	{
		try
		{
			$serviceUrl = 'http://dev.shopping-mail.com/service/work_demo.php';
			list($token, $userPwd) = explode('|', $testData);
			
			$parameters['token'] = $token;
			$curl_get_data = http_build_query($parameters);
			$curl = curl_init($serviceUrl."?".$curl_get_data);
			if ($userPwd)
			{
				curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
				curl_setopt($curl, CURLOPT_USERPWD, $userPwd);
			}
			curl_setopt($curl, CURLOPT_POST, false);
			curl_setopt($curl, CURLOPT_HEADER, false);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
			$curl_response = curl_exec($curl);
			if ($curl_response === false)
			{
				Framework::error(__METHOD__ . ' curl error: ' . curl_errno($curl) . ', ' . curl_error($curl));
				$curl_response = null;
			}
			curl_close($curl);
			return $curl_response;
		}
		catch (Exception $e)
		{
			Framework::exception($e);
		}
		return null;
	}
	
	
	/**
	 * @param shoppingmail_persistentdocument_shoppingmail $document
	 * @param Integer $parentNodeId Parent node ID where to save the document (optionnal => can be null !).
	 * @return void
	 */
	protected function preSave($document, $parentNodeId)
	{
		if ($document->getLabel() == null &&$document->getShop())
		{
			$document->setLabel($document->getShop()->getLabel());
		}
	}
		
}