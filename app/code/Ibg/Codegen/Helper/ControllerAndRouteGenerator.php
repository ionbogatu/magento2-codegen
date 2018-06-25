<?php
/**
 * Created by PhpStorm.
 * User: Ion Bogatu
 * Date: 6/7/2018
 * Time: 9:11 PM
 */

namespace Ibg\Codegen\Helper;

class ControllerAndRouteGenerator extends GeneratorHelper
{
    public function getMenuItemHtml()
    {
        return '<li data-ui-id="menu-ibg-codegen-controller-and-route" class="item-controller-and-route level-2" role="menu-item">
                    <a href="#" onclick="return false;" class=""><span>Controller and Router</span></a>
                </li>';
    }

    public function getFrontNameRegEx()
    {
        return '/[a-z]+[_A-Z]?[a-z]*/';
    }
}