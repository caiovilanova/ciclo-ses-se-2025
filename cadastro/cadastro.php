<?php

// Verifica se os dados foram enviados via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    require("lib/funcoes.php");

    // Obtém os dados do formulário
    $nome = $_POST['nome'] ?? 'Contato Black Caveira';  // Nome enviado pelo formulário ou um nome padrão
    $telefone = $_POST['telefone'] ?? '';     // Número do WhatsApp enviado pelo formulário
    
    $whatsapp = formatPhoneNumber($telefone);

    // Configurações para o Token e Funil
    $accessToken = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6IjBjZmE5Yzk0MDIxMTIxZWU3NTkwODZjNThmZWZkMmY2ZjI1MmUyY2M1NzM1ZjE5NGI4ZDA5ZjJhNzFhNTNkMjA0NTVlZGM0MjcyOGMxOGYwIn0.eyJhdWQiOiJkNDY5NDFkMy05N2ZkLTRlMmItOWIyZS04YTUxOTM5NTQ0ZDAiLCJqdGkiOiIwY2ZhOWM5NDAyMTEyMWVlNzU5MDg2YzU4ZmVmZDJmNmYyNTJlMmNjNTczNWYxOTRiOGQwOWYyYTcxYTUzZDIwNDU1ZWRjNDI3MjhjMThmMCIsImlhdCI6MTczMDA4NDQxMCwibmJmIjoxNzMwMDg0NDEwLCJleHAiOjE3MzMwMTEyMDAsInN1YiI6IjExODE5ODg3IiwiZ3JhbnRfdHlwZSI6IiIsImFjY291bnRfaWQiOjMzMzczMTQ3LCJiYXNlX2RvbWFpbiI6ImtvbW1vLmNvbSIsInZlcnNpb24iOjIsInNjb3BlcyI6WyJwdXNoX25vdGlmaWNhdGlvbnMiLCJmaWxlcyIsImNybSIsIm5vdGlmaWNhdGlvbnMiXSwiaGFzaF91dWlkIjoiZDQzMDg2Y2UtMjJmZi00M2YzLTg1YzQtOTlmNTUwNTEzZWIyIiwiYXBpX2RvbWFpbiI6ImFwaS1jLmtvbW1vLmNvbSJ9.jTGct75oYLdqaSh7ptX__3LB7GWpWdfeetRlT8YIBsXjhdLqSD_i5gWv9lhVl3DooEpx1v5omYp8i1FWc-wUWFTTPjlTSlJEKn50t8R4jI9wLnEojm5naVoUYPJA1S6twt_jN41G0_1J6GRr_bHT7iFic1BElvxcE7MZtkQKBedJ5Zp-z5ZLH9Uu_rklVJ43mcv0fp6o4wf81YBTVFsGTaxuMGmVpq7WBXmp0_4wGk9y8UxLXGzJM-4l4GHZ2ggFqj2O6rS8r_OUI_OlEq_-MA18b-vZiybAAiPXOwikL0fPbRtBPL64sXa9167OBGpHa_AXoldnVq3TQ9HpGxgtYg';
    $pipelineId = 9877883;  // ID do funil onde o lead será criado
    $statusId = 75861879;   // ID da etapa onde o lead será criado

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
          "name" => "Lead para $nome",
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
    header("Location: https://pmse.portalciclo.com.br/cadastro/parabens");

} else {
    echo "Acesso inválido. ";
}
