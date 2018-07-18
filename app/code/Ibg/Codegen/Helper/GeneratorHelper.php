<?php
/**
 * Created by PhpStorm.
 * User: Ion Bogatu
 * Date: 6/11/2018
 * Time: 3:44 PM
 */

namespace Ibg\Codegen\Helper;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

abstract class GeneratorHelper extends AbstractHelper
{
    /**
     * @var DirectoryList
     */
    protected $directoryList;

    /**
     * GeneratorHelper constructor.
     * @param Context $context
     * @param DirectoryList $directoryList
     */
    public function __construct(
        Context $context,
        DirectoryList $directoryList
    )
    {
        parent::__construct($context);

        $this->directoryList = $directoryList;
    }

    /**
     * @param $module_name
     * @param string $filepath
     * @return string
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function buildLocation($module_name, $filepath = ''){

        $codegenModulePath = $this->getCodegenModulePath();
        $module_path = str_replace('_', '/', $module_name);
        $relativePath = str_replace(
            $this->directoryList->getPath(DirectoryList::APP) . '/code/' . $codegenModulePath . '/_Files/ModuleEthalone',
            '',
            $filepath
        );

        if(!empty($relativePath)){
            $relativePath = pathinfo($relativePath)['dirname'];
        }

        return $this->directoryList->getPath(DirectoryList::APP) . '/code/' . $module_path . $relativePath;
    }

    /**
     * @return mixed
     */
    public function getCodegenModulePath(){

        return str_replace('_', '/', $this->getCodegenModuleName());
    }

    /**
     * @return string
     */
    public function getCodegenModuleName(){

        return $this->_getModuleName();
    }

    /**
     * @param $file
     * @param $location
     * @return bool
     * @throws \Exception
     */
    public function copyFileToLocation($file, $location, $bindVariables){

        $pathinfo = pathinfo($file);
        $newFile = $location . '/' . $pathinfo['filename'];
        $newFileDirectory = dirname($newFile);

        if(!file_exists($newFileDirectory)){
            mkdir($newFileDirectory, 0755, true);
        }

        if(!fopen($newFile, 'w+')){
            throw new \Exception(__('Cannot create the file'));
        }

        $fileContent = file_get_contents($file);
        $this->bindVariables($fileContent, $bindVariables);
        file_put_contents($newFile, $fileContent);

        return true;
    }

    public function bindVariables(&$fileContent, $bindVariables){

        $variablesPattern = $this->getBindVariablesPattern();

        preg_match_all($variablesPattern, $fileContent,$matches);

        foreach($matches[0] as $match){
            $strippedVariableName = trim($match, '{}');
            $fileContent = str_replace($match, $bindVariables[$strippedVariableName], $fileContent);
        }
    }

    private function getBindVariablesPattern()
    {
        return '/\{\{[a-zA-Z]+[a-zA-Z0-9]*\}\}/m';
    }

    /**
     * @return array
     */
    abstract public function getFilesToGenerate();
}