<?php
require_once('./helpers.php');
require_once('./PagSeguroAdapter.php');

$pagamento = new PagSeguroAdapter();
$code = $pagamento->geraSessao();


?>
<head>
    <script type="text/javascript" src="https://stc.sandbox.pagseguro.uol.com.br/pagseguro/api/v2/checkout/pagseguro.directpayment.js"></script>
</head>
<style>
	label{
		display: block;
	}
</style>
<body>
    <div>
        <button onclick="pagarComBoleto()">pagar com boleto</button>

        <div id="form">
            <input name="cpf" placeholder="cpf" value="01529151090"/>
            <input name="name" placeholder="card name" value="Some name"/>
            <input name="number" placeholder="card number" value="4111111111111111"/>
            <input name="cvv" placeholder="card cvv" value="123"/>
            <input name="month" placeholder="validade mes" value="12"/>
            <input name="year" placeholder="validade ano" value="2030"/>
            <input name="band" placeholder="bandeira" value="visa"/>
            <input name="installments" placeholder="parcelas" value="2"/>

            <button onclick="pagarComCartao()">pagar com boleto</button>
        </div>
    </div>
    <script>
        PagSeguroDirectPayment.setSessionId('<?=$code?>');
        function pagarComBoleto(){
            PagSeguroDirectPayment.onSenderHashReady(function(response){
                if(response.status === 'error') return false;
                var hash = response.senderHash;
                fetch('/pagar.php', {method: 'post', body: JSON.stringify({hash, paymentMethod: 'boleto'}), headers: {'content-type': 'application/json'}})
                .then(r => r.json())
                .then(({ paymentLink }) => window.open(paymentLink))
            });
        }
        function pagarComCartao(){
            PagSeguroDirectPayment.onSenderHashReady(function(response){
                if(response.status === 'error') return false;
                var hash = response.senderHash;

                var form = document.querySelector('#form');
                var card = serializeForm(form);


                PagSeguroDirectPayment.createCardToken({
                    cardNumber: card.number,
                    brand: card.brand,
                    cvv: card.cvv,
                    expirationMonth: card.month,
                    expirationYear: card.year,
                    success: function(response) {
                        card.token = response.card.token
                        console.log('h1', card)
                        PagSeguroDirectPayment.getInstallments({
                            amount: 50,
                            maxInstallmentNoInterest: 10,
                            brand: card.brand,
                            success: function(response){
                                console.log('success', response.installments.visa)
                                fetch('/pagar.php', {method: 'post', body: JSON.stringify({hash, paymentMethod: 'cartao', card}), headers: {'content-type': 'application/json'}})
                                    .then(r => r.json())
                                    // .then(({ paymentLink }) => window.open(paymentLink))
                            },
                            error: function(response) {
                                console.log('error')
                            },
                            complete: function(response){
                                console.log('complete')
                            }
                        });

                    },
                    error: function(response) {
                        console.log('error', response)
                        // Callback para chamadas que falharam.
                    },
                    complete: function(response) {
                        console.log('complete', response)
                        // Callback para todas chamadas.
                    }
                });


            });
            return false
        }

        document.querySelector('#form').addEventListener('onsubmit', event => pagarComCartao(event), false)

        function serializeForm(formElement) {
            const inputs = formElement.querySelectorAll('input, select, textarea,[aria-selected=true],[aria-checked=true]')
            return Object.values(inputs).reduce((obj, input) => {
                const name = input.name || input.dataset.name
                const value = input.value || input.dataset.value
                if (!name || !value) return obj
                return {
                    ...obj,
                    ...fromDotObject(value, name, obj),
                }
            }, {})
        }

        function fromDotObject(value, name, obj) {
            if (!name.match(/.*\..*/)) return { [name]: value }
            const [base, field] = name.split(/\.(.+)/)
            return {
                [base]: { ...obj[base], [field]: value },
            }
        }
    </script>
</body>