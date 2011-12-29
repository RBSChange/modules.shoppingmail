<?php
/**
 * shoppingmail_ShoppingmailScriptDocumentElement
 * @package modules.shoppingmail.persistentdocument.import
 */
class shoppingmail_ShoppingmailScriptDocumentElement extends import_ScriptDocumentElement
{
    /**
     * @return shoppingmail_persistentdocument_shoppingmail
     */
    protected function initPersistentDocument()
    {
    	return shoppingmail_ShoppingmailService::getInstance()->getNewDocumentInstance();
    }
    
    /**
	 * @return f_persistentdocument_PersistentDocumentModel
	 */
	protected function getDocumentModel()
	{
		return f_persistentdocument_PersistentDocumentModel::getInstanceFromDocumentModelName('modules_shoppingmail/shoppingmail');
	}
}