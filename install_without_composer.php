<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

define("showError", true);
function showInfo($text)
{
  if(showError == false){ return; }
  echo sprintf("%s <br>", $text);
}

$needClass = array('ZipArchive');

// Závislosti
$dependency = array(
  "PHP-EET"     => "http://github.com/filipsedivy/PHP-EET/zipball/master/",
  "WSE-PHP"     => "http://github.com/robrichards/wse-php/zipball/master/",
  "XMLSecLibs"  => "https://github.com/robrichards/xmlseclibs/zipball/master"
);

// Kontrola existence tříd
foreach($needClass as $class)
{
  if(!class_exists($class))
  {
    throw new Exception("Class {$class} not exists");
  }
}

// Stažení závislostí
foreach($dependency as $name => $url)
{
  showInfo("Kontrola závislosti s názvem ".$name);
  if(file_exists(__DIR__."/{$name}.zip"))
  {
    // Soubor existuje - smazat
    showInfo("Existující závislost s názvem ".$name." byla smazána");
    unlink(__DIR__."/{$name}.zip");
  }

  showInfo("Probíhá stahování závislosti s názvem ".$name);
  copy($url, __DIR__."/{$name}.zip");
  showInfo("Závislost ".$name." byla stažena");


  // V případě neexistence složky EETLib se vytvoří
  if(!file_exists(__DIR__."/EETLib") || !is_dir(__DIR__."/EETLib"))
  {
    showInfo("Složka EETLib neexistuje a byla vytvořena");
    mkdir(__DIR__."/EETLib", 0777);
  }


  // Do této složky se rozzipují soubory
  $ZipObject = new ZipArchive;
  if($ZipObject->open(__DIR__."/{$name}.zip") === TRUE){
    showInfo("Byl otevřen ZIP s názvem ".$name);
    $ZipObject->extractTo(__DIR__."/EETLib");
    $ZipObject->close();
    showInfo("Rozzipování ZIP souboru s názvem ".$name." bylo dokončeno");
  }else{
    showInfo("Nelze otevřít ZIP s názvem ".$name);
  }

  showInfo("Byl odstraněn ZIP s názvem ".$name);
  unlink(__DIR__."/{$name}.zip");
}

// Vytvoření autoloaderu
ob_start(); ?>
function EETLib_Autoloader($class)
{
  // Mapování složek
  $path = array(
    "PHP-EET"     => basename(glob(__DIR__."/filipsedivy-PHP-EET*")[0]),
    "WSE-PHP"     => basename(glob(__DIR__."/robrichards-wse-php*")[0]),
    "XMLSecLibs"  => basename(glob(__DIR__."/robrichards-xmlseclibs*")[0])
  );

  // Mapování objektů
  $map = array(
    "FilipSedivy\\EET\\Certificate"     => $path["PHP-EET"] . "/src/Certificate.php",
    "FilipSedivy\\EET\\Dispatcher"      => $path["PHP-EET"] . "/src/Dispatcher.php",
    "FilipSedivy\\EET\\Receipt"         =>  $path["PHP-EET"] . "/src/Receipt.php",
    "FilipSedivy\\EET\\SoapClient"      => $path["PHP-EET"] . "/src/SoapClient.php",

    "FilipSedivy\\EET\\Utils\\UUID"     => $path["PHP-EET"] . "/src/Utils/UUID.php",
    "FilipSedivy\\EET\\Utils\\Format"   => $path["PHP-EET"] . "/src/Utils/Format.php",

    "FilipSedivy\\EET\\Exceptions\\CertificateException"  => $path["PHP-EET"] . "/src/Exceptions/CertificateException.php",
    "FilipSedivy\\EET\\Exceptions\\ClientException"       => $path["PHP-EET"] . "/src/Exceptions/ClientException.php",
    "FilipSedivy\\EET\\Exceptions\\EetException"          => $path["PHP-EET"] . "/src/Exceptions/EetException.php",
    "FilipSedivy\\EET\\Exceptions\\RequirementsException" => $path["PHP-EET"] . "/src/Exceptions/RequirementsException.php",
    "FilipSedivy\\EET\\Exceptions\\ServerException"       => $path["PHP-EET"] . "/src/Exceptions/ServerException.php",


    "RobRichards\\XMLSecLibs\\XMLSecurityKey"   => $path["XMLSecLibs"] . "/src/XMLSecurityKey.php",
    "RobRichards\\XMLSecLibs\\XMLSecurityDSig"  => $path["XMLSecLibs"] . "/src/XMLSecurityDSig.php",
    "RobRichards\\XMLSecLibs\\XMLSecEnc"        => $path["XMLSecLibs"] . "/src/XMLSecEnc.php",

    "RobRichards\\WsePhp\\WSSESoap"         => $path["WSE-PHP"] . "/src/WSSESoap.php",
    "RobRichards\\WsePhp\\WSASoap"          => $path["WSE-PHP"] . "/src/WSASoap.php",
    "RobRichards\\WsePhp\\WSSESoapServer"   => $path["WSE-PHP"] . "/src/WSSESoapServer.php",
  );

  if(isset($map[$class]) && file_exists(__DIR__."/".$map[$class]))
  {
    require_once __DIR__."/".$map[$class];
  }
}

spl_autoload_register("EETLib_Autoloader");
<?php
$autoloader = ob_get_clean();

// Ukázka EET knihovny
ob_start(); ?>
require_once __DIR__."/EETLib/Autoloader.php";

use FilipSedivy\EET\Dispatcher;
use FilipSedivy\EET\Receipt;
use FilipSedivy\EET\Utils\UUID;
use FilipSedivy\EET\Certificate;

// Cesta k testovacímu certifikátu
$certExample = __DIR__."/EET_CA1_Playground-CZ00000019.p12";
$certificate = new Certificate($certExample, 'eet');

$dispatcher = new Dispatcher($certificate);
$dispatcher->setPlaygroundService();

$uuid = UUID::v4();

$r = new Receipt;
$r->uuid_zpravy = $uuid;
$r->id_provoz = '11';
$r->id_pokl = 'IP105';
$r->dic_popl = 'CZ1212121218';
$r->porad_cis = '1';
$r->dat_trzby = new \DateTime();
$r->celk_trzba = 500;

echo '<h2>---REQUEST---</h2>';
echo "<pre>";

try {

    $dispatcher->send($r);

    // Tržba byla úspěšně odeslána
    echo sprintf("FIK: %s <br>", $dispatcher->getFik());
    echo sprintf("BKP: %s <br>", $dispatcher->getBkp());

}catch(\FilipSedivy\EET\Exceptions\EetException $ex){
    // Tržba nebyla odeslána

    echo sprintf("BKP: %s <br>", $dispatcher->getBkp());
    echo sprintf("PKP: %s <br>", $dispatcher->getPkp());

}catch(Exception $ex){
    // Obecná chyba
    var_dump($ex);

}
<?php
$eetExample = ob_get_clean();

$startPhp = "<?php\n";

// Detekce existence autoloaderu
showInfo("Tvorba autoloaderu");
if(file_exists(__DIR__."/EETLib/Autoloader.php"))
{
  unlink(__DIR__."/EETLib/Autoloader.php");
}
file_put_contents(__DIR__."/EETLib/Autoloader.php", $startPhp . $autoloader);

// Detekce existence ukázky
showInfo("Probíhá export ukázky");
if(file_exists(__DIR__."/EET_Example.php"))
{
  unlink(__DIR__."/EET_Example.php");
}
file_put_contents(__DIR__."/EET_Example.php", $startPhp . $eetExample);

// Zkopírování příkladu
showInfo("Probíhá export certifikátu");
$certExample = __DIR__."/EETLib/".(basename(glob(__DIR__."/EETLib/filipsedivy-PHP-EET*")[0]))."/examples/EET_CA1_Playground-CZ00000019.p12";
if(file_exists($certExample))
{
  if(file_exists(__DIR__."/".basename($certExample)))
  {
    unlink(__DIR__."/".basename($certExample));
  }

  copy($certExample, __DIR__."/".basename($certExample));
}
