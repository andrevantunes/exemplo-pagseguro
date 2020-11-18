<?php
require_once './helpers.php';
require_once('./PagSeguroAdapter.php');

$data = json_decode( file_get_contents('php://input') );


$comprador = [
    'email' => 'c68542824397924480358@sandbox.pagseguro.com.br',
    "nome"=> 'André ção',
    'cpf' => '72962940005',
    'area' => 11,
    'phone' => '999999999'
];
$items = [
    [
        "id"=>"001",
        "description" => "Ítem com ção ê",
        "amount" => "25.00",
        "quantity"=>1
    ],
    [
        "id"=>"002",
        "description" =>"Ítem com ção ê 2",
        "amount" => "25.00",
        "quantity"=>1
    ]
];
if($data->paymentMethod ==='boleto'){
    $parametros = [
        "paymentMode" => "default",
        "paymentMethod" => "boleto",
        'items' => $items,
        "currency"=>"BRL",
        "senderEmail"=> $comprador['email'],
        "senderName"=> $comprador['nome'],
        'senderCPF' => $comprador['cpf'],
        'senderAreaCode' => $comprador['area'],
        'senderPhone' => $comprador['phone'],
        'senderHash' => $data->hash,
        'shippingAddressRequired' => false
    ];
    $transaction = (new PagSeguroAdapter())->geraBoleto($parametros);
    echo json_encode($transaction);
}
else{
    $cardData = $data->card;

    $parametros = [
        "paymentMode" => "default",
        "paymentMethod" => "credit_card",
        'items' => $items,
        "currency"=>"BRL",
        "senderEmail"=> $comprador['email'],
        "senderName"=> $comprador['nome'],
        'senderCPF' => $comprador['cpf'],
        'senderAreaCode' => $comprador['area'],
        'senderPhone' => $comprador['phone'],
        'senderHash' => $data->hash,
        'shippingAddressRequired' => false,

        'creditCardToken' => $cardData->token,
        'creditCardHolderName'=> $cardData->name,
        'creditCardHolderCPF'=> $cardData->cpf,
        'installmentQuantity' => 1,
        'installmentValue' => '50.00',
        'creditCardHolderBirthDate'=> '27/10/1987',
        'creditCardHolderAreaCode'=> '11',
        'creditCardHolderPhone'=> '56273440',

        'billingAddressStreet'=>'Av. Brig. Faria Lima',
        'billingAddressNumber'=>'1384',
        'billingAddressComplement'=>null,
        'billingAddressDistrict'=>'Jardim Paulistano',
        'billingAddressPostalCode'=>'01452002',
        'billingAddressCity'=>'Sao Paulo',
        'billingAddressState'=>'SP',
        'billingAddressCountry'=>'BRA',
    ];
//    echo json_encode($parametros);
//    echo json_encode($data);
    $pagamento = new PagSeguroAdapter();
//    echo json_encode($parametros);
    $transaction = $pagamento->geraBoleto($parametros);
    echo json_encode($transaction);
}
