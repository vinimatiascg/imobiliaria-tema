# Imobiliária Tema - WordPress Theme

Tema profissional para imobiliárias, corretores e portais imobiliários desenvolvido para WordPress com suporte nativo a Elementor, alta performance, SEO técnico e ferramentas administrativas inteligentes.

---

## 🚀 Versão Atual: `1.4.2`

### 📋 Histórico de Alterações (Changelog)

#### **Versão 1.4.2** (Atualização Recente)
- **"Apresentação com foto (texto + imagem)"**: colunas com 50% de largura cada; nova aba **Estilo** no Elementor com tipografia (fonte, tamanho, peso, altura de linha, espaçamento etc.) e cor da chamada superior, título, subtítulo, texto e botões.
- Base dos widgets de seção (`inc/elementor/widgets/sections.php`) ganhou `register_style_controls()` e `add_text_style()` para adicionar controles de estilo em outras seções.

#### **Versão 1.4.1**
- **Nova seção/widget Elementor "Apresentação com foto (texto + imagem)" (`template-parts/sections/split_image.php`)**: chamada, título, subtítulo e texto de um lado e foto em PNG com fundo transparente do outro. Configurável como a "Apresentação": botões, título grande e linha dourada, além de posição da foto (direita ou esquerda), texto alternativo e fundo atrás da foto (arco azul com detalhe dourado, círculo dourado ou sem fundo). A foto fica apoiada na base da seção. O layout padrão da página Sobre passa a usar essa seção em "Quem está por trás da IS22".

#### **Versão 1.4.0**
- **Páginas Sobre e Avaliação de Imóveis encaixotadas (`template-parts/sections/split.php`, `template-parts/sections/pillars.php`, `assets/css/sections.css`)**: as seções "Apresentação" (título + quadro) e "Blocos coloridos" (Missão/Visão/Valores, Tipos/Métodos/Outros serviços) agora ficam dentro do container de 1320px como as demais páginas, com cantos arredondados; texto da "Trajetória e formação" alinhado ao container e fundo uniforme na chamada "Entre em contato".

#### **Versão 1.3.9**
- **1. Bairro nas listagens (`functions.php`)**: no site os bairros estão no mesmo nível da cidade (não são filhos de "Campina Grande"), e vários imóveis têm a cidade como termo primário no Yoast. Agora cidades (lista em **Personalizar > Imóveis: localidades**, padrão `campina-grande, joao-pessoa`, e qualquer localidade com filhos) nunca aparecem como bairro quando há um bairro marcado; o termo primário vale entre os bairros. A cidade só aparece quando o imóvel não tem bairro.
- **2. Etapas do processo na home**: o CSS do Elementor (`.elementor img { height: auto }`) esticava as ilustrações sobre o texto. Seletores reforçados e `overflow: hidden` na imagem; a mesma proteção foi aplicada ao slider e ao topo. Nova ilustração para a 6ª etapa (Entrega).
- **3. Redes sociais no rodapé**: ícones de Facebook, Instagram e WhatsApp (dados de Opções do Tema) na coluna 1 e novo widget **Imobiliária - Redes sociais**; o widget "Marca do rodapé" ganhou a opção de mostrar os ícones.

#### **Versão 1.3.8**
- **1. Rodapé personalizável (`footer.php`, `inc/core/footer.php`)**:
  - Áreas de widgets **Rodapé - Coluna 1/2/3** (Aparência > Widgets). Coluna sem widgets mostra o conteúdo padrão.
  - Novos widgets **Imobiliária - Marca do rodapé** (logo, texto e botão) e **Imobiliária - Mais visitados** (título, quantidade e tipos de conteúdo).
  - Coluna 1 com um único botão de WhatsApp ("Fale conosco"); removido o ícone de chat duplicado.
  - Coluna "Acesso rápido" carrega o menu do novo local **Menu do rodapé (Acesso rápido)** (Aparência > Menus).
  - Coluna "Mais visitados" lista as páginas e imóveis mais acessados, com contador de visitas próprio (funciona com cache/Cloudflare, ignora robôs e usuários logados). Quantidade e tipos configuráveis em Personalizar > Rodapé ou no widget.
  - Faixa inferior preta com a assinatura "Desenvolvido por Vinicius Matias especialista em wordpress." no estilo do logo de vmatias.dev (Inter + JetBrains Mono e ponto verde), com link para https://vmatias.dev/. Removido o botão "Faça sua Simulação Agora!".

#### **Versão 1.3.7**
- **1. Novo widget Elementor "Slider (largura total)" (`template-parts/sections/slider.php`, `assets/css/slider.css`, `assets/js/slider.js`)**: slides com imagem de fundo, chamada, título, texto, 2 botões e link do slide inteiro; alinhamento por slide; altura (420px, 560px, 720px ou tela inteira); escurecimento da imagem; transição em fade; passagem automática com tempo configurável (pausa ao passar o mouse); setas, indicadores, teclado e deslizar no celular; opção de ocupar 100% da largura da tela mesmo dentro de container.
- **2. Etapas do processo**: opções de 3, 4 ou 5 etapas por linha; CSS refeito com flexbox para qualquer quantidade de etapas (linhas incompletas ficam centralizadas) e responsivo (3 > 2 > 1 coluna).
- **3. Página Sobre**: quadros de texto da seção "Apresentação" voltam a encostar na borda da tela, topo sem imagem usa a largura toda (sem a moldura vazia), blocos Missão/Visão/Valores com espaçamento uniforme e fundo da seção "Trajetória e formação" limpo.
- **4. Topo da página inicial no layout da Black Friday**: a seção/widget "Topo (hero)" ganhou o campo **Layout do topo** (Automático, Oferta ou Clássico). No automático, a página inicial usa o mesmo hero da landing da Black Friday (componente `template-parts/sections/hero-offer.php` + `assets/css/hero-offer.css`), com textos, botões, quadro de desconto e contagem regressiva editáveis.
- **5. Bairro nas listagens**: nova função `imob_get_localidade_term()` exibe o termo marcado como primário (Yoast SEO / Rank Math) e, sem primário, o termo mais específico (bairro) em vez da cidade. Aplicada em cards, listagens, widgets, página do imóvel, SEO e llms.txt.

#### **Versão 1.3.6**
- **1. Etapas do processo com colunas configuráveis e ilustrações (`template-parts/sections/process.php`, `inc/sections/sections.php`, `assets/images/etapas/`)**:
  - Novo campo **Etapas por linha** (1 a 5) na seção/widget Elementor "Etapas do processo".
  - 5 ilustrações SVG criadas para cada etapa (Simulação, Avaliação, Análise de crédito, Escolha do imóvel, Assinatura), usadas por padrão quando nenhuma imagem é enviada.
- **2. Botão de WhatsApp opcional nos depoimentos**: campo **Exibir botão do WhatsApp** na seção/widget "Depoimentos" e no widget Elementor "Depoimentos" (repetidor).
- **3. Landing page da Black Friday (`template-black-friday.php`, `inc/landing/black-friday.php`, `assets/css/black-friday.css`, `assets/js/black-friday.js`)**:
  - Modelo de página "Landing Page - Black Friday" com hero, contagem regressiva, benefícios (R$ 1.000 de desconto extra mínimo, documentação grátis, descontos, brindes, 1 ano de condomínio grátis nas casas em condomínio), imóveis em oferta, formulário de lead (origem "Black Friday"), FAQ e schema `SaleEvent`.
  - Datas, valor do desconto e mensagem do WhatsApp em Personalizar > Black Friday.
- **4. Conteúdo inicial (`inc/content/`)**: na primeira visita de um administrador ao painel, o tema cria a página `/black-friday/` e 5 notícias do mercado imobiliário (Selic, Minha Casa Minha Vida, crédito SBPE, FipeZAP e reforma tributária) com imagens destacadas 1600x900. Nada é duplicado: itens com o mesmo slug são ignorados.
- **5. Google News / Discover**: sitemap de notícias apenas com posts dos últimos 2 dias (regra do Google News), autor com link para a página do autor, data de atualização visível, imagem destacada em tamanho cheio e verificador de elegibilidade na tela de edição do post.
- **7. Atualizações pelo GitHub (`inc/core/theme-updater.php`)**: o tema compara a versão instalada com o `style.css` do repositório `vinimatiascg/imobiliaria-tema` (branch `main`, configurável) e mostra a atualização em Painel > Atualizações e Aparência > Temas, com o botão "Atualizar agora", igual aos temas da loja. Tela de status e configurações em Aparência > Atualizações do tema (branch e token opcional para repositório privado).
- **6. CTA de WhatsApp no final de todos os posts (`inc/blog/post-cta.php`)**: "Quer comprar o seu imóvel em Campina Grande com condições especiais, entre em contato conosco."

#### **Versão 1.3.5**
- **1. Otimização Avançada para SEO, GEO (Generative Engine Optimization + Local Geo Tags), Discovery de Redes Sociais e Google News (`inc/core/seo.php`, `inc/seo/schema.php`)**:
  - **Schema.org `@graph` Unificado**: Entidades conectadas (`RealEstateAgent` + `Organization` com CRECI-PB 9972-F, `WebSite` com `SearchAction`, `RealEstateListing` + `Apartment`/`SingleFamilyResidence`, `ApartmentComplex`, `HomeAndConstructionBusiness`, `NewsArticle` + `BlogPosting`, `CollectionPage` + `ItemList` e `BreadcrumbList`), incluindo `SpeakableSpecification` para assistentes de voz/IA.
  - **Google News & Google Discover**: Diretivas `max-image-preview:large, max-snippet:-1, max-video-preview:-1` no `wp_robots`, priorização de imagem LCP (`fetchpriority="high"`), meta tags `news_keywords`, imagens no feed RSS e sitemap dedicado em `/sitemap-news.xml`.
  - **GEO (`/llms.txt` & Local Geo Meta Tags)**: Geração e sincronização automática de `/llms.txt` e `/llms-full.txt` para motores de busca generativos (ChatGPT, Perplexity, Gemini, Claude), permissões explícitas no `robots.txt` e meta tags geográficas (`geo.region`, `geo.placename`, `geo.position`, `ICBM`).
  - **Social Media Discovery**: Open Graph completo, Twitter Cards (`summary_large_image` + `twitter:label1/data1`), tags comerciais (`product:price:amount`, `product:price:currency`, `product:availability`) e integração com filtros do Yoast SEO.
- **2. Campo Opcional "Registro de Incorporação" em Imóveis e Empreendimentos (`inc/meta-boxes/imovel-meta.php`, `inc/meta-boxes/empreendimento-meta.php`, `single-imovel.php`, `single-empreendimento.php`)**:
  - Adicionado o campo de texto opcional **Registro de Incorporação** no cadastro de Imóveis (`_imob_registro_incorporacao`), no cadastro de Empreendimentos (`_imob_emp_registro_incorporacao`) e no modal de criação rápida de empreendimento.
  - Exibição condicional no frontend (`single-imovel.php` e `single-empreendimento.php`) e nos dados estruturados (`Schema.org` e `/llms.txt`) apenas quando o campo estiver preenchido.
- **3. Correção de Permalinks, Rewrite Rules e Rotas de CPTs (`functions.php`, `inc/post-types/imovel.php`)**:
  - Reordenado o carregamento de taxonomias antes dos Custom Post Types em `functions.php` para garantir o registro de `%localidade%` antes da permastruct de `imovel`.
  - Corrigido `'has_archive' => 'imoveis'` no CPT `imovel` e adicionadas regras explícitas de rewrite para single (`/imoveis/{localidade}/{slug}/`) e paginação (`/imoveis/page/{n}/`), além de auto-flush inteligente (`imob_maybe_flush_rewrite_rules`).
- **4. Novos Templates de Artigo, Páginas Institucionais e 404 (`single.php`, `page.php`, `404.php`, `index.php`)**:
  - Criado `single.php` com layout em duas colunas (artigo completo + sidebar com CTA de imóveis, últimos artigos e categorias), breadcrumbs, tempo de leitura, compartilhamento e artigos relacionados.
  - Criado `page.php` compatível com páginas Elementor (sem duplicar `h1.entry-title`), páginas institucionais (`Termos e Condições`, `Política de Privacidade`, `FAQ`) e layout dedicado para `Fale Conosco`.
  - Criado `404.php` estilizado e atualizado `index.php` com container e grid padronizados.
- **5. Compatibilidade com Páginas de Menu e Links de Cabeçalho/Rodapé (`inc/core/search-logic.php`, `template-imoveis.php`, `header.php`, `footer.php`, `style.css`)**:
  - Mapeamento automático das páginas do menu principal (`Todos os Imóveis`, `Lançamentos`, `Apartamentos`, `Casas em Condomínio`, `Imóveis Comerciais`, `Imóveis Rurais`, `Terrenos`, `Entrada Zero`, `Minha Casa Minha Vida`, `Subsídio CEHAP`) para `template-imoveis.php` com filtros pré-selecionados, e `/nosso-blog/` para `template-blog.php`.
  - Correção de todos os links `href="#"` no `header.php` e `footer.php`, filtro de URLs legadas `http://localhost/vinicius` e restrição responsiva de dimensões para `.custom-logo`.

#### **Versão 1.3.4**
- **1. Correção do CSS da Paginação (Eliminação de Background Branco Externo)**:
  - Corrigido o problema onde o elemento `ul.page-numbers` gerado pelo `paginate_links()` recebia regras genéricas de `.page-numbers`, exibindo um bloco com fundo branco retangular por fora dos botões.
  - Resetados containers (`.imob-pagination`, `ul.page-numbers`, `li`) com `background: transparent !important`, `border: none !important` e `box-shadow: none !important`, restringindo o background e bordas exclusivamente aos botões e links de navegação (`a.page-numbers` e `span.page-numbers`).
- **2. Esquema de Cores dos Ícones das Redes Sociais no Box do Corretor (`single-imovel.php`)**:
  - Alinhamento dos botões de redes sociais do corretor com a paleta dos botões de construtora/empreendimento.
  - Fundo dourado (`var(--accent-color)`), ícone branco e bordas arredondadas de `8px`.
  - No hover: fundo azul escuro (`var(--primary-color)`), elevação sutil e ícone branco mantido, corrigindo a anomalia em que o ícone ficava azul claro por herança de `a:hover`.
- **3. Padronização do Botão de Filtro da Barra Lateral (`single-imovel.php`)**:
  - Botão atualizado para seguir o padrão dos botões das construtoras (`.imob-btn-sidebar-filter`): fundo dourado, hover azul escuro, bordas arredondadas de `8px` e letras brancas.
  - Adicionado ícone de lupa (`<span class="material-symbols-outlined">search</span>`).
  - Tipografia padronizada em caixa alta (`MOSTRAR RESULTADOS` e `text-transform: uppercase`).

#### **Versão 1.3.3**
- **1. Separação dos Widgets de Empreendimento e Construtora na Sidebar (`single-imovel.php`)**:
  - Removido o widget conjunto anterior e seu título "Empreendimento & Construtora".
  - Criados dois blocos/widgets independentes na barra lateral (`.imob-sidebar-emp-widget` e `.imob-sidebar-const-widget`).
  - Hierarquia respeitada: se o imóvel tiver empreendimento vinculado, o widget do empreendimento é exibido em primeiro lugar, seguido pelo widget da construtora.
  - Cada widget conta com ícone temático (`domain` e `apartment`), nome em destaque e botão com link direto para a listagem completa de imóveis daquela relação.
- **2. Vínculo de Construtora no Cadastro de Empreendimentos (`empreendimento-meta.php`)**:
  - Nova metabox dedicada na coluna lateral direita (`side`, alta prioridade): **Construtora Responsável**.
  - Dropdown com todas as construtoras cadastradas, link de edição da construtora ativa, link para visualização pública e atalho para criar uma nova construtora.
  - Sincronização automática entre os campos e garantia de salvamento de `_imob_emp_construtora_id`.
- **3. Limpeza dos Badges de Topo na Página do Imóvel (`single-imovel.php`)**:
  - Removido o badge da construtora que aparecia no meio das categorias, estágios e tipos no cabeçalho do imóvel.
  - Mantido o badge de empreendimento e os links destacados e contextualizados na parte inferior do header (`.imob-imovel-relations-banner`) e na barra lateral.
- **4. Galeria de Fotos Interativa com Botões de Navegação no Empreendimento (`single-empreendimento.php`)**:
  - Reestruturação completa da seção de fotos: substituído o grid estático por um slider interativo de alta resolução com proporção elegante e background escuro.
  - **Botões de Navegação Anterior (`<`) e Próximo (`>`)**: botões flutuantes arredondados com ícones chevron, hover dourado interativo e suporte a teclado (setas esquerda e direita) e swipe em dispositivos móveis.
  - **Contador Dinâmico de Fotos**: badge indicativo no cabeçalho da seção exibindo a contagem da foto ativa (`1 / N`).
  - **Trilha de Miniaturas (Thumbnails)**: barra inferior com rolagem horizontal automática e borda dourada ativa na miniatura selecionada.
  - **Integração com GLightbox**: botão "Ampliar" e clique na imagem para visualização em tela cheia com zoom e loop.
- **5. Estilização dos Botões de Empreendimento e Construtora (Dourado & Azul Escuro)**:
  - Padronização visual dos botões de relacionamento nos boxes da barra lateral (`.imob-btn-relation`) e nos metaboxes administrativos (`.imob-meta-btn`, upload e busca no mapa).
  - Fundo dourado (`var(--accent-color)` / `#B2915A`) com borda correspondente.
  - Cantos arredondados ajustados com `border-radius: 8px` para um visual moderno e proporcional.
  - Efeito hover sofisticado alternando para azul escuro (`var(--primary-color)` / `#0E1A2B`) com letras brancas mantidas em ambos os estados.
- **6. Layout dos Filtros de Imóveis em 2 Colunas (`archive-imovel.php` e `template-imoveis.php`)**:
  - Reorganização dos campos de filtro em grade moderna de 2 colunas proporcionais (`.imob-filters-grid`), distribuindo tipo, localidade, modalidade e estágio da obra.
  - Alinhamento das ações à direita (`.imob-filters-actions`), posicionando o botão "Filtrar" e "Limpar Filtros" no canto inferior direito da barra.
  - Responsividade automática para telas menores (`<= 768px`) com coluna única.

#### **Versão 1.3.2**
- **1. Galeria de Imagens e Slideshow Completo (`single-imovel.php`)**:
  - Correção na galeria de fotos do imóvel: anteriormente, o loop de miniaturas limitava a renderização no DOM às primeiras imagens (`array_slice`), fazendo com que o GLightbox não carregasse as imagens subsequentes no slideshow.
  - Implementada a renderização de links ocultos (`.imob-gallery-hidden`) para todas as fotos excedentes pertencentes ao imóvel com a mesma classe e atributo `data-gallery="imovel-gallery"`.
  - Configurado o GLightbox com `loop: true`, `zoomable: true` e `touchNavigation: true`, permitindo navegar fluidamente por todas as dezenas de fotos cadastradas sem interrupções.
  - Adicionado `pointer-events: none` na sobreposição `+X fotos` para garantir o clique direto no elemento de abertura do lightbox.
- **2. Vínculo e Links de Construtora e Empreendimento (`single-imovel.php`)**:
  - Criados os helpers `imob_get_imovel_construtora()` e `imob_render_construtora_badge()` com detecção via meta direto e herança automática via empreendimento.
  - No cabeçalho do imóvel: exibição de badges com links para a listagem da construtora e do empreendimento (`imob-single-badge-construtora` e `imob-single-badge-empreendimento`), além do estágio da obra.
  - No corpo do imóvel: novo banner informativo (`.imob-imovel-relations-banner`) abaixo da barra de características com links destacados para a listagem de todos os imóveis da respectiva construtora e do respectivo empreendimento.
  - Na barra lateral (sidebar): novo widget dedicado (`.imob-sidebar-relations`) destacando o Empreendimento e a Construtora com links diretos para suas páginas de catálogo e listagem completa de unidades.

#### **Versão 1.3.1**
- **1. Correção de Erro de Validação de Formulário ao Salvar Imóveis (`An invalid form control with name='' is not focusable`)**:
  - Removido o atributo nativo HTML5 `required` dos inputs de texto nos modais de cadastro rápido de Construtora, Proprietário e Empreendimento (`#quick_const_nome`, `#quick_prop_nome`, `#quick_emp_nome`).
  - Implementado transporte dinâmico via JavaScript (`$('body').append(...)`) para anexar os modais diretamente ao `body`, desvinculando-os do formulário principal de submissão do WordPress (`#post`) e blindando a gravação de imóveis contra bloqueios nativos do navegador.
  - A validação de preenchimento permanece 100% ativa e segura via JavaScript no clique do botão de submissão rápida.
- **2. Correção e Dinamização dos Badges nos Empreendimentos (Estágio da Obra e Tipo de Imóvel com Links)**:
  - Eliminação da badge estática de "Lançamento" que aparecia indevidamente em todos os empreendimentos.
  - Criados os helpers universais `imob_get_empreendimento_estagio_data()`, `imob_get_empreendimento_tipo_data()` e `imob_render_empreendimento_badges()`:
    - **Estágio da Obra**: Lê dinamicamente os termos da taxonomia `estagio_obra` (com fallback inteligente para `_imob_emp_estagio`) e gera o badge com link direto para a listagem correspondente.
    - **Tipo de Imóvel**: Permite atribuir `tipo_imovel` diretamente ao empreendimento ou herda automaticamente os tipos das unidades cadastradas naquele empreendimento, gerando badges clicáveis direcionando para a listagem de cada tipo de imóvel.
  - Atualização dos templates `single-empreendimento.php`, `single-construtora.php` e criação de `archive-empreendimento.php`.
  - Sincronização automática bidirecional entre o select de estágio e a taxonomia `estagio_obra` no painel administrativo.

#### **Versão 1.3.0**
- **1. Padronização da Página de Blog (`/nosso-blog/` e `home.php`)**:
  - Unificação completa do design da página de blog (`home.php`, `template-blog.php`, `page-nosso-blog.php`) com a mesma estrutura visual e harmonia de cores estabelecida em `category.php`.
  - Cards elegantes com badges de categoria dourados com hover invertido, título, resumo legível, link "Ler artigo" dourado e data com ícone `calendar_today`.
- **2. Correção e Modernização do CSS da Paginação**:
  - Correção dos estilos da paginação em toda a listagem de imóveis (`archive-imovel.php`, `template-imoveis.php`, `taxonomy`).
  - Eliminação de marcadores padrão de lista (`list-style: none`) em `ul.page-numbers` e adição de botões arredondados, flexbox centralizado, sombras suaves, hover interativo e destaque ativo em dourado.
- **3. Carrossel de Construtoras para Elementor (`Construtoras_Widget`)**:
  - Reconstrução completa do widget do Elementor com carrossel dinâmico e interativo para exibir os logos das construtoras parceiras.
  - Links automáticos direcionando para a página individual de cada construtora (`single-construtora.php`).
  - Suporte a modo automático (lê CPT `construtora`) e itens manuais, com controles de autoplay, setas de navegação prev/next e transições suaves.
- **4. Destaque do Logo e Responsividade na Página da Construtora (`single-construtora.php`)**:
  - No desktop: logo da construtora ampliado (até 220px) posicionado à esquerda, com textos, dados de contato e botões alinhados fluidamente à esquerda.
  - No smartphone: tamanho da tipografia do título reduzido proporcionalmente (`1.75rem`), com empilhamento vertical limpo e sem estouro visual.
- **5. Taxonomia "Estágio da Obra" e Filtro Superior de Imóveis**:
  - Criação da taxonomia personalizada `estagio_obra` para imóveis e empreendimentos com termos padrão (*Lançamento*, *Em Construção*, *Pronto para Morar*, *Na Planta*).
  - Criação do template de arquivo `taxonomy-estagio_obra.php` com paginação e design integrado.
  - Adição do filtro suspenso por Estágio de Obra na barra superior de imóveis (`archive-imovel.php` e `template-imoveis.php`), com valor padrão "Todos" e integração à lógica de busca `pre_get_posts` em `search-logic.php`.
- **6. Ajuste de Taxonomias no Post Type Empreendimento e Itens de Lazer**:
  - Remoção das taxonomias `tipo_imovel` e `status_imovel` do CPT `empreendimento`.
  - Associação oficial da taxonomia `caracteristica` para registrar os itens de lazer e diferenciais do empreendimento.
  - Nova seção no template `single-empreendimento.php` renderizando os "Itens de Lazer & Diferenciais" com ícones e visual de destaque.
- **7. Marca d'Água e Redimensionamento de Fotos nos Empreendimentos**:
  - Integração da galeria de fotos de empreendimentos (`_imob_emp_galeria`) com o pipeline de processamento de imagens do tema:
    - Aplicação automática de marca d'água (`inc/core/watermark.php`) para uploads no CPT `empreendimento`.
    - Geração padronizada das resoluções otimizadas (1280px e 400x300 miniatura) e limpeza de resoluções redundantes.
    - Suporte completo no escaneamento em lote e proteção contra remoção como órfãs em `inc/core/image-optimizer.php`.

#### **Versão 1.2.0**
- **Links de Categoria e Novo Template de Categoria (`category.php` e `archive.php`)**:
  - Os badges de categorias no Grid de Posts agora são links navegáveis (`<a>`) que direcionam para o arquivo da respectiva categoria.
  - Criado o template `category.php` seguindo rigorosamente a identidade visual e o padrão de cards do Grid de Posts (badges dourados com hover invertido, data com ícone de calendário dourado, botão "Ler artigo" dourado e paginação moderna).
- **Página de Imóveis com Filtros Superiores Avançados (`archive-imovel.php` e `template-imoveis.php`)**:
  - Nova barra de filtros no topo com layout responsivo e moderno.
  - Filtros por:
    - **Tipo de Imóvel** (Apartamento, Casa, etc.)
    - **Localidade / Bairro** (Campina Grande e bairros cadastrados)
    - **Modalidade** (Comprar / Venda ou Alugar / Aluguel)
  - Botão de "Filtrar" com feedback visual e botão "Limpar" quando há filtros ativos.
  - Criação de `template-imoveis.php` e `page-imoveis.php` para uso como modelo de página no WordPress ou via URL `/imoveis/`.
  - Busca inteligente com fallback duplo por taxonomia e faixas de preço (`_imob_preco_venda` / `_imob_preco_aluguel`).
- **Páginas de Construtora e Empreendimento (`single-construtora.php` e `single-empreendimento.php`)**:
  - **Empreendimento**:
    - Novo template `single-empreendimento.php` com logo/imagem de destaque, estágio da obra (Lançamento, Em Construção, Pronto), previsão de entrega, endereço e vínculo com a Construtora.
    - Seção de **Galeria de Fotos** do empreendimento com grid expansível e upload nativo no painel administrativo.
    - Seção de **Localização em Mapa Interativo** com coordenadas e busca geográfica.
    - Listagem de todas as unidades/imóveis disponíveis vinculadas àquele empreendimento com paginação.
  - **Construtora**:
    - Exibição de logo, nome, dados de contato completos (telefone, WhatsApp, site, Instagram), catálogo de empreendimentos cadastrados e listagem de imóveis.
- **Badge do Empreendimento nos Grids e Página do Imóvel**:
  - Quando um imóvel estiver vinculado a um empreendimento, um badge exclusivo com ícone predial é renderizado automaticamente nos cards de todos os grids (`archive-imovel`, `template-imoveis`, `taxonomy`, widgets do Elementor) e no topo do `single-imovel.php`, com link direto para a página do empreendimento.
  - Criados os helpers universais `imob_get_imovel_empreendimento()` e `imob_render_empreendimento_badge()`.

#### **Versão 1.1.6**
- **Correção e Blindagem de Especificidade no Botão "Ler artigo" (`imob_blog_grid`)**:
  - **Resolução de Conflito com CSS Compilado do Elementor**: Em páginas onde o widget já havia sido salvo anteriormente, o Elementor gerava um seletor estático com `!important` e cor azul legada (`#2F80ED`).
  - **Injeção de Bloco Scoped Dinâmico**: Adicionado bloco `<style>` renderizado dinamicamente junto ao widget com os seletores exatos `.elementor-element-{{ID}}` e `div[data-id="{{ID}}"]`, garantindo prioridade imediata sobre folhas de estilo compiladas em cache.
  - **Super Especificidade no `style.css`**: Adicionadas regras ancoradas em `#page` e classes compostas do Elementor para sobrepor qualquer herança de especificidade.
  - **Remoção de Trava no Painel**: Removido o `!important` dos seletores do controle do Elementor para permitir futuras customizações limpas.

#### **Versão 1.1.5**
- **Harmonização Visual e Cores no Grid de Posts (`imob_blog_grid`)**:
  - **Badges de Categorias**:
    - Fundo dourado institucional (`var(--accent-color)` / `#B2915A`), borda dourada e tipografia 100% branca (`#FFFFFF`) em estado normal.
    - Efeito Hover com **inversão dinâmica**: fundo branco puro (`#FFFFFF`), borda dourada e letras douradas (`#B2915A`).
    - Remoção da classe conflitante `.badge-tipo` para garantir isolamento e estilo exclusivo aos badges do blog.
  - **Link "Ler artigo"**:
    - Ajustado para o mesmo tom institucional dourado (`#B2915A`), com transição suave no hover e controles atualizados no Elementor.
  - **Ícone da Data**:
    - O ícone `calendar_today` no rodapé do card foi atualizado para o tom de dourado (`#B2915A`), mantendo a identidade visual coesa.

#### **Versão 1.1.4**
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
