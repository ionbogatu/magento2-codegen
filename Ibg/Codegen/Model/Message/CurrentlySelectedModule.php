<?php
/**
 * Created by PhpStorm.
 * User: Ion Bogatu
 * Date: 6/6/2018
 * Time: 5:51 PM
 */

namespace Ibg\Codegen\Model\Message;

use Magento\Framework\UrlInterface;

class CurrentlySelectedModule implements \Magento\Framework\Notification\MessageInterface
{
    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * CurrentlySelectedModule constructor.
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        UrlInterface $urlBuilder
    )
    {
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Retrieve unique message identity
     *
     * @return string
     */
    public function getIdentity()
    {
        return md5('CURRENTLY_SELECTED_MODULE');
    }

    /**
     * Check whether
     *
     * @return bool
     */
    public function isDisplayed()
    {
        return true;
    }

    /**
     * Retrieve message text
     *
     * @return string
     */
    public function getText()
    {
        $url = $this->urlBuilder->getUrl('adminhtml/integration');
        return __(
            'Test.',
            $url
        );
    }

    /**
     * Retrieve message severity
     *
     * @return int
     */
    public function getSeverity()
    {
        return self::SEVERITY_NOTICE;
    }
}