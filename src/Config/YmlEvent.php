<?php
/**
 * The MIT License (MIT)
 *
 * Copyright (c) 2015  Philip Elson <phil@pegasus-commerce.com>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.

 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * Date: 12/08/15
 * Time: 19:29
 *
 * PHP version 5.3+
 *
 * @category Pegasus_Tools
 * @package  Pegasus_Originator
 * @author   Philip Elson <phil@pegasus-commerce.com>
 * @license  MIT http://opensource.org/licenses/MIT
 * @link     http://pegasus-commerce.com
 */
namespace Pegasus\Application\Originator\Config;

use Symfony\Component\EventDispatcher\Event;

/**
 * Event for the config
 *
 * @category Pegasus_Tools
 * @package  Pegasus_Originator
 * @author   Philip Elson <phil@pegasus-commerce.com>
 * @license  MIT http://opensource.org/licenses/MIT
 * @link     http://pegasus-commerce.com 
*/
class YmlEvent extends Event
{
    /**
     * Class scope config
     *
     * @var Yml
     */
    protected $config;

    /**
     * Constructor sets the Yml object
     *
     * @param Yml $config Is the config object
     */
    public function __construct(Yml $config)
    {
        $this->config = $config;
    }

    /**
     * Allows overriding of the class scope config object
     *
     * @param Yml $config Is the config object
     *
     * @return $this
     */
    public function setConfig(Yml $config) 
    {
        $this->config = $config;
        return $this;
    }

    /**
     * This method returns the config
     *
     * @return Yml
     */
    public function getConfig()
    {
        return $this->config;
    }

}