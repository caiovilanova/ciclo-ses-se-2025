// Função para atualizar o conteúdo da seção "Quem é a CICLO"
async function updateCicloContent() {
    try {
        // Carrega o arquivo JSON
        const response = await fetch('../data/about.json'); 
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        const data = await response.json();

        // Acessa os dados do JSON
        const aboutData = data.about;

        // Atualiza o título
        const titleElement = document.getElementById('about-title');
        if (titleElement) {
            titleElement.textContent = aboutData.title;
        }

        // Atualiza a imagem
        const imageContainer = document.getElementById('about-img');
        if (imageContainer) {
            const imgElement = imageContainer.querySelector('img');
            if (imgElement) {
                imgElement.src = aboutData.image.src;
                imgElement.alt = aboutData.image.alt;
            }
        }

        // Atualiza os parágrafos
        const paragraphsContainer = document.getElementById('about-paragraphs');
        if (paragraphsContainer) {
            // Limpa os parágrafos existentes
            paragraphsContainer.innerHTML = '';

            // Cria e adiciona os novos parágrafos
            aboutData.paragraphs.forEach(paragraphText => {
                const p = document.createElement('p');
                p.textContent = paragraphText;
                paragraphsContainer.appendChild(p);
            });
        }

    } catch (error) {
        console.error('Erro ao carregar ou processar o JSON:', error);
    }
}

// Chama a função após o carregamento do DOM
updateCicloContent();
//document.addEventListener('DOMContentLoaded', updateCicloContent);
