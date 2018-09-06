<?php
/**
 * Created by PhpStorm.
 * User: Ion Bogatu
 * Date: 6/7/2018
 * Time: 9:11 PM
 */

namespace Ibg\Codegen\Helper;

use Magento\Backend\Model\Session;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Module\ModuleListInterface;
use Magento\Framework\Module\ModuleResource;

class ModuleGenerator extends GeneratorHelper
{
    /**
     * @var Session
     */
    private $session;
    /**
     * @var ModuleListInterface
     */
    private $moduleList;
    /**
     * @var ModuleResource
     */
    private $moduleResource;

    /**
     * ModuleGenerator constructor.
     * @param Context $context
     * @param DirectoryList $directoryList
     * @param Session $session
     * @param ModuleListInterface $moduleList
     * @param ModuleResource $moduleResource
     */
    public function __construct(
        Context $context,
        DirectoryList $directoryList,
        Session $session,
        ModuleListInterface $moduleList,
        ModuleResource $moduleResource
    )
    {
        parent::__construct($context, $directoryList);

        $this->session = $session;
        $this->moduleList = $moduleList;
        $this->moduleResource = $moduleResource;
    }

    /**
     * @return mixed
     */
    public function getCurrentlySelectedModule()
    {
        return $this->session->getCurrentlySelectedModule();
    }

    public function getModuleNameRegEx(){

        return '/([A-Z]+[a-z]*)_[A-Za-z]+/';
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function getFilesToGenerate(){

        $codegenModulePath = $this->getCodegenModulePath();

        return [
            $this->directoryList->getPath(DirectoryList::APP) . '/code/' . $codegenModulePath . '/_Files/ModuleEthalone/etc/module.xml.sample',
            $this->directoryList->getPath(DirectoryList::APP) . '/code/' . $codegenModulePath . '/_Files/ModuleEthalone/registration.php.sample'
        ];
    }

    /**
     * @param $config
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function regenerateConfigFile($config)
    {
        $configFilePath = $this->directoryList->getPath(DirectoryList::APP) . '/etc/config.php';

        $fd = fopen($configFilePath, 'w');

        fwrite($fd, "<?php\n");
        fwrite($fd, "return [\n");
        fwrite($fd, "  'modules' => [\n");

        $arrayKeys = array_keys($config['modules']);
        $lastKey = end($arrayKeys);

        foreach($config['modules'] as $key => $module){
            if ($lastKey === $key){
                fwrite($fd, "    '" . $key . "' => " . $module . "\n");
            }else{
                fwrite($fd, "    '" . $key . "' => $module,\n");
            }
        }

        fwrite($fd, "  ]\n");
        fwrite($fd, "];");

        fclose($fd);
    }



    /**
     * @return string[]
     */
    public function getModuleList()
    {
        $result = $this->moduleList->getNames();
        sort($result, SORT_STRING);

        return $result;
    }

    /**
     * Creates new module in the app/code directory
     *
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Exception
     */
    public function createModule(){

        $result = [];

        $params = $this->_request->getParams();

        // check if module exists
        if($this->moduleExists($params['module_name'])){

            throw new \Exception(sprintf(__('Module with name %s already exists.'), $params['module_name']));
        }

        $module_path = $this->buildLocation($params['module_name']);
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
        $pattern = $this->getModuleNameRegEx();
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
    public function selectModule(){

        $params = $this->_request->getParams();

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
     * @return array
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Exception
     */
    private function generateModule($module_name){

        $filesToGenerate = $this->getFilesToGenerate();

        foreach($filesToGenerate as $file){
            $this->copyFileToLocation(
                $file,
                $this->buildLocation($module_name, $file),
                [
                    'setupVersion' => '0.1.0',
                    'componentName'=> $this->_request->getParam('module_name')
                ]
            );
        }

        return ['success' => true];
    }

    /**
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    private function enableModule(){

        $module_name = $this->_request->getParam('module_name');

        $configFilePath = $this->directoryList->getPath(DirectoryList::APP) . '/etc/config.php';
        $config = require $configFilePath;

        $config['modules'][$module_name] = 1;
        $this->regenerateConfigFile($config);
    }

    private function saveInSession($module_name){

        $this->session->setCurrentlySelectedModule($module_name);
    }

    private function saveInDatabase(){

        $module_name = $this->_request->getParam('module_name');
        $version = '0.1.0';

        $this->moduleResource->setDbVersion($module_name, $version);
        $this->moduleResource->setDataVersion($module_name, $version);
    }
}