<?php
// Verificar se o código de autorização foi recebido
if (isset($_GET['code'])) {
    $authorizationCode = $_GET['code'];

    // Configurar os dados para a requisição de troca pelo access_token
    $subdomain = 'ciclo';
    $url = "https://{$subdomain}.kommo.com/oauth2/access_token";

    $data = [
        'client_id' => 'd46941d3-97fd-4e2b-9b2e-8a51939544d0',            // Substitua pelo Integration ID fornecido pelo Kommo
        'client_secret' => 'ndbvbvKMjgczkyKZoL0d4ioUhQlpQMMnioafaKJFVWEm4hL8BsraoSpMul31Mm7s',       // Substitua pelo Secret Key fornecido pelo Kommo
        'grant_type' => 'authorization_code',
        'code' => $authorizationCode,
        'redirect_uri' => 'https://pmse.portalciclo.com.br/kommo.php' // Esse é o mesmo Redirect URI configurado no Kommo
    ];

    // Fazer a requisição usando cURL
    $curl = curl_init(); // Inicia a sessão cURL
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); // Define para retornar o resultado como string
    curl_setopt($curl, CURLOPT_USERAGENT, 'Kommo-oAuth-client/1.0'); // Define um user agent para a requisição
    curl_setopt($curl, CURLOPT_URL, $url); // URL para onde a requisição será enviada (o endpoint do Kommo)
    curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type: application/json']); // Define o cabeçalho do tipo de conteúdo
    curl_setopt($curl, CURLOPT_HEADER, false); // Define para não incluir o cabeçalho da resposta no output
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST'); // Define o método da requisição como POST
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data)); // Define os dados enviados no corpo da requisição
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 1); // Verifica a autenticidade do certificado SSL
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2); // Confirma que o nome do host corresponde ao certificado SSL

    $response = curl_exec($curl); // Executa a requisição
    $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE); // Obtém o código HTTP da resposta
    curl_close($curl); // Fecha a sessão cURL

    // Processar a resposta da requisição
    if ($http_code == 200) {
        $responseData = json_decode($response, true);
        $accessToken = $responseData['access_token'];
        $refreshToken = $responseData['refresh_token'];

        // Aqui você pode salvar o access_token e refresh_token em um banco de dados seguro ou em um arquivo
        echo "Tokens obtidos com sucesso!";
        echo "<br>Acess Token" . $accessToken;
        echo "<br>Refresh Token" . $refreshToken;

        // Redirecionar ou exibir mensagem de sucesso para o usuário
    } else {
        echo "Erro ao obter tokens: " . $response;
    }
} else {
    echo "<br>Código de autorização não encontrado.";
}
