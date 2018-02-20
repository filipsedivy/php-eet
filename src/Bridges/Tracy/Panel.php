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

    public function getTab()
    {
        return 'EET';
    }

    public function getPanel()
    {


        $result = '<h1>EET</h1>';

        $result .= '<div class="tracy-inner">
                Dispatcher
                
                <table>
                    <tr><th>Property</th><th>Value</th></tr>
                    <tr>
                        <td>FIK</td>
                        <td>' . $this->dump($this->dispatcher->getFik()). '</td>
                    </tr>
                    
                    <tr>
                        <td>BKP</td>
                        <td>' . $this->dump($this->dispatcher->getBkp()) . '</td>
                    </tr>
                    
                    <tr>
                        <td>PKP</td>
                        <td>' . $this->dump($this->dispatcher->getPkp()) . '</td>
                    </tr>
                    
                     <tr>
                        <td>Warnings</td>
                        <td>' . $this->dump($this->dispatcher->getWarnings()) . '</td>
                    </tr>
                    
                    <tr>
                        <td>Dispatcher</td>
                        <td>' . $this->dump($this->dispatcher) . '</td>
                    </tr>
                </table>';

        if (!is_null($lastReceipt = $this->dispatcher->getLastReceipt()))
        {
            $result .= 'Receipt
                <table>
                    <tr><th>Property</th><th>Value</th></tr>';

            foreach ($lastReceipt as $item => $value)
            {
                $result .= '<tr><td>' . $item . '</td><td>' . $this->dump($value) . '</td></tr>';
            }
            $result .= '</table>';

        }

        $result .= '</div>';
        return $result;
    }

    private function dump($var)
    {
        return call_user_func('\Tracy\Debugger::dump', $var, true);
    }
}