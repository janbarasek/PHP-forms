<?php
// Vytvořil Jan Barášek; Baraja

echo '<meta charset="UTF-8">';
echo '<link href="min.css" rel="stylesheet">';
include 'forms.php';

/*
	Formulář si řeší veškerou logiku interně sám.
	Pokud však programátor potřebuje nějaká data, tak je má
	po celou dobu k dispozici v těchto statickým proměnných:

	Forms::$data = pole všech dat (surový formát)
	Forms::$data_type = pole všech typů formulářů
	Forms::$is_ok = jsou všechna data validní?
	Forms::$is_post = byl formulář odeslán?
	Forms::$sum_captcha = kolikrát byla vykreslena captcha?

	// -----

	Formulář je předprogramován na výpis dat. Pokud chcete
	chování změnit (například je nějak zpracovat), upravte
	v soboru forms.php oblast, začínající komentářem:
	"Here is your data output"

*/

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
	array(
		'name' => 'E-mail s kontrolou délky',
		'id' => 'mail2',
		'type' => 'text',
		'required' => true,
		'data' => '@',
		'valid' => array(
			'filter' => 'mail', // kontroluje, že to je mail
			'strlen' => '+8', // délka musí být >= 8
		)
	),
	array(
		'name' => 'Heslo',
		'id' => 'password',
		'type' => 'password',
		'required' => true,
		'valid' => array(
			'strlen' => '+6', // délka musí být >= 6
		)
	),
	array(
		'name' => 'Heslo znovu',
		'note' => 'Framework automaticky ohlídá, že budou obě hesla stejná (i když mají inputy jiný název).',
		'id' => 'password2',
		'type' => 'password',
		'required' => true,
		'valid' => array(
			'strlen' => '+6', // délka musí být >= 6
		)
	),
	array(
		'name' => 'Telefon',
		'note' => 'Rádi vám zavoláme zpět',
		'id' => 'phone',
		'type' => 'text',
		'required' => true,
		'valid' => array(
			'filter' => 'numeric', // musí to být číslo (pokud obsahuje nečíselné znaky, budou odstraněny a až pak se validuje)
			'strlen' => '+9', // délka musí být >= 9 (typické telefonní číslo)
		)
	),
	array(
		'name' => 'Typ jídelníčku',
		'id' => 'type',
		'type' => 'point', // může být "radio" nebo "point"
		'required' => true,
		'data' => array( // položky se uvádějí jako pole
			'Na hubnutí',
			'Udržení tělesné hmotnosti',
			'Přibírání svalové hmoty',
		),
	),
	array(
		'name' => 'Pohlaví',
		'id' => 'gender',
		'type' => 'point',
		'required' => true,
		'data' => array(
			'Muž', 'Žena',
		),
	),
	array(
		'name' => 'Rok narození',
		'note' => 'Sem můžete psát jakékoli nesmysly kolem čísel a FW si vše ohlídá. To je ale šikula...',
		'id' => 'age',
		'type' => 'text',
		'required' => true,
		'valid' => array(
			'filter' => 'numeric', // opět kontrola validity
		),
	),
	array(
		'name' => 'Výška',
		'id' => 'height',
		'type' => 'text',
		'required' => true,
		'valid' => array(
			'filter' => 'numeric',
		)
	),
	array(
		'name' => 'Vaše aktuální hmotnost',
		'id' => 'value',
		'type' => 'text',
		'required' => true,
	),
	array(
		'name' => 'Váš cíl',
		'id' => 'final',
		'type' => 'area', // vykreslí velké víceřádkové textové pole
		'required' => true,
	),
	array(
		'name' => 'Popište Vaši denní činnost',
		'id' => 'everyday',
		'type' => 'area',
		'required' => true,
	),
	array(
		'name' => 'Popište Vaši postavu',
		'id' => 'body',
		'type' => 'area',
		'required' => true,
	),
	array(
		'name' => 'Jaká jídla si přejete vynechat?',
		'note' => 'Alergie, ...',
		'id' => 'alergy',
		'type' => 'area',
		'required' => true,
	),
	array(
		'name' => 'Jaké doplňky stravy chcete zahrnout?',
		'id' => 'specialfood',
		'type' => 'area',
		'required' => true,
	),
	array(
		'name' => 'Opište captcha kód',
		'id' => 'captcha',
		'type' => 'captcha', // vykreslí captcha kontrolu, nemá žádné další parametry
		'required' => true,
	),
	array(
		'name' => 'Souhlasím s <a href="#">obchodními podmínkami</a>.',
		'id' => 'obch_podminky',
		'type' => 'checkbox', // zaškrtávací pole
		'required' => true, // musí být zaškrtnuto
		'valid' => array(
			'error' => 'Musíte souhlasit s obchodními podmínkami!', // chybovka, když není zaškrtnuto
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