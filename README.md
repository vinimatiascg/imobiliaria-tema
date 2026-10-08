# Imobiliária Tema - WordPress Theme

Tema profissional para imobiliárias, corretores e portais imobiliários desenvolvido para WordPress com suporte nativo a Elementor, alta performance, SEO técnico e ferramentas administrativas inteligentes.

---

## 🚀 Versão Atual: `1.1.4`

### 📋 Histórico de Alterações (Changelog)

#### **Versão 1.1.4** (Atualização Recente)
- **Ajuste de Posicionamento dos Badges de Categoria no Grid de Posts**:
  - Os badges de categorias foram movidos do topo da imagem para o corpo do card, posicionados estrategicamente **abaixo do resumo do post e acima do rodapé com a data**.
  - A imagem destacada fica agora totalmente limpa e sem elementos sobrepostos.
  - Estilização dedicada (`.imob-post-categories-badges`) com suporte a múltiplas categorias em linha flexível (`flex-wrap`).

#### **Versão 1.1.3**
- **Refinamento do Grid de Posts (`imob_blog_grid` / `imob_post_grid`)**:
  - **Exibição de Todas as Categorias**: o card agora itera sobre todas as categorias associadas ao post, exibindo um badge individual com `imob_strtoupper()` para cada uma com suporte a quebra de linha fluida (`flex-wrap`).
  - **Remoção da Data sobre a Imagem**: eliminado o badge de data que ficava sobre a foto para manter o foco total na imagem e nas categorias.
  - **Remoção de Duplicidades**: retirados o bloco de autor e o contador de comentários, deixando o card mais limpo e objetivo.
  - **Data Única no Rodapé**: a data de publicação agora é exibida uma única vez no rodapé do card acompanhada do ícone `calendar_today`, alinhada ao botão "Ler artigo".

#### **Versão 1.1.2**
- **Novo Widget do Elementor: Grid de Posts / Blog (`imob_blog_grid` / `imob_post_grid`)**:
  - Segue fielmente a mesma linguagem visual, arquitetura de componentes e padrão do **Grid de Imóveis**.
  - **Cards Padronizados (`.imob-card`)**: fundo branco, cantos arredondados, sombra suave e efeito de elevação no hover (`translateY(-5px)`).
  - **Thumbnail com Badges Flutuantes**:
    - Badge superior com a categoria do post em destaque (com `imob_strtoupper()`).
    - Badge inferior escuro com a data de publicação no mesmo formato do preço de imóveis.
    - Placeholder elegante caso o post não possua imagem destacada.
  - **Conteúdo e Metadados Ricos**:
    - Tag do autor com ícone no topo do conteúdo.
    - Título do post com tipografia profissional e efeito hover.
    - Resumo do artigo com limitador configurável de palavras.
    - Barra de recursos com ícones de calendário e contagem de comentários.
    - Rodapé com data por extenso e botão dinâmico "Ler artigo" com seta.
  - **Controles no Elementor**:
    - Colunas responsivas (1 a 4 colunas para desktop, tablet e celular).
    - Quantidade de posts, filtro dinâmico por categoria, ordenação personalizada e paginação numérica opcional.
    - Customização de tipografia, cores, bordas e espaçamentos no painel de estilos.

#### **Versão 1.1.1**
- **Novo Widget do Elementor: FAQ - Perguntas Frequentes (`imob_faq_accordion`)**:
  - Exibição das perguntas e respostas em formato **accordion** (sanfona) com animação suave e abertura expansível.
  - Cadastro de múltiplas perguntas e respostas utilizando controle **Repeater**.
  - O campo de resposta conta com editor de texto **WYSIWYG** completo, permitindo negrito (`<strong>`), itálico, listas e inserção de **links** internos/externos.
  - Opções avançadas: primeiro item aberto por padrão, modo acordeão estrito (fecha os demais itens ao abrir um), seleção de ícones (seta ou +/-) e microdados Schema.org `FAQPage` para SEO.
  - Personalização completa de cores, tipografia, bordas e sombras pelo painel de estilos do Elementor.
- **Correção e Blindagem do Botão de WhatsApp**:
  - O botão flutuante foi configurado com `position: fixed !important; z-index: 9999999 !important;`, garantindo permanência fixa no canto inferior da tela em qualquer página ou dispositivo.
  - O botão estático presente no rodapé (`.footer-whatsapp-btn`) foi integrado à mesma URL dinâmica do WhatsApp.
  - Adicionado suporte a fallback universal via API do WhatsApp: o link agora funciona mesmo que o número ainda não tenha sido cadastrado nas opções do tema, abrindo o WhatsApp com a mensagem contextual pré-preenchida.
  - Resolução de eventuais cortes de overflow no rodapé.

#### **Versão 1.1.0**
- **Geração Automática do Código do Imóvel**:
  - O campo de digitação manual de referência/código foi substituído por uma geração automática de código único baseado no tipo de imóvel selecionado (ex: `Apt-123456` para apartamento, `Cas-654321` para casa, `Ter-987654` para terreno, etc.).
  - Validação de unicidade no banco de dados para evitar referências duplicadas.
  - Exibição como badge somente leitura na tela de edição, gerado automaticamente na publicação/salvamento.
- **Cadastro Rápido via Modal (Construtora, Proprietário e Empreendimento)**:
  - Adicionados botões dedicados de cadastro rápido ao lado de cada seletor na tela de inserção/edição do imóvel.
  - Modais responsivos e assíncronos (AJAX) que cadastram o novo item sem que o usuário precise sair da página do imóvel.
  - O novo item cadastrado é automaticamente inserido e selecionado no respectivo `<select>` da página.
- **Localização Interativa via Google Maps**:
  - Mapa interativo do Google Maps integrado na meta box de localização do imóvel.
  - Centralização inicial configurada por padrão para a cidade de **Campina Grande - PB** (`lat: -7.2247, lng: -35.8816`).
  - Marcador clicável e arrastável pelo usuário, campo de busca com autocompletar de endereços (Places API) e geocodificação reversa que preenche automaticamente endereço, bairro, cidade, latitude e longitude.
- **Otimização no Upload de Imagens**:
  - Filtro estrito de geração de imagens intermediárias no WordPress (`intermediate_image_sizes_advanced`): agora são geradas **apenas duas resoluções**:
    - `imob_large`: até **1280px** (proporcional, sem corte) para visualização em alta definição.
    - `imob_thumb`: **400x300px** (com corte central) para miniaturas de grids e listagens.
  - Desabilitada a criação redundante de arquivos `-scaled` pesados (`big_image_size_threshold`).
- **Ferramenta de Otimização de Imagens Existentes (Painel do Tema)**:
  - Nova ferramenta disponível em **Opções do Tema** que varre todas as fotos dos imóveis já cadastrados no site.
  - Aplica a marca d'água configurada no arquivo original (com opções de transparência e alinhamento em 5 posições).
  - Redimensiona e salva as resoluções de 1280px e 400x300.
  - **Remove do disco** todos os arquivos de resoluções antigas desnecessárias, economizando armazenamento do servidor.
  - Processamento em lote via AJAX com barra de progresso e console de log em tempo real para evitar limites de timeout do PHP.
- **Ferramenta de Limpeza de Imagens Órfãs (Painel do Tema)**:
  - Ferramenta para varredura e detecção de arquivos de imagem na biblioteca de mídia que não estão associados a nenhum imóvel, construtora, empreendimento, proprietário, corretor, logo ou post do site.
  - Exclusão segura e permanente via AJAX com confirmação do usuário e log detalhado.
- **Botão Flutuante do WhatsApp Dinâmico**:
  - Botão flutuante moderno no canto inferior direito com suporte a efeito de pulso suave e tooltip.
  - Integração com número configurável nas **Opções do Tema** (com fallback inteligente para o WhatsApp do corretor responsável no caso de imóvel).
  - Campo nas **Opções do Tema** para definir a **Mensagem Padrão do WhatsApp** em páginas gerais (Home, listagens, etc.).
  - Em páginas individuais de imóveis (`single-imovel`), o link monta automaticamente com a mensagem personalizada:
    > `Olá, eu gostaria de mais informações sobre o imóvel [Título do Imóvel] - [Código do Imóvel]`
- **Ajustes de Layout e Badges**:
  - Correção na exibição de badges com caracteres acentuados, garantindo caixa alta correta (`mb_strtoupper(..., 'UTF-8')`).
  - Ajuste de espaçamento e flexbox para evitar sobreposição do preço sobre o endereço nos cards e grids de imóveis.
  - Integração dos dados de contato do corretor/autor no widget de contato da página de imóvel.

---

#### **Versão 1.0.0**
- Lançamento inicial do tema imobiliário customizado.
- Custom Post Types: `imovel`, `construtora`, `proprietario`, `empreendimento`.
- Taxonomias: `tipo_imovel`, `localidade`, `caracteristica`, `status_imovel`, `corretor`.
- Widgets customizados do Elementor para exibição de cards, buscas e filtros avançados.
- Integração básica com Open Graph, Schema.org e SEO técnico.

---

## 🛠️ Tecnologias Utilizadas
- **WordPress 6+** (PHP 8.1+ / 8.2+)
- **Google Maps JavaScript API & Places Library**
- **GD Library / WordPress Image Editor** para processamento de fotos e marca d'água
- **Elementor**
- **HTML5 Semântico, Vanilla CSS e jQuery / AJAX**
