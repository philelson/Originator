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

/**
 * Class which loads the config
 *
 * @category Pegasus_Tools
 * @package  Pegasus_Originator
 * @author   Philip Elson <phil@pegasus-commerce.com>
 * @license  MIT http://opensource.org/licenses/MIT
 * @link     http://pegasus-commerce.com
 */
class Yml
{

    const EVENT_CONFIG_VALIDATE_BEFORE  = 'yml.config.validate.before';

    private $_config                     = null;

    private $_requiredNodes              = null;

    private $_dispatcher                 = null;

    private $_configFileName             = null;

    /**
     * Constructor method.
     * This method sets the default required nodes.
     */
    public function __construct() 
    {
        $this->_requiredNodes = array('magento_root');
    }

    /**
     * This method sets the Yml event dispatcher
     *
     * @param EventDispatcher $dispatcher Is the event dispatcher
     *
     * @return $this;
     */
    public function setEventDispatched(EventDispatcher $dispatcher) 
    {
        $this->_dispatcher = $dispatcher;
        return $this;
    }

    /**
     * This method sets the config file name, must be set before
     * load is called.
     *
     * @param string $configName Is the name of the config file
     * 
     * @return $this
     */
    public function setConfigFileName($configName)
    {
        $this->_configFileName = $configName;
        return $this;
    }

    /**
     * This method adds required nodes to the config.
     * Must be called before load.
     *
     * @param $nodes Node array to be added
     *
     * @return $this
     */
    public function addRequiredNodes($nodes) 
    {
        if(false == is_array($nodes)) {
            throw new InvalidArgumentException('Additional nodes needs to be an array');
        }
        $this->_requiredNodes = array_merge($nodes);
        return $this;
    }

    /**
     * This method parses the config data
     *
     * @param Parser|null $parser Is the parser
     *
     * @throws InvalidConfigurationException    If config can not be loaded
     * @throws \Exception If the config name has not been specified
     *
     * @return $this;
     */
    public function load(Parser $parser=null) 
    {
        if (null == $this->_configFileName) {
            throw new \Exception("Config file name not set!");
        }
        if (null == $parser) {
            $parser = new Parser();
        }
        if (false == file_exists(self::getConfigFileName())) {
            throw new InvalidConfigurationException('Configuration file not found: '.self::getConfigFileName());
        }
        $this->_config = $parser->parse(file_get_contents(self::getConfigFileName()));
        $this->_validateConfig();
        return $this;
    }

    /**
     * This method validates the config.
     * This method dispatches the EVENT_CONFIG_VALIDATE_BEFORE allowing
     * other parts of the system to validate the config.
     *
     * @param Event|null $event Is the event object
     *
     * @return $this;
     */
    private function _validateConfig(Event $event=null) 
    {
        if (null == $event) {
            $event = new YmlEvent($this);
        }
        if (null != $this->_dispatcher) {
            $this->_dispatcher->dispatch(self::EVENT_CONFIG_VALIDATE_BEFORE, $event);
        }
        return $this;
    }
}