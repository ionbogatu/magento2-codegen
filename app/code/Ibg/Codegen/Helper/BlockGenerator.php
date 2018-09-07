<?php
/**
 * Created by PhpStorm.
 * User: Ion Bogatu
 * Date: 6/7/2018
 * Time: 9:11 PM
 */

namespace Ibg\Codegen\Helper;

use Magento\Framework\App\Filesystem\DirectoryList;

class BlockGenerator extends GeneratorHelper
{
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
            'copy_path' => $this->directoryList->getPath(DirectoryList::APP) . '/code/' . $codegenModulePath . '/_Files/ModuleEthalone/Block' . $area . '/Block.php.sample',
            'paste_path' => $this->directoryList->getPath(DirectoryList::APP) . '/code/' . $codegenModulePath . '/_Files/ModuleEthalone/Block' . $area . '/' . $params['block_name'] . '.php.sample'
        ];

        return $filesToGenerate;
    }

    public function getBlockNameRegEx()
    {
        return '/[A-Z][\\a-zA-Z]*/';
    }
}
