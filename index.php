<?php
require_once('./helpers.php');
require_once('./PagSeguroAdapter.php');

$userEmail = 'c68542824397924480358@sandbox.pagseguro.com.br';

$parametros = [
	"currency"=>"BRL",
    "items" => [
        [
            "id"=>"001",
            "description" =>"Item X",
            "amount" => "25.00",
            "quantity"=>1
        ]
    ],
	"senderEmail"=> $userEmail,
];

$result = (new PagSeguroAdapter())->createCheckoutLink($parametros);

$redirectLink = "https://sandbox.pagseguro.uol.com.br/v2/checkout/payment.html?code=$result->code";

?>
<style>
	label{
		display: block;
	}
</style>
<div>
	Go to PagSeguro: <a href="<?=$redirectLink?>" target="_blank">ir para tela de compra</a>
</div>
<div>
	<label>Use: </label><span><?=$userEmail?></span>
</div>
<div>
	<label>Use: </label><span>rAwgg0KLDh9n3Jun</span>
</div>
