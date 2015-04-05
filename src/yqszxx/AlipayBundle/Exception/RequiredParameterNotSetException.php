<?php
/**
 * This file (RequiredParameterNotSettedException.php) is belong to project "LearnSymfony".
 * Author: yqszxx
 * Created At: 15-4-5-下午4:08
 */

namespace yqszxx\AlipayBundle\Exception;


class RequiredParameterNotSetException extends \RuntimeException
{
    /**
     * Constructor.
     *
     * @param string $parameterName The exception message. A note to install the intl extension is appended to this string
     */
    public function __construct($parameterName)
    {
        parent::__construct('The parameter '.$parameterName.' has not been set yet. Please set all the required parameters.');
    }
}