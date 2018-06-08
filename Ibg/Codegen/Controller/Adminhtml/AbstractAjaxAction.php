<?php
/**
 * Created by PhpStorm.
 * User: Ion Bogatu
 * Date: 6/8/2018
 * Time: 5:47 PM
 */

namespace Ibg\Codegen\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;

abstract class AbstractAjaxAction extends Action
{
    public function isAjaxAndPost()
    {
        $result = $this->resultFactory->create(ResultFactory::TYPE_JSON);

        $errorMessages = [];
        if($this->getRequest()->isPost()){
            $errorMessages = __('Unsupported HTTP Verb.');
        }

        if($this->getRequest()->isAjax()){
            $errorMessages = __('Only Ajax Requests are available');
        }

        if(!empty($errorMessages)){
            return $result->setData(['success' => false, 'error' => implode('\n', $errorMessages)]);
        }

        return $result;
    }
}