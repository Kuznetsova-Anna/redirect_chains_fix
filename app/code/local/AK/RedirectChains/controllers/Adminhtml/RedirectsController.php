<?php


class AK_RedirectChains_Adminhtml_RedirectsController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Render Redirect Chains Fix page
     */
    public function indexAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('catalog');
        $this->renderLayout();
    }

    /**
     *
     */
    public function saveAction()
    {
        $data = $this->getRequest()->getPost();
        $data['urls'] = explode(',', $data['urls']);
        $storeName = Mage::app()->getStore($data['store'])->getName();

        try {
            $redirectLoopsFirstUrls = [];
            foreach ($data['urls'] as $url) {
                $url = trim($url);
                $redirectUrl = Mage::getModel('ak_redirectchains/redirects')->updateRedirect($url, $data['store'], 1, $urlsArray = []);
                if ($redirectUrl) {
                    $redirectLoopsFirstUrls[] = $redirectUrl;
                }
            }

            if (empty($redirectLoopsFirstUrls)) {
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('redirectchains')->__('The update has been completed for ' . $storeName . ' store.')
                );
            } else {
                $redirectLoopsFirstUrls = implode(', ', $redirectLoopsFirstUrls);
                Mage::getSingleton('adminhtml/session')->addSuccess(
                Mage::helper('redirectchains')
                    ->__('The update has been completed for ' . $storeName . ' store. Please handle loop redirects manually for: ' . $redirectLoopsFirstUrls)
                );
            }

        } catch (Mage_Core_Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('redirectchains')->__('Url chains update error')
            );
        }
        $this->_redirect('*/*/');
    }

}