// Função para atualizar o conteúdo da seção <header>
async function updateHeaderContent() {
    try {
        // Carrega o arquivo JSON
        const response = await fetch('../data/headerGrupo.json'); // Atualize o caminho se necessário
        if (!response.ok) {
            throw new Error(`Erro HTTP! status: ${response.status}`);
        }
        const data = await response.json();

        // Acessa os dados do JSON
        const headerData = data.header;

        // Atualiza a imagem do logo
        const imgElement = document.getElementById('header-img');
        if (imgElement) {
            imgElement.src = headerData.image.src;
            imgElement.alt = headerData.image.alt;
        }

        // Atualiza o título
        const titleElement = document.getElementById('header-title');
        if (titleElement) {
            // Usa innerHTML para interpretar a tag <br>
            titleElement.innerHTML = headerData.title;
        }

        // Atualiza o subtítulo
        const subtitleElement = document.getElementById('header-subtitle');
        if (subtitleElement) {
            subtitleElement.textContent = headerData.subtitle;
        }

        // Atualiza o texto do botão
        const buttonElement = document.getElementById('header-button');
        if (buttonElement) {
            buttonElement.textContent = headerData.button;
        }

    } catch (error) {
        console.error('Erro ao carregar ou processar o JSON:', error);
    }
}

// Chama a função após o carregamento do DOM
updateHeaderContent();
//document.addEventListener('DOMContentLoaded', updateHeaderContent);
