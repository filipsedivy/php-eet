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

namespace FilipSedivy\EET\Bridges\Nette;

use FilipSedivy;
use Nette;

class EetExtension extends Nette\DI\CompilerExtension
{
    protected $default = array(
        'certificate' => array(
            'file'     => null,
            'password' => null
        ),
        'service'     => 'playground'
    );

    public function loadConfiguration()
    {
        $builder = $this->getContainerBuilder();
        $this->validateConfig($this->default);

        $certificate = $builder->addDefinition($this->prefix('certificate'))
            ->setFactory('FilipSedivy\EET\Certificate', array(
                $this->config['certificate']['file'],
                $this->config['certificate']['password']
            ));

        $dispatcher = $builder->addDefinition($this->prefix('dispatcher'))
            ->setFactory('FilipSedivy\EET\Dispatcher', array(
                $certificate
            ));

        static $whitelist = ['playground', 'production'];
        if (in_array(strtolower($this->config['service']), $whitelist))
        {
            $dispatcher->addSetup('set' . $this->config['service'] . 'service');
        }
        else
        {
            $dispatcher->addSetup('setService', $this->default['service']);
        }
    }
}