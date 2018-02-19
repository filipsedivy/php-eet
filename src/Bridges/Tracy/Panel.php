<?php
/**
 * This file is part of the PHP-EET package.
 *
 * (c) Filip Sedivy <mail@filipsedivy.cz>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license MIT
 * @author  Filip Sedivy <mail@filipsedivy.cz>
 */


namespace FilipSedivy\EET\Bridges\Tracy;

use FilipSedivy\EET\Dispatcher;
use Tracy\Debugger;
use Tracy\Dumper;
use Tracy\IBarPanel;

class Panel implements IBarPanel
{
    /** @var Dispatcher $dispatcher */
    private $dispatcher;

    public function register(Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;

        Debugger::getBar()->addPanel($this);
    }

    /**
     * Renders HTML code for custom tab.
     * @return string
     */
    function getTab()
    {
        return 'EET';
    }

    /**
     * Renders HTML code for custom panel.
     * @return string
     */
    function getPanel()
    {
        $result = '<h1>EET</h1>';

        $result .= '<div class="tracy-inner">
                Dispatcher
                
                <table>
                    <tr><th>Property</th><th>Value</th></tr>
                    <tr>
                        <td>FIK</td>
                        <td>' . Debugger::dump($this->dispatcher->getFik(), true) . '</td>
                    </tr>
                    
                    <tr>
                        <td>BKP</td>
                        <td>' . Debugger::dump($this->dispatcher->getBkp(), true) . '</td>
                    </tr>
                    
                    <tr>
                        <td>PKP</td>
                        <td>' . Debugger::dump($this->dispatcher->getPkp(), true) . '</td>
                    </tr>
                    
                     <tr>
                        <td>Warnings</td>
                        <td>' . Debugger::dump($this->dispatcher->getWarnings(), true) . '</td>
                    </tr>
                    
                    <tr>
                        <td>Dispatcher</td>
                        <td>' . Debugger::dump($this->dispatcher, true) . '</td>
                    </tr>
                </table>

                Receipt
                <table>
                    <tr><th>Property</th><th>Value</th></tr>';
        foreach ($this->dispatcher->getLastReceipt() as $item => $value)
        {
            $dump = \Tracy\Debugger::dump($value, true);
            $result .= '<tr><td>' . $item . '</td><td>' . $dump . '</td></tr>';
        }

        $result .= '</table>';

        $result .= '</div>';
        return $result;
    }
}