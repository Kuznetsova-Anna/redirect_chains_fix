<?php

class AK_RedirectChains_Block_Adminhtml_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * AK_RedirectChains_Block_Adminhtml_Edit constructor.
     */
    public function __construct()
    {
        Varien_Object::__construct();
        $this->_addButton('update_redirect', array(
            'label'     => Mage::helper('redirectchains')->__('Update'),
            'onclick'   => 'editForm.submit();',
            'class'     => 'update',
        ), 1);
    }

    /**
     * Header text getter
     *
     * @return string
     */
    public function getHeaderText()
    {
        return Mage::helper('redirectchains')->__('Update Redirect Chains');
    }
}