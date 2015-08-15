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
 * Date: 23/06/15
 * Time: 11:25
 *
 * PHP version 5.3+
 *
 * @category Pegasus_Tools
 * @package  Pegasus_Originator
 * @author   Philip Elson <phil@pegasus-commerce.com>
 * @license  MIT http://opensource.org/licenses/MIT
 * @link     http://pegasus-commerce.com
 */
namespace Pegasus\Application\Originator;

use Pegasus\Application\Originator\Config\Yml as OriginatorConfig;
use Pegasus\Application\Originator\Events\Basic as BasicEvent;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

/**
 * This class provides the core methods for VagrantTransient
 *
 * @category Pegasus_Utilities
 * @package  Originator
 * @author   Philip Elson <phil@pegasus-commerce.com>
 * @license  MIT http://opensource.org/licenses/MIT
 * @link     http://pegasus-commerce.com
 */
class Originator extends Command
{
    /**
     * This is the application version
     */
    const VERSION               = "0.1.0";

    /**
     * Application specific configuration file
     */
    const CONFIG_FILE           = 'originator.yml';

    /**
     * Module specific file cache
     */
    const CACHE_MODULE_FILE     = '.originator_file_cache';

    /**
     * Class scope config
     *
     * @var null
     */
    private $config = null;

    private $dispatcher = null;

    /**
     * Configures the application
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName("originator")
            ->setDescription("Simple Magento module synchronisation");
//            ->addOption(
//                'storage',
//                null,
//                InputOption::VALUE_OPTIONAL,
//                'Environment Location Storage',
//                $this->getDefaultFileName()
//            );
    }

    /**
     * This method returns the version of the application
     *
     * @return float
     */
    public function getVersion()
    {
        return self::VERSION;
    }

    /**
     * This method dispatches an event
     *
     * @param $name Is the name of the event being dispatched
     * @param array $data  Additional data - this class is always added as originator.
     * @param Event|null $event
     * @param EventDispatcherInterface|null $eventDispatcher for dependancy injection
     */
    private function _dispatchEvent($name, Event $event=null, $data=array(), EventDispatcherInterface $eventDispatcher=null) {
        /* Initialise the class dispatcher if one doesn't exist */
        if(null == $this->dispatcher) {
            $this->dispatcher = new EventDispatcher();
        }
        /* Use the class dispatcher if the parameter dispatcher is null */
        if(null == $eventDispatcher) {
            $eventDispatcher = $this->dispatcher;
        }
        /* Add the originator object to the event data by default */
        if(false == array_key_exists('originator', $data)) {
            $data['originator'] = $this;
        }
        /* Create the event if the parameter event is null */
        if(null == $event) {
            $event = new BasicEvent($data);
        }
        /* dispatch the event */
        $eventDispatcher->dispatch($name, $event);
    }

    /**
     * This is the command which executes the application
     *
     * @param InputInterface  $input  input interface for  command
     * @param OutputInterface $output output interface for command
     * 
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->loadOutputStyles($output);
        $this->initialise($input, $output);
        $this->beforeParse($input, $output);
        $this->parse($input, $output);
        $this->afterParse($input, $output);
        $this->cleanup($input, $output);
    }

    protected function initialise(InputInterface $input, OutputInterface $output) {
        $this->_dispatchEvent('originator.initialise.before');
        $this->config = new OriginatorConfig();
        $this->config->setEventDispatched($this->dispatcher);
        $this->config->load();
        $this->_setupMagentoRoot();
        $this->_dispatchEvent('originator.initialise.after');
    }

    protected function beforeParse(InputInterface $input, OutputInterface $output) {
        $this->_dispatchEvent('originator.beforeParse.before');
        //Logic
        $this->_dispatchEvent('originator.beforeParse.after');
    }

    protected function parse(InputInterface $input, OutputInterface $output) {
        $this->_dispatchEvent('originator.parse.before');
        //Logic
        $this->_dispatchEvent('originator.parse.after');
    }

    protected function afterParse(InputInterface $input, OutputInterface $output) {
        $this->_dispatchEvent('originator.afterParse.before');
        //Logic
        $this->_dispatchEvent('originator.afterParse.after');
    }

    protected function cleanup(InputInterface $input, OutputInterface $output) {
        $this->_dispatchEvent('originator.cleanup.before');
        //Logic
        $this->_dispatchEvent('originator.cleanup.after');
    }

    private function _setupMagentoRoot() {

    }

    /**
     * This method loads the command styles (warning|general|notice|fatal_error)
     *
     * @return void
     */
    public function loadOutputStyles(OutputInterface $output)
    {
        $styleOne   = array('bold');
        $styleTwo   = array('bold', 'underscore');
        $style      = new OutputFormatterStyle();
        $output->getFormatter()->setStyle('normal', $style);
        $style      = new OutputFormatterStyle('white', 'red', $styleOne);
        $output->getFormatter()->setStyle('warning', $style);
        $style      = new OutputFormatterStyle('white', 'green', $styleOne);
        $output->getFormatter()->setStyle('general', $style);
        $style      = new OutputFormatterStyle('white', 'blue', $styleOne);
        $output->getFormatter()->setStyle('notice', $style);
        $style      = new OutputFormatterStyle('white', 'red', $styleTwo);
        $output->getFormatter()->setStyle('fatal_error', $style);
    }

    /**
     * This method returns the path to the log file
     *
     * @return string
     */
    private function _getLogFileName()
    {
        return 'originator.log';
    }

    /**
     * This method prints a line to the terminal.
     *
     * @param string $message Is the message
     * @param string $type    Is the type of message (warning|notice|general)
     * @param bool   $out     Tells the method to print to the terminal or just log
     *
     * @return void
     */
    protected function printLn($message, $type='general', $out=true)
    {
        if (null == $message) {
            return;
        }
        if (null != $this->output) {
            $message = (null == $type) ? $message : "<{$type}>{$message}</{$type}>";
            if (true == $out) {
                $this->output->writeLn($message);
            }
        }
        $this->getLog()->addInfo($message, array('type' => $type));
    }

    /**
     * This method returns a singleton instance of the logger class.
     *
     * @return Logger
     */
    public function getLog()
    {
        if (null == $this->log) {
            $handler    = new StreamHandler($this->_getLogFileName(), Logger::INFO);
            $this->log  = new Logger('VagrantTransient');
            $this->log->pushHandler($handler);
        }
        return $this->log;
    }
}