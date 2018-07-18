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
     * ModuleGenerator constructor.
     * @param Context $context
     * @param DirectoryList $directoryList
     * @param Session $session
     * @param ModuleListInterface $moduleList
     */
    public function __construct(
        Context $context,
        DirectoryList $directoryList,
        Session $session,
        ModuleListInterface $moduleList
    )
    {
        parent::__construct($context, $directoryList);

        $this->session = $session;
        $this->moduleList = $moduleList;
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
}