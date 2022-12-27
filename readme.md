# Freemecef

[EN] Freemecef makes it possible to issue standardized invoices with the emecef API of the DGI with PHP

[FR] Freemecef permet d'émettre de factures normalisées avec l'API emecef de la DGI avec PHP

## Usage
```
//set a valide jwt token
$token = "eyJhbGciOiJ...";
```

### Invoice API

```
// Create new MecefInfo object
// second parameter: false = test env, true = prod env, default: false 
$mecefinfo =new \Freemecef\EmecefInfo($token);

//get info
$info = $mecefinfo->getInfo();

//print result
print_r($info->json();


// you can also use
->getTaxGroups() to get tax groups
->getInvoiceTypes() to get invoice types
->getPaymentTypes() to get payment types

```

### Info API

```
emecef = (new \Freemecef\Emecef($token))->setClient("Papa na Venza")->setInvoiceType(InvoiceTypes::VENTE)
    ->setOperator("Tonton")
    ->addPaymentType(PaymentTypes::ESPECES, 100) // you can add multiple payment types
    ->addProduct('Article 2',50, 1); // you can add multiple products

$invoice = $emecef->invoice()->json();

$data = $emecef->execute($invoice['uid'])->json())->json();

```

## For more

For more, read Emecef documentation at [DGI website](https://impots.bj)

## Contact me 

On twitter [@tontonakam](https://twitter.com/tontonakam)
