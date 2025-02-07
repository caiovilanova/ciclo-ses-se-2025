<?php
// Verifica se os dados foram enviados via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {


    // Configurações para o Token e Funil
    $accessToken = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6ImNmYTg1YjAyNzI3YWJhNDZhYmQ2ZDY3YzYzYzUzOTdhMzVlYWNkZDkxZTU2MDg3NWFiMDA5ODZiOWM5MzhmMTBhMDNhN2VjZDY4MDdjODk2In0.eyJhdWQiOiJkNDY5NDFkMy05N2ZkLTRlMmItOWIyZS04YTUxOTM5NTQ0ZDAiLCJqdGkiOiJjZmE4NWIwMjcyN2FiYTQ2YWJkNmQ2N2M2M2M1Mzk3YTM1ZWFjZGQ5MWU1NjA4NzVhYjAwOTg2YjljOTM4ZjEwYTAzYTdlY2Q2ODA3Yzg5NiIsImlhdCI6MTczODYyMTU4OSwibmJmIjoxNzM4NjIxNTg5LCJleHAiOjE3NjcxMzkyMDAsInN1YiI6IjExODE5ODg3IiwiZ3JhbnRfdHlwZSI6IiIsImFjY291bnRfaWQiOjMzMzczMTQ3LCJiYXNlX2RvbWFpbiI6ImtvbW1vLmNvbSIsInZlcnNpb24iOjIsInNjb3BlcyI6WyJwdXNoX25vdGlmaWNhdGlvbnMiLCJmaWxlcyIsImNybSIsIm5vdGlmaWNhdGlvbnMiXSwiaGFzaF91dWlkIjoiM2RiZDg4MTAtNmIyZi00YjUyLWExZGEtOTUzZmU3ODI0NTk0IiwiYXBpX2RvbWFpbiI6ImFwaS1jLmtvbW1vLmNvbSJ9.CpSA8A-y08nwFwYhCkBxaB-f8vR63kEqpt7d4uxevYTfBJNFEhBInSTpiGxS5BTStE8zt-ebJzhcFVK1Rk7IISaiEoGWrn4OX4RG1W66U4GNzVN0D8IppwJFat_7kX3ysDo8YPr6aznHCnDVuJfJu2MDfDanw2XgU_TdamB2c9u5xkOTNHe2hh_RuOsyMajcbQFQ0cwFDL08RZAIqAUFDmmevHIS0McxSzCwPImOJo7Q7mVUt0V2CosQgETikXXB730BnuVs33_4y_D-EoJyCEsgO7d6cpHVOrWXQLkxqzWn7GnE-i1v6DgJDi5GNoSa8lVVKI3xIRQZQgo9a3r-vA';
    $pipelineId = 10172655;  // ID do funil onde o lead será criado
    $statusId = 78065443;   // ID da etapa onde o lead será criado
    $urlRetorno = "https://ses.portalciclo.com.br/cadastro/parabens";
  
    require("../lib/funcoes.php");

    // Obtém os dados do formulário
    $nome = $_POST['nome'] ?? 'Sem nome';  // Nome enviado pelo formulário ou um nome padrão
    $telefone = $_POST['telefone'] ?? '';     // Número do WhatsApp enviado pelo formulário
    
    $whatsapp = formatPhoneNumber($telefone);

    // Passo 1: Criar o contato
    $curl = curl_init();
    curl_setopt_array($curl, [
      CURLOPT_URL => "https://ciclo.kommo.com/api/v4/contacts",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_HTTPHEADER => [
        "accept: application/json",
        "authorization: Bearer $accessToken",
        "content-type: application/json"
      ],
      CURLOPT_POST => true,
      CURLOPT_POSTFIELDS => json_encode([
        [
            'name' => $nome,
            'custom_fields_values' => [
                [
                    'field_code' => 'PHONE',
                    'values' => [
                        ['value' => $whatsapp, 'enum_code' => 'WORK']
                    ]
                ]
            ]
        ]
      ])
    ]);

    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    if ($err) {
      die("Erro ao criar contato: $err");
    }

    // Decodificar a resposta para obter o ID do contato criado
    $data = json_decode($response, true);
    if (isset($data['_embedded']['contacts'][0]['id'])) {
      $contactId = $data['_embedded']['contacts'][0]['id'];
      //echo "Contato criado com ID: $contactId\n";
    } else {
      die("Erro ao obter o ID do contato.");
    }

    // Passo 2: Criar o lead e vincular ao contato
    $curl = curl_init();
    curl_setopt_array($curl, [
      CURLOPT_URL => "https://ciclo.kommo.com/api/v4/leads",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_HTTPHEADER => [
        "accept: application/json",
        "authorization: Bearer $accessToken",
        "content-type: application/json"
      ],
      CURLOPT_POST => true,
      CURLOPT_POSTFIELDS => json_encode([
        [
          "name" => $nome,
          "pipeline_id" => $pipelineId,
          "status_id" => $statusId,
          "_embedded" => [
            "contacts" => [
              [
                "id" => $contactId,
                "is_main" => true
              ]
            ]
          ]
        ]
      ])
    ]);

    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    if ($err) {
      die("Erro Z: $err");
    }

    // Exibir a resposta da criação do lead
    //echo "Lead criado com sucesso:\n";
    //echo $response;
    header("Location: $urlRetorno");

} else {
    echo "Acesso inválido. ";
}
