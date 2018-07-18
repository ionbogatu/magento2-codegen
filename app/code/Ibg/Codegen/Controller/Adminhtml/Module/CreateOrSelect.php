<?php
/**
 * Created by PhpStorm.
 * User: Ion Bogatu
 * Date: 6/8/2018
 * Time: 5:41 PM
 */

namespace Ibg\Codegen\Controller\Adminhtml\Module;

use Ibg\Codegen\Controller\Adminhtml\AbstractAjaxAction;
use Magento\Backend\App\Action;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Ibg\Codegen\Helper\ModuleGenerator as ModuleGeneratorHelper;
use Ibg\Codegen\Helper\ControllerAndRouteGenerator as ControllerAndRouteGeneratorHelper;
use Magento\Framework\Filesystem;
use Ibg\Codegen\Logger\Logger as CodegenLogger;
use Magento\Framework\Module\ModuleResource;

class CreateOrSelect extends AbstractAjaxAction
{
    /**
     * @var DirectoryList
     */
    private $directoryList;
    /**
     * @var ModuleGeneratorHelper
     */
    private $moduleGeneratorHelper;
    /**
     * @var Filesystem
     */
    private $filesystem;
    /**
     * @var ModuleResource
     */
    private $moduleResource;
    /**
     * @var ControllerAndRouteGeneratorHelper
     */
    private $controllerAndRouteGeneratorHelper;

    /**
     * CreateOrSelect constructor.
     * @param Action\Context $context
     * @param DirectoryList $directoryList
     * @param ModuleGeneratorHelper $moduleGeneratorHelper
     * @param ControllerAndRouteGeneratorHelper $controllerAndRouteGeneratorHelper
     * @param Filesystem $filesystem
     * @param CodegenLogger $codegenLogger
     * @param ModuleResource $moduleResource
     */
    public function __construct(
        Action\Context $context,
        DirectoryList $directoryList,
        ModuleGeneratorHelper $moduleGeneratorHelper,
        ControllerAndRouteGeneratorHelper $controllerAndRouteGeneratorHelper,
        Filesystem $filesystem,
        CodegenLogger $codegenLogger,
        ModuleResource $moduleResource
    )
    {
        parent::__construct($context, $codegenLogger);
        $this->logTime('CreateOrSelect action started.');

        $this->directoryList = $directoryList;
        $this->moduleGeneratorHelper = $moduleGeneratorHelper;
        $this->controllerAndRouteGeneratorHelper = $controllerAndRouteGeneratorHelper;
        $this->filesystem = $filesystem;
        $this->moduleResource = $moduleResource;
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

        $params = $this->getRequest()->getParams();

        try {
            if($params['destination'] === 'create'){
                $resultData = $this->createModule();
            }else if($params['destination'] === 'select'){
                $resultData = $this->selectModule();
            }
        }catch (\Exception $e){
            $result->setData(['success' => false, 'message' => $e->getMessage()]);
            return $result;
        }

        $this->logTime('CreateOrSelect action ended.');

        $result->setData($resultData);
        return $result;
    }

    /**
     * Creates new module in the app/code directory
     *
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Exception
     */
    private function createModule(){

        $result = [];

        $params = $this->getRequest()->getParams();

        // check if module exists
        if($this->moduleExists($params['module_name'])){

            throw new \Exception(sprintf(__('Module with name %s already exists.'), $params['module_name']));
        }

        $module_path = $this->controllerAndRouteGeneratorHelper->buildLocation($params['module_name']);
        if(!file_exists($module_path)){
            if(!mkdir($module_path, 0644, true)){
                throw new \Exception(__('Cannot create module\'s directory.'));
            }
        }

        $files = glob($module_path . '/*');
        if(isset($files[0])){
            throw new \Exception(__('Module\'s folder is not empty.'));
        }

        // validate module name
        $pattern = $this->moduleGeneratorHelper->getModuleNameRegEx();
        $pregMatchResult = preg_match($pattern, $params['module_name'], $matches);

        if($matches[1] === 'Magento'){
            throw new \Exception(__('Module name cannot contain "Magento" name as vendor, because it may conflict with magento modules.'));
        }

        if($pregMatchResult === 1){ // module name is invalid
            $result = $this->generateModule($params['module_name']);
            $this->enableModule();
            $this->saveInDatabase();
            $this->saveInSession($params['module_name']);
        }else if($pregMatchResult === 0){
            throw new \Exception(sprintf(__('%s is an invalid name for the module. Please choose another one that match the regular expression %s'), $params['module_name'], $pattern));
        }else if($pregMatchResult === false){
            throw new \Exception(__('Cannot validate module name.'));
        }

        return $result;
    }

    /**
     * Selects the module
     *
     * @throws \Exception
     */
    private function selectModule(){

        $params = $this->getRequest()->getParams();

        // check if module exists
        if(!$this->moduleExists($params['module_name'])){

            throw new \Exception(sprintf(__('Module with name %s doesn\'t exist.'), $params['module_name']));
        }

        $this->saveInSession($params['module_name']);

        $result = ['success' => true, 'message' => sprintf(__('Module %s was selected. All files will be generated in this module\'s directory'), $params['module_name'])];

        return $result;
    }

    /**
     * @param $module_name
     * @return bool
     * @throws \Exception
     */
    private function moduleExists($module_name){

        $config = require $this->directoryList->getPath(DirectoryList::APP) . '/etc/config.php';

        if(isset($config['modules'][$module_name])){
            return true;
        }

        return false;
    }

    /**
     * @param $module_name
     * @param $result
     * @return array
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Exception
     */
    private function generateModule($module_name){

        $filesToGenerate = $this->moduleGeneratorHelper->getFilesToGenerate();

        foreach($filesToGenerate as $file){
            $this->controllerAndRouteGeneratorHelper->copyFileToLocation(
                $file,
                $this->controllerAndRouteGeneratorHelper->buildLocation($module_name, $file),
                [
                    'setupVersion' => '0.1.0',
                    'componentName'=> $this->getRequest()->getParam('module_name')
                ]
            );
        }

        return ['success' => true];
    }

    /**
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    private function enableModule(){

        $module_name = $this->getRequest()->getParam('module_name');

        $configFilePath = $this->directoryList->getPath(DirectoryList::APP) . '/etc/config.php';
        $config = require $configFilePath;

        $config['modules'][$module_name] = 1;
        $this->moduleGeneratorHelper->regenerateConfigFile($config);
    }

    private function saveInSession($module_name){

        $this->_session->setCurrentlySelectedModule($module_name);
    }

    private function saveInDatabase(){

        $module_name = $this->getRequest()->getParam('module_name');
        $version = '0.1.0';

        $this->moduleResource->setDbVersion($module_name, $version);
        $this->moduleResource->setDataVersion($module_name, $version);
    }

    /**
     * @throws \Exception
     */
    protected function validateParams()
    {
        $params = $this->getRequest()->getParams();

        if (empty($params['module_name'])) {
            throw new \Exception(__('Module name cannot be empty when creating new module.'));
        }

        if (empty($params['destination'])) {
            throw new \Exception(__('Cannot determine whether to create a new module or select an existing one.'));
        }
    }
}