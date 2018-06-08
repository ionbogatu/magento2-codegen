<?php
/**
 * Created by PhpStorm.
 * User: Ion Bogatu
 * Date: 6/8/2018
 * Time: 5:41 PM
 */

namespace Ibg\Codegen\Controller\Adminhtml\Module;

use Ibg\Codegen\Controller\Adminhtml\AbstractAjaxAction;
use Magento\Framework\App\ResponseInterface;

class CreateOrSelect extends AbstractAjaxAction
{
    /**
     * Execute action based on request and return result
     *
     * Note: Request will be added as operation argument in future
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     */
    public function execute()
    {
        $result = $this->isAjaxAndPost();

        $params = $this->getRequest()->getParams();



        return $result;
    }
}