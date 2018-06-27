<?php
/**
 * Created by PhpStorm.
 * User: Ion Bogatu
 * Date: 6/16/2018
 * Time: 11:13 AM
 */

namespace Ibg\Codegen\Plugin\View\Element;

use DOMDocument;
use Magento\Framework\DomDocument\DomDocumentFactory;
use Ibg\Codegen\Helper\ModuleGenerator as ModuleGeneratorHelper;
use Ibg\Codegen\Helper\ControllerAndRouteGenerator as ControllerAndRouteGeneratorHelper;
use Ibg\Codegen\Helper\BlockGenerator as BlockGeneratorHelper;
use Ibg\Codegen\Helper\HelperGenerator as HelperGeneratorHelper;
use Magento\Framework\View\Element\Context;

class AbstractBlock extends \Magento\Framework\View\Element\AbstractBlock
{
    /**
     * @var ModuleGeneratorHelper
     */
    private $moduleGeneratorHelper;
    /**
     * @var DomDocumentFactory
     */
    private $domDocumentFactory;
    /**
     * @var ControllerAndRouteGeneratorHelper
     */
    private $controllerAndRouteGeneratorHelper;
    /**
     * @var BlockGeneratorHelper
     */
    private $blockGeneratorHelper;
    /**
     * @var HelperGeneratorHelper
     */
    private $helperGeneratorHelper;

    /**
     * AbstractBlock constructor.
     * @param Context $context
     * @param ModuleGeneratorHelper $moduleGeneratorHelper
     * @param ControllerAndRouteGeneratorHelper $controllerAndRouteGeneratorHelper
     * @param BlockGeneratorHelper $blockGeneratorHelper
     * @param HelperGeneratorHelper $helperGeneratorHelper
     * @param DomDocumentFactory $domDocumentFactory
     * @param array $data
     */

    public function __construct(
        Context $context,
        ModuleGeneratorHelper $moduleGeneratorHelper,
        ControllerAndRouteGeneratorHelper $controllerAndRouteGeneratorHelper,
        BlockGeneratorHelper $blockGeneratorHelper,
        HelperGeneratorHelper $helperGeneratorHelper,
        DomDocumentFactory $domDocumentFactory,
        array $data = []
    )
    {
        parent::__construct($context, $data);

        $this->moduleGeneratorHelper = $moduleGeneratorHelper;
        $this->controllerAndRouteGeneratorHelper = $controllerAndRouteGeneratorHelper;
        $this->blockGeneratorHelper = $blockGeneratorHelper;
        $this->helperGeneratorHelper = $helperGeneratorHelper;
        $this->domDocumentFactory = $domDocumentFactory;
    }

    public function afterToHtml(\Magento\Framework\View\Element\AbstractBlock $subject, $result)
    {
        if($subject instanceof \Magento\Backend\Block\Menu){

            if($this->moduleGeneratorHelper->getCurrentlySelectedModule()){

                // parse html result
                $dom = new DOMDocument();
                @$dom->loadHTML($result);

                $zendDom = new \Zend_Dom_Query($dom);
                // get nav item to render
                $nav = $zendDom->query('nav.admin__menu')->current();

                // get item to append child
                $codegenBackendSubmenu = $zendDom->query('li[data-ui-id="menu-ibg-codegen-backend"] .submenu ul[role="menu"]')->current();

                // add new node
                $listItemHtml = $this->controllerAndRouteGeneratorHelper->getMenuItemHtml();
                $newDom = new \DOMDocument();
                @$newDom->loadHTML($listItemHtml);
                $newZendDom = new \Zend_Dom_Query($newDom);
                $newNode = $newZendDom->query('li[data-ui-id="menu-ibg-codegen-controller-and-route"]')->current();
                $newNode = $dom->importNode($newNode, true);
                $codegenBackendSubmenu->appendChild($newNode);

                // add new node
                $listItemHtml = $this->blockGeneratorHelper->getMenuItemHtml();
                $newDom = new \DOMDocument();
                @$newDom->loadHTML($listItemHtml);
                $newZendDom = new \Zend_Dom_Query($newDom);
                $newNode = $newZendDom->query('li[data-ui-id="menu-ibg-codegen-block"]')->current();
                $newNode = $dom->importNode($newNode, true);
                $codegenBackendSubmenu->appendChild($newNode);

                // add new node
                $listItemHtml = $this->helperGeneratorHelper->getMenuItemHtml();
                $newDom = new \DOMDocument();
                @$newDom->loadHTML($listItemHtml);
                $newZendDom = new \Zend_Dom_Query($newDom);
                $newNode = $newZendDom->query('li[data-ui-id="menu-ibg-codegen-helper"]')->current();
                $newNode = $dom->importNode($newNode, true);
                $codegenBackendSubmenu->appendChild($newNode);

                // build result string
                $result = $dom->saveHTML($nav);

                $this->_saveCache($result);
            }
        }

        return $result;
    }
}