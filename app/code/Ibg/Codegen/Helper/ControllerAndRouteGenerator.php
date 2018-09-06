<?php
/**
 * Created by PhpStorm.
 * User: Ion Bogatu
 * Date: 6/7/2018
 * Time: 9:11 PM
 */

namespace Ibg\Codegen\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Ibg\Codegen\Helper\ModuleGenerator as ModuleGeneratorHelper;
use Magento\Framework\App\Route\ConfigInterface as RouteConfig;
use Magento\Framework\App\Router\ActionList;

class ControllerAndRouteGenerator extends GeneratorHelper
{
    /**
     * @var ModuleGenerator
     */
    private $moduleGeneratorHelper;
    /**
     * @var RouteConfig
     */
    private $routeConfig;
    /**
     * @var ActionList
     */
    private $actionList;

    /**
     * ControllerAndRouteGenerator constructor.
     * @param Context $context
     * @param DirectoryList $directoryList
     * @param ModuleGenerator $moduleGeneratorHelper
     * @param RouteConfig $routeConfig
     * @param ActionList $actionList
     */
    public function __construct(
        Context $context,
        DirectoryList $directoryList,
        ModuleGeneratorHelper $moduleGeneratorHelper,
        RouteConfig $routeConfig,
        ActionList $actionList
    )
    {
        parent::__construct($context, $directoryList);

        $this->moduleGeneratorHelper = $moduleGeneratorHelper;
        $this->routeConfig = $routeConfig;
        $this->actionList = $actionList;
    }

    public function getFrontNameRegEx()
    {
        return '/[a-z]+[_A-Z]?[a-z]*/';
    }

    public function getControllerNameRegEx()
    {
        return '/[A-Z]+[A-Za-z]*/';
    }

    public function getActionNameRegEx()
    {
        return '/[a-z]+[A-Za-z]*/';
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function getFilesToGenerate()
    {
        $params = $this->_request->getParams();

        $codegenModulePath = $this->getCodegenModulePath();

        $filesToGenerate = [];

        $area = $this->_request->getParam('area') === 'adminhtml' ? '/Adminhtml' : '';
        $filesToGenerate[] = [
            'copy_path' => $this->directoryList->getPath(DirectoryList::APP) . '/code/' . $codegenModulePath . '/_Files/ModuleEthalone/Controller' . $area . '/Controller/Action.php.sample',
            'paste_path' => $this->directoryList->getPath(DirectoryList::APP) . '/code/' . $codegenModulePath . '/_Files/ModuleEthalone/Controller' . $area . '/' . $params['controller_name'] .'/' . $params['action_name'] . '.php.sample'
        ];

        // generate routes file
        $routeFile = $this->generateRouteFile();
        if(!empty($routeFile)){
            // $filesToGenerate[] = $this->directoryList->getPath(DirectoryList::APP) . '/code/' . $codegenModulePath . '/_Files/ModuleEthalone/etc/' . $params['area'] . '/routes.xml.sample';
            $filesToGenerate[] = $routeFile;
        }

        return $filesToGenerate;
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    private function generateRouteFile(){

        $params = $this->_request->getParams();

        $codegenModulePath = $this->getCodegenModulePath();

        $result = $this->directoryList->getPath(DirectoryList::APP) . '/code/' . $codegenModulePath . '/_Files/ModuleEthalone/etc/' . $params['area'] . '/routes.xml.sample';

        $selectedModulePath = str_replace('_', '/', $params['module_name']);
        $routeFile = $this->directoryList->getPath(DirectoryList::APP) . '/code/' . $selectedModulePath . '/etc/' . $params['area'] . '/routes.xml';
        if(file_exists($routeFile)){
            $routeFileContent = simplexml_load_string(file_get_contents($routeFile));

            if($routeFileContent->xpath('/config/router/route[@id="' . $params['front_name'] . '"]')){
                $route = $routeFileContent->xpath('/config/router/route[@id="' . $params['front_name'] . '"]')[0];
            }elseif($routeFileContent->xpath('/config/router/route[@frontName="' . $params['front_name'] . '"]')){
                $route = $routeFileContent->xpath('/config/router/route[@frontName="' . $params['front_name'] . '"]')[0];
            }else{
                $router = $routeFileContent->xpath('/config/router')[0];
                $route = $router->addChild('route');
                $route->addAttribute('id', $params['front_name']);
                $route->addAttribute('front_name', $params['front_name']);
            }

            if(empty($route->xpath('module[@name="' . $params['module_name'] . '"]'))){
                $module = $route->addChild('module');
                $module->addAttribute('name', $params['module_name']);

                $dom = new \DOMDocument("1.0");
                $dom->preserveWhiteSpace = false;
                $dom->formatOutput = true;
                $dom->loadXML($routeFileContent->asXML());

                $routeFileContent = str_replace("  ", "    ", $dom->saveXML());
                file_put_contents($routeFile, $routeFileContent);
            }

            return "";
        }

        return $result;
    }
}
