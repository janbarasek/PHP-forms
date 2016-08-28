# Baraja PHP Forms

Živé demo: [http://forms.baraja.cz/demo.php](http://forms.baraja.cz/demo.php)

Framework má opravdu simple-use syntaxi, kterou pochopí každý během jediné minuty. Není potřeba studovat dokumentaci. Stačí jej prostě začít používat podle přiložené ukázky. Veškerou logiku si řeší interně sám.

Při návrhu jsem vycházel z praxe, proto framework sám řeší všechny věci, které se běžně programují (automatická validace, detekce typů polí, kontrola typovosti dat, pokusy o podvrhnutí formuláře, ochrana před roboty (vlastním captcha kódem), možnost nastavení vlastních filtrů, variabilita chybových hlášení, ...).

Framework se výborně kamarádí s jinými frameworky. Pokud používáte BootStrap, tak bude formulář rovnou hezky ostylovaný (jako na obrázku); v ostatních případech stačí velice rychle vytvořit základní styly. Design formuláře je možné dále jednoduše přizpůsobit.

Instalace
---------

Celý framework je psaný v objektech jako statická třída. Stačí jej proto na začátku souboru načíst a je ihned k dispozici. Načtení se dělá jednoduše takto:

```php
<?php
include 'forms.php';
```

Příklad použití
---------------

Framework myslí hlavně na rychlost a další kompatibilitu. Mám v plánu brzy vydat další "komponenty", které budou tvořit celý plnohodnotný framework s důrazem na jednoduchost a opravdu rychlý vývoj (a také rychlost webu).

Ukázka syntaxe jednoduchého formuláře:

```php
<?php
include 'forms.php';
 
$form = array(
    array(
        'name' => 'Jméno a příjmení',
        'id' => 'name', // interní název indexu (pole), kde bude dostupná hodnota
        'type' => 'text', // typ vstupního pole (textové)
        'required' => true, // jde o povinnou položku
        'valid' => array(
            'strlen' => '+5', // délka musí být >= 5
        )
    ),
    array(
        'name' => 'E-mail s požadovaným formátem',
        'id' => 'mail',
        'type' => 'text',
        'required' => true,
        'data' => '@', // toto bude předvyplněno v prázdném formuláři jako placeholder
        'valid' => array(
            'filter' => 'mail', // kontroluje, že to je mail
            'contain' => '@baraja.cz', // musí obsahovat tento řetězec
            'error' => 'Zadej ve formátu cokoli@baraja.cz', // vlastní error hláška
        )
    ),
);
 
Forms::render($form, // zavoláme statickou metodu, která se postará o celou logiku formuláře
    array(
        'method' => 'POST', // metoda odesílání dat (GET nebo POST), vyžaduji velká písmena
        'action' => 'postmail.php', // kam přesměrovat, až bude formulář správně vyplněn
       'submit' => 'Dokončit objednávku', // text odesílacího tlačítka
    )
);
```
