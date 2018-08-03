<?php


class AK_RedirectChains_Block_Adminhtml_Form extends Mage_Adminhtml_Block_Widget_Form
{

    /**
     * @return mixed
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array('id' => 'update_redirect_chain', 'action' => $this->getData('action'), 'method' => 'post'));
        $fieldset = $form->addFieldset('main_fieldset', array('legend' => Mage::helper('redirectchains')->__('Fix Redirect Chains')));
        $fieldset->addField('redirect_chain', 'note', array(
            'text' => Mage::helper('redirectchains')
                ->__('Pass the first urls of the redirect chains and the intermediate ones will be removed. The first url will lead to the last one.')
        ));
        $fieldset->addField('store', 'select', array(
            'name'    => 'store',
            'label'   => Mage::helper('redirectchains')->__('Select a store'),
            'class'   => 'required-entry',
            'required'  => true,
            'options' => Mage::getModel('AK_RedirectChains_Model_Redirects')->getStores()
        ));
        $fieldset->addField('urls', 'textarea', array(
            'name'      => 'urls',
            'label'     => Mage::helper('redirectchains')->__('List the urls separated with comma (",")'),
            'style'     => 'width:32em;',
            'class'   => 'required-entry',
            'required'  => true,
            'note'    => Mage::helper('redirectchains')->__('Please pass the urls according to the chosen store.'),
        ));
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }


}
