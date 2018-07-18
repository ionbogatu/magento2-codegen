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

class ControllerAndRouteGenerator extends GeneratorHelper
{
    /**
     * @var ModuleGenerator
     */
    private $moduleGeneratorHelper;

    /**
     * ControllerAndRouteGenerator constructor.
     * @param Context $context
     * @param DirectoryList $directoryList
     * @param ModuleGenerator $moduleGeneratorHelper
     */
    public function __construct(
        Context $context,
        DirectoryList $directoryList,
        ModuleGeneratorHelper $moduleGeneratorHelper
    )
    {
        parent::__construct($context, $directoryList);

        $this->moduleGeneratorHelper = $moduleGeneratorHelper;
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
        $codegenModulePath = $this->getCodegenModulePath();

        $filesToGenerate = [
            $this->directoryList->getPath(DirectoryList::APP) . '/code/' . $codegenModulePath . '/_Files/ModuleEthalone/etc/module.xml.sample'
        ];

        if(!$this->moduleContainsRoutesXmlFile()){
            $area = $this->_request->getParam('area') !== 'frontend' ? '/' . $this->_request->getParam('area') : '';
            $filesToGenerate[] = $this->directoryList->getPath(DirectoryList::APP) . '/code/' . $codegenModulePath . '/_Files/ModuleEthalone/etc/' . $area . 'routes.xml.sample';
        }

        return $filesToGenerate;
    }

    /**
     * return bool
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    private function moduleContainsRoutesXmlFile(){

        $result = false;

        $currentlySelectedModule = $this->moduleGeneratorHelper->getCurrentlySelectedModule();
        $area = $this->_request->getParam('area') !== 'frontend' ? '/' . $this->_request->getParam('area') : '';
        $path = $this->directoryList->getPath(DirectoryList::APP) . '/code/' . $currentlySelectedModule . '/etc/' . $area . 'module.xml.sample';
        if(file_exists($path)){
            $result = true;
        }

        return $result;
    }
}