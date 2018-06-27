<?php
/**
 * Created by PhpStorm.
 * User: Ion Bogatu
 * Date: 6/16/2018
 * Time: 4:17 PM
 */

namespace Ibg\Codegen\Controller\Adminhtml\Controller;

use Ibg\Codegen\Controller\Adminhtml\AbstractAjaxAction;
use Magento\Framework\App\ResponseInterface;

class CreateControllerAndRoute extends AbstractAjaxAction
{
    /**
     * Execute action based on request and return result
     *
     * Note: Request will be added as operation argument in future
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        $result = $this->isAjaxAndPost();

        $resultData = [];

        $params = $this->getRequest()->getParams();

        try{

        }catch(\Exception $e){
            $result->setData(['success' => false, 'message' => $e->getMessage()]);
            return $result;
        }
    }
}