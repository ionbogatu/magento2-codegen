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
use Ibg\Codegen\Logger\Logger as CodegenLogger;
use Ibg\Codegen\Helper\ModuleGenerator as ModuleGeneratorHelper;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Config\CacheInterface as FrontendCache;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;
use Magento\Framework\Serialize\Serializer\Serialize as Serializer;

class CreateControllerAndRoute extends AbstractAjaxAction
{
    const ROUTE_CONFIG_CACHE_ID = 'RoutesConfig';

    const ACTION_CONFIG_CACHE_ID = 'app_action_list';

    /**
     * @var ControllerAndRouteGeneratorHelper
     */
    private $controllerAndRouteGeneratorHelper;
    /**
     * @var ModuleGeneratorHelper
     */
    private $moduleGeneratorHelper;
    /**
     * @var FrontendCache
     */
    private $frontendCache;
    /**
     * @var Serializer
     */
    private $serializer;
    /**
     * @var JsonSerializer
     */
    private $jsonSerializer;

    /**
     * CreateControllerAndRoute constructor.
     * @param Action\Context $context
     * @param CodegenLogger $codegenLogger
     * @param ControllerAndRouteGeneratorHelper $controllerAndRouteGeneratorHelper
     * @param ModuleGeneratorHelper $moduleGeneratorHelper
     * @param FrontendCache $frontendCache
     * @param JsonSerializer $jsonSerializer
     * @param Serializer $serializer
     */
    public function __construct(
        Action\Context $context,
        CodegenLogger $codegenLogger,
        ControllerAndRouteGeneratorHelper $controllerAndRouteGeneratorHelper,
        ModuleGeneratorHelper $moduleGeneratorHelper,
        FrontendCache $frontendCache,
        JsonSerializer $jsonSerializer,
        Serializer $serializer
    )
    {
        parent::__construct($context, $codegenLogger);

        $this->controllerAndRouteGeneratorHelper = $controllerAndRouteGeneratorHelper;
        $this->moduleGeneratorHelper = $moduleGeneratorHelper;
        $this->frontendCache = $frontendCache;
        $this->jsonSerializer = $jsonSerializer;
        $this->serializer = $serializer;
    }

    /**
     * Execute action based on request and return result
     *
     * Note: Request will be added as operation argument in future
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     */
    public function execute(){
        $this->codegenLogger->info('Start controller and route action: ' . time());
        $params = $this->getRequest()->getParams();

        $result = $this->resultFactory->create(ResultFactory::TYPE_JSON);

        try{
            $this->isAjaxAndPost();

            if(!empty($params['destination'])){
                if($params['destination'] === 'create'){
                    $this->moduleGeneratorHelper->createModule();
                }else if($params['destination'] === 'select'){
                    $this->moduleGeneratorHelper->selectModule();
                }
            }

            $resultData = $this->createController();
        }catch(\Exception $e){
            $result->setData(['success' => false, 'message' => $e->getMessage()]);
            return $result;
        }

        $result->setData($resultData);
        $this->codegenLogger->info('End controller and route action: ' . time());
        return $result;
    }

    /**
     * @throws \Exception
     */
    protected function validateParams()
    {
        $params = $this->getRequest()->getParams();

        // validate module creation
        if(!empty($params['destination'])){
            if(empty($params['module_name'])){
                throw new \Exception(__('You did not select any module to put new controller.'));
            }

            $pattern = $this->moduleGeneratorHelper->getModuleNameRegEx();
            $pregMatchResult = preg_match($pattern, $params['module_name'], $matches);
            if($pregMatchResult === 0){
                throw new \Exception(sprintf(__('%s is an invalid name for module name. Please choose another one that match the regular expression %s'), $params['module_name'], $pattern));
            }else if($pregMatchResult === false){
                throw new \Exception(__('Cannot validate module name.'));
            }
        }else{
            $params['module_name'] = $this->moduleGeneratorHelper->getCurrentlySelectedModule();
            $this->getRequest()->setParams($params);
        }

        // validate area
        if(empty($params['area'])){
            throw new \Exception(__('Area should be selected when creating new controller.'));
        }

        // validate front name
        if(empty($params['front_name'])){
            throw new \Exception(__('Front name cannot be empty when creating new controller.'));
        }

        $pattern = $this->controllerAndRouteGeneratorHelper->getFrontNameRegEx();
        $pregMatchResult = preg_match($pattern, $params['front_name'], $matches);
        if($pregMatchResult === 0){
            throw new \Exception(sprintf(__('%s is an invalid name for front name. Please choose another one that match the regular expression %s'), $params['front_name'], $pattern));
        }else if($pregMatchResult === false){
            throw new \Exception(__('Cannot validate front name.'));
        }

        // validate controller name
        if(empty($params['controller_name'])){
            throw new \Exception(__('Controller name cannot be empty when creating new controller.'));
        }

        $pattern = $this->controllerAndRouteGeneratorHelper->getControllerNameRegEx();
        $pregMatchResult = preg_match($pattern, $params['controller_name'], $matches);
        if($pregMatchResult === 0){
            throw new \Exception(sprintf(__('%s is an invalid name for controller. Please choose another one that match the regular expression %s'), $params['controller_name'], $pattern));
        }else if($pregMatchResult === false){
            throw new \Exception(__('Cannot validate controller name.'));
        }

        // validate action name
        if(empty($params['action_name'])){
            throw new \Exception(__('Action name cannot be empty when creating new controller.'));
        }

        $pattern = $this->controllerAndRouteGeneratorHelper->getActionNameRegEx();
        $pregMatchResult = preg_match($pattern, $params['action_name'], $matches);
        if($pregMatchResult === 0){
            throw new \Exception(sprintf(__('%s is an invalid name for action. Please choose another one that match the regular expression %s'), $params['action_name'], $pattern));
        }else if($pregMatchResult === false){
            throw new \Exception(__('Cannot validate action name.'));
        }

        $this->returnIfActionExists();
    }

    /**
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Exception
     */
    private function createController(){

        $filesToGenerate = $this->controllerAndRouteGeneratorHelper->getFilesToGenerate();

        $module_name = $this->moduleGeneratorHelper->getCurrentlySelectedModule();

        foreach($filesToGenerate as $file){

            if(is_array($file)){
                $copyPath = $file['copy_path'];
                $pastePath = $file['paste_path'];
            }else{
                $copyPath = $pastePath = $file;
            }

            $this->controllerAndRouteGeneratorHelper->copyFileToLocation(
                $copyPath,
                $this->controllerAndRouteGeneratorHelper->buildLocation($module_name, $pastePath),
                [
                    'modulePath' => trim(str_replace('_', '\\', $module_name)),
                    'moduleName' => $module_name,
                    'frontName' => $this->_request->getParam('front_name'),
                    'controllerName'=> $this->_request->getParam('controller_name'),
                    'actionName'=> $this->_request->getParam('action_name')
                ],
                $pastePath
            );
        }

        $this->updateCache();

        return ['success' => true];
    }

    private function updateCache(){
        $params = $this->getRequest()->getParams();

        // ==================== Routes cache ====================
        $cacheId = $params['area'] . '::' . self::ROUTE_CONFIG_CACHE_ID;

        $cachedData = $this->frontendCache->load($cacheId);

        if($cachedData) {
            $routes = $this->jsonSerializer->unserialize($cachedData);
        }else{
            $routes = [];
        }

        if(empty($routes[$params['front_name']])){
            $routes[$params['front_name']] = [
                'id' => $params['front_name'],
                'frontName' => $params['front_name'],
                'modules' => [$params['module_name']]
            ];
        }else{
            if(empty($routes[$params['front_name']]['modules'][$params['front_name']])){
                $routes[$params['front_name']]['modules'][] = $params['front_name'];
            }
        }

        $routesData = $this->jsonSerializer->serialize($routes);

        $this->frontendCache->save($routesData, $cacheId);

        // ==================== Actions cache ====================

        $cacheId = self::ACTION_CONFIG_CACHE_ID;

        $cachedData = $this->frontendCache->load($cacheId);
        $actions = $this->serializer->unserialize($cachedData);

        $area = ($params['area'] === 'adminhtml') ? '\\adminhtml' : '';
        $actionKey = strtolower(str_replace('_', '\\', $params['module_name']) . '\\controller' . $area . '\\' . $params['controller_name'] . '\\' . $params['action_name']);

        $area = ($params['area'] === 'adminhtml') ? '\\Adminhtml' : '';
        $actionValue = str_replace('_', '\\', $params['module_name']) . '\\Controller' . $area . '\\' . $params['controller_name'] . '\\' . $params['action_name'];

        $actions[$actionKey] = $actionValue;
        $actionsData = $this->serializer->serialize($actions);
        $this->frontendCache->save($actionsData, $cacheId);
    }

    /**
     * @throws \Exception
     */
    private function returnIfActionExists(){
        $params = $this->getRequest()->getParams();

        $cacheId = self::ACTION_CONFIG_CACHE_ID;
        $cachedData = $this->frontendCache->load($cacheId);
        $actions = $this->serializer->unserialize($cachedData);

        $area = ($params['area'] === 'adminhtml') ? '\\adminhtml' : '';
        $actionKey = strtolower(str_replace('_', '\\', $params['module_name']) . '\\controller' . $area . '\\' . $params['controller_name'] . '\\' . $params['action_name']);

        if(isset($actions[$actionKey])){
            throw new \Exception(__('This action already exists.'));
        }
    }
}
