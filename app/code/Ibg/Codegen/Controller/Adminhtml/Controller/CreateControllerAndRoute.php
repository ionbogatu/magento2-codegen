<?php
/**
 * Created by PhpStorm.
 * User: Ion Bogatu
 * Date: 6/16/2018
 * Time: 4:17 PM
 */

namespace Ibg\Codegen\Controller\Adminhtml\Controller;

use Ibg\Codegen\Controller\Adminhtml\AbstractAjaxAction;
use Magento\Backend\App\Action;
use Magento\Framework\App\ResponseInterface;
use Ibg\Codegen\Helper\ControllerAndRouteGenerator as ControllerAndRouteGeneratorHelper;

class CreateControllerAndRoute extends AbstractAjaxAction
{
    /**
     * @var ControllerAndRouteGeneratorHelper
     */
    private $controllerAndRouteGeneratorHelper;

    /**
     * CreateControllerAndRoute constructor.
     * @param Action\Context $context
     * @param ControllerAndRouteGeneratorHelper $controllerAndRouteGeneratorHelper
     */
    public function __construct(
        Action\Context $context,
        ControllerAndRouteGeneratorHelper $controllerAndRouteGeneratorHelper
    )
    {
        parent::__construct($context);

        $this->controllerAndRouteGeneratorHelper = $controllerAndRouteGeneratorHelper;
    }

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

        $resultData = [];
        try{
            $this->createController();
        }catch(\Exception $e){
            $result->setData(['success' => false, 'message' => $e->getMessage()]);
            return $result;
        }
    }

    /**
     * @throws \Exception
     */
    protected function validateParams()
    {
        $params = $this->getRequest()->getParams();

        if(empty($params['area'])){
            throw new \Exception(__('Area should be selected when creating new controller.'));
        }

        if(empty($params['front_name'])){
            throw new \Exception(__('Front name cannot be empty when creating new controller.'));
        }

        // validate front name
        $pattern = $this->controllerAndRouteGeneratorHelper->getFrontNameRegEx();
        $pregMatchResult = preg_match($pattern, $params['front_name'], $matches);
        if($pregMatchResult === 0){
            throw new \Exception(sprintf(__('%s is an invalid name for fron name. Please choose another one that match the regular expression %s'), $params['front_name'], $pattern));
        }else if($pregMatchResult === false){
            throw new \Exception(__('Cannot validate front name.'));
        }

        if(empty($params['controller_name'])){
            throw new \Exception(__('Front name cannot be empty when creating new controller.'));
        }
        // validate controller name
        $pattern = $this->controllerAndRouteGeneratorHelper->getControllerNameRegEx();
        $pregMatchResult = preg_match($pattern, $params['controller_name'], $matches);
        if($pregMatchResult === 0){
            throw new \Exception(sprintf(__('%s is an invalid name for fron name. Please choose another one that match the regular expression %s'), $params['controller_name'], $pattern));
        }else if($pregMatchResult === false){
            throw new \Exception(__('Cannot validate controller name.'));
        }

        if(empty($params['action_name'])){
            throw new \Exception(__('Action name cannot be empty when creating new controller.'));
        }
        // validate controller name
        $pattern = $this->controllerAndRouteGeneratorHelper->getActionNameRegEx();
        $pregMatchResult = preg_match($pattern, $params['action_name'], $matches);
        if($pregMatchResult === 0){
            throw new \Exception(sprintf(__('%s is an invalid name for fron name. Please choose another one that match the regular expression %s'), $params['action_name'], $pattern));
        }else if($pregMatchResult === false){
            throw new \Exception(__('Cannot validate action name.'));
        }
    }

    /**
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    private function createController(){

        $filesToGenerate = $this->controllerAndRouteGeneratorHelper->getFilesToGenerate();
    }
}