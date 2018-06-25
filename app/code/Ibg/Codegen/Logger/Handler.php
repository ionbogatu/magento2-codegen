<?php
/**
 * SquareUp
 *
 * Handler Log
 *
 * @category    Squareup
 * @package     Squareup_Omni
 * @copyright   2018
 * @author      SquareUp
 */

namespace Ibg\Codegen\Logger;

use Magento\Framework\Logger\Handler\Base;

/**
 * Class Handler
 */
class Handler extends Base
{
    /**
     * Logging level
     *
     * @var int
     */
    protected $loggerType = Logger::INFO;

    /**
     * File name
     *
     * @var string
     */
    protected $fileName = '/var/log/codegen.log';
}