<?php
/**
 * This file (NotYetImplemented.php) is belong to project "LearnSymfony".
 * Author: yqszxx
 * Created At: 15-4-5-下午3:37
 */

namespace yqszxx\AlipayBundle\Exception;


class NotYetImplementedException extends \RuntimeException
{
    const INTL_INSTALL_MESSAGE = 'Please wait for the newer updates!';

    /**
     * Constructor.
     *
     * @param string $message The exception message. A note to install the intl extension is appended to this string
     */
    public function __construct($message)
    {
        parent::__construct($message.' Please wait for the newer updates!');
    }
}