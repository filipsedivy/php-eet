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
 * @author Filip Sedivy <mail@filipsedivy.cz>
 */

namespace FilipSedivy\EET;

use FilipSedivy\EET\Exceptions\ClientException;

/**
 * Parsování PKCS#12 a uchování X.509 certifikátu
 *
 * @author Filip Šedivý <mail@filipsedivy.cz>
 * @version 1.0.1
*/
class Certificate
{
    private $pkey;

    private $cert;

    public function __construct($certificate, $password)
    {
        if(!file_exists($certificate)){
            throw new ClientException("Certifikat nebyl nalezen");
        }

        $certs = [];
        $pkcs12 = file_get_contents($certificate);

        if (!extension_loaded('openssl') || !function_exists('openssl_pkcs12_read')) {
            throw new ClientException("Rozsireni OpenSSL neni dostupne.");
        }

        $openSSL = openssl_pkcs12_read($pkcs12, $certs, $password);
        if(!$openSSL)
        {
            throw new ClientException("Certifikat se nepodarilo vyexportovat.");
        }

        $this->pkey = $certs['pkey'];
        $this->cert = $certs['cert'];
    }

    public function getPrivateKey(){
        return $this->pkey;
    }

    public function getCert(){
        return $this->cert;
    }
}
