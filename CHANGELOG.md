# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/) and this project adheres to [Semantic Versioning](http://semver.org/).

## 3.1.0
   - **Nové funkce**
      - Možnost zpracování propustných kontrol
   - **Dokumentace**
      - Vytvoření souboru `CHANGELOG.md`
   - **Testy**
      - Pokrytí testy v případě neplatné platby [#30](https://github.com/filipsedivy/PHP-EET/issues/30)
      - Odstranění souboru `phpunit.xml.dist`
      - Refaktorování testů
      - Přidání testů pro kontrolu propustné kontroly
   - **Oprava**
      - Typografie
      - `SoapClient`
         - Vytvoření getteru a setteru pro privátní proměnné

## 3.0.1
   - Opakované zasílání plateb

## 3.0.0
   - Vytvoření [wiki](https://github.com/filipsedivy/PHP-EET/wiki) systému
   - Úprava licence z GNU GPL 3 na MIT
   - Code review
   - Úprava issues (_počeštění_)
   - Vytvoření standardu pro issues, pull requesty, vývoj,...
   - Opravení BKP a PKP kódu
   - Oprava PhpDoc bloků

## 2.0.0
   - Úprava načítání certifikátu (*nyní není třeba certifikát převádět, třída jej převede sama*)
