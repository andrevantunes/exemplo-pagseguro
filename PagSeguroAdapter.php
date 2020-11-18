<?php
define('PAGSEGURO_MAIL', getenv('PAGSEGURO_MAIL'));
define('PAGSEGURO_S_TOKEN', getenv('PAGSEGURO_S_TOKEN'));

class PagSeguroAdapter
{
    private $API_URL = "https://ws.sandbox.pagseguro.uol.com.br/";
    private $fetchOptions = [];

    public function createCheckoutLink($data)
    {
        $parsedData = self::convertItems($data);
        return $this->fetchPagSeguro('v2/checkout', $parsedData, true);
    }

    public function geraSessao(){
        $sessao = $this->fetchPagSeguro('v2/sessions', null, true);
        return $sessao->id;
    }

    public function geraBoleto($data){
        $parsedData = self::convertItems($data);
        return $this->fetchPagSeguro('v2/transactions', $parsedData, true);
    }

    public function listTransactions($initialDate, $finalDate)
    {
        return $this->fetchPagSeguro('v2/transactions', ['initialDate' => $initialDate, 'finalDate' => $finalDate]);
    }

    public function findTransactions($id)
    {
        return $this->fetchPagSeguro('v3/transactions/' . $id);
    }

    public static function statusDetail($code)
    {
        $allStatus = [
            1 => ['Completo', 'Significa que o pagamento já foi concluído e creditado'],
            2 => ['Aprovado', 'O pagamento já foi processado e aprovado'],
            3 => ['Em Análise', 'O pagamento foi iniciado mas está sendo analisado pelo PagSeguro.'],
            4 => ['Devolvido', 'O pagamento foi devolvido.'],
            5 => ['Cancelado', 'A transação foi cancelada.']
        ];
        $currentStatus = $allStatus[$code];
        return (object)['name' => $currentStatus[0], 'description' => $currentStatus[1]];
    }

    public static function convertItems($data){
        foreach ($data['items'] as $key => $item){
            $num = $key + 1;
            foreach($item as $field => $value){
                $captalizedField = ucfirst($field);
                $data["item$captalizedField$num"] = $value;
            }
        }
        unset($data['items']);
        return $data;
    }


    private function fetchPagSeguro($endpoint, $data = null, $post = false)
    {
        $this->fetchOptions[CURLOPT_URL] = $this->getUrl($endpoint);
        $this->fetchOptions[CURLOPT_POST] = $post;
        $this->fetchOptions[CURLOPT_RETURNTRANSFER] = true;
        $this->fetchOptions[CURLOPT_HTTPHEADER] = ['Content-Type: application/x-www-form-urlencoded; charset=UTF-8'];

        $this->setDataOptions($post, $data);

        $curlSandbox = curl_init();
        curl_setopt_array($curlSandbox, $this->fetchOptions);
        return (object)(array) simplexml_load_string(curl_exec($curlSandbox));
    }

    private function setDataOptions($post, $data)
    {
        if(empty($data)) return null;
        if ($post) {
            $this->fetchOptions[CURLOPT_POSTFIELDS] = http_build_query($data);
            return null;
        }
        if (!empty($data)) {
            $this->fetchOptions[CURLOPT_URL] .= '&' . http_build_query($data);
            return null;
        }
    }

    private function getUrl($endpoint)
    {
        return $this->API_URL . '/' . $endpoint . "?email=" . PAGSEGURO_MAIL . "&token=" . PAGSEGURO_S_TOKEN;
    }
}