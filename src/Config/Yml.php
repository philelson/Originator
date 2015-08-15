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

use Doctrine\Instantiator\Exception\InvalidArgumentException;
use Pegasus\Application\Originator\Originator;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Yaml\Parser;

class Yml {

    const EVENT_CONFIG_VALIDATE = 'yml.config.validate.before';

    private $config             = null;

    private $requiredNodes      = null;

    private $dispatcher         = null;

    public function __construct() {
        $this->requiredNodes = array('magento_root', 'originator_root', 'originator_module_root');
    }

    public function setEventDispatched(EventDispatcher $dispatcher) {
        $this->dispatcher = $dispatcher;
    }

    public static function getConfigFileName() {
        return Originator::CONFIG_FILE;
    }

    public function addRequiredNodes($nodes) {
        if(false == is_array($nodes)) {
            throw new InvalidArgumentException('Additional nodes needs to be an array');
        }
        $this->requiredNodes = array_merge($nodes);
    }

    public function load() {
        $yaml = new Parser();
        if(false == file_exists(self::getConfigFileName())) {
            throw new InvalidConfigurationException('Configuration file not found: '.self::getConfigFileName());
        }
        $this->config = $yaml->parse(file_get_contents(self::getConfigFileName()));
        $this->_validateConfig();
    }

    private function _validateConfig() {

        $event = new YmlEvent($this);
        if(null != $this->dispatcher) {
            $this->dispatcher->dispatch(self::EVENT_CONFIG_VALIDATE, $event);
        }
    }
}