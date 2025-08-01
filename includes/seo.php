<?php
/**
 * Configurações SEO centralizadas para o Clube de Vantagens ANETI
 */

// Configurações base do site
$site_config = [
    'site_name' => 'Clube de Vantagens ANETI',
    'site_url' => (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'],
    'default_image' => '/assets/images/logo-aneti-social.png',
    'facebook_app_id' => '',
    'twitter_handle' => '@aneti',
    'organization_name' => 'ANETI - Associação Nacional dos Especialistas em Tecnologia da Informação',
    'contact_email' => 'contato@aneti.org.br',
    'phone' => '(61) 93618-0637'
];

// Função para gerar meta tags SEO
function generateSEOTags($page_config) {
    global $site_config;
    
    // Configurações padrão
    $title = $page_config['title'] ?? 'Clube de Vantagens ANETI | Benefícios Exclusivos para Especialistas em TI';
    $description = $page_config['description'] ?? 'Aproveite descontos e condições especiais em dezenas de empresas parceiras. O Clube de Vantagens ANETI é exclusivo para profissionais de TI associados à maior comunidade do Brasil.';
    $keywords = $page_config['keywords'] ?? 'clube de vantagens ANETI, benefícios para TI, descontos tecnologia, clube aneti, associação ANETI, vantagens associados, tech perks, parceiros aneti';
    $canonical = $page_config['canonical'] ?? $site_config['site_url'] . $_SERVER['REQUEST_URI'];
    $image = $page_config['image'] ?? $site_config['site_url'] . $site_config['default_image'];
    $type = $page_config['type'] ?? 'website';
    
    // Remove query parameters from canonical URL
    $canonical = strtok($canonical, '?');
    
    echo "<!-- SEO Meta Tags -->\n";
    echo "<title>" . htmlspecialchars($title) . "</title>\n";
    echo "<meta name=\"description\" content=\"" . htmlspecialchars($description) . "\">\n";
    echo "<meta name=\"keywords\" content=\"" . htmlspecialchars($keywords) . "\">\n";
    echo "<meta name=\"author\" content=\"" . htmlspecialchars($site_config['organization_name']) . "\">\n";
    echo "<meta name=\"robots\" content=\"index, follow\">\n";
    echo "<link rel=\"canonical\" href=\"" . htmlspecialchars($canonical) . "\">\n";
    
    // Open Graph Tags
    echo "\n<!-- Open Graph Meta Tags -->\n";
    echo "<meta property=\"og:title\" content=\"" . htmlspecialchars($title) . "\">\n";
    echo "<meta property=\"og:description\" content=\"" . htmlspecialchars($description) . "\">\n";
    echo "<meta property=\"og:type\" content=\"" . htmlspecialchars($type) . "\">\n";
    echo "<meta property=\"og:url\" content=\"" . htmlspecialchars($canonical) . "\">\n";
    echo "<meta property=\"og:image\" content=\"" . htmlspecialchars($image) . "\">\n";
    echo "<meta property=\"og:image:width\" content=\"1920\">\n";
    echo "<meta property=\"og:image:height\" content=\"1080\">\n";
    echo "<meta property=\"og:image:alt\" content=\"Clube de Benefícios ANETI - Vantagens Exclusivas para Profissionais de TI\">\n";
    echo "<meta property=\"og:site_name\" content=\"" . htmlspecialchars($site_config['site_name']) . "\">\n";
    echo "<meta property=\"og:locale\" content=\"pt_BR\">\n";
    
    if (!empty($site_config['facebook_app_id'])) {
        echo "<meta property=\"fb:app_id\" content=\"" . htmlspecialchars($site_config['facebook_app_id']) . "\">\n";
    }
    
    // Twitter Card Tags
    echo "\n<!-- Twitter Card Meta Tags -->\n";
    echo "<meta name=\"twitter:card\" content=\"summary_large_image\">\n";
    echo "<meta name=\"twitter:title\" content=\"" . htmlspecialchars($title) . "\">\n";
    echo "<meta name=\"twitter:description\" content=\"" . htmlspecialchars($description) . "\">\n";
    echo "<meta name=\"twitter:image\" content=\"" . htmlspecialchars($image) . "\">\n";
    echo "<meta name=\"twitter:image:alt\" content=\"Clube de Benefícios ANETI - Vantagens Exclusivas para Profissionais de TI\">\n";
    echo "<meta name=\"twitter:url\" content=\"" . htmlspecialchars($canonical) . "\">\n";
    
    if (!empty($site_config['twitter_handle'])) {
        echo "<meta name=\"twitter:site\" content=\"" . htmlspecialchars($site_config['twitter_handle']) . "\">\n";
        echo "<meta name=\"twitter:creator\" content=\"" . htmlspecialchars($site_config['twitter_handle']) . "\">\n";
    }
    
    // Additional meta tags
    echo "\n<!-- Additional SEO Meta Tags -->\n";
    echo "<meta name=\"theme-color\" content=\"#012d6a\">\n";
    echo "<meta name=\"msapplication-TileColor\" content=\"#012d6a\">\n";
    echo "<link rel=\"icon\" type=\"image/x-icon\" href=\"/favicon.ico\">\n";
    echo "<link rel=\"icon\" type=\"image/png\" sizes=\"32x32\" href=\"/assets/images/favicon.png\">\n";
    echo "<link rel=\"apple-touch-icon\" sizes=\"180x180\" href=\"/assets/images/favicon.png\">\n";
    echo "<meta name=\"msapplication-TileImage\" content=\"/assets/images/favicon.png\">\n";
    
    // Language and geographical targeting
    echo "<meta name=\"language\" content=\"Portuguese\">\n";
    echo "<meta name=\"geo.region\" content=\"BR\">\n";
    echo "<meta name=\"geo.country\" content=\"Brazil\">\n";
}

// Função para gerar Schema.org JSON-LD
function generateSchemaOrg($page_config) {
    global $site_config;
    
    $schema = [
        "@context" => "https://schema.org",
        "@graph" => []
    ];
    
    // Organization Schema
    $organization = [
        "@type" => "Organization",
        "@id" => $site_config['site_url'] . "/#organization",
        "name" => $site_config['organization_name'],
        "alternateName" => "ANETI",
        "url" => $site_config['site_url'],
        "email" => $site_config['contact_email'],
        "telephone" => $site_config['phone'],
        "address" => [
            "@type" => "PostalAddress",
            "addressCountry" => "BR",
            "addressRegion" => "DF",
            "addressLocality" => "Brasília"
        ],
        "sameAs" => [
            "https://www.facebook.com/aneti",
            "https://www.linkedin.com/company/aneti",
            "https://www.instagram.com/aneti"
        ]
    ];
    
    // Website Schema
    $website = [
        "@type" => "WebSite",
        "@id" => $site_config['site_url'] . "/#website",
        "url" => $site_config['site_url'],
        "name" => $site_config['site_name'],
        "description" => $page_config['description'] ?? 'Clube de vantagens exclusivo para profissionais de TI',
        "publisher" => [
            "@id" => $site_config['site_url'] . "/#organization"
        ],
        "inLanguage" => "pt-BR"
    ];
    
    // Add search action for main page
    if (isset($page_config['is_homepage']) && $page_config['is_homepage']) {
        $website["potentialAction"] = [
            "@type" => "SearchAction",
            "target" => [
                "@type" => "EntryPoint",
                "urlTemplate" => $site_config['site_url'] . "/public/categorias.php?search={search_term_string}"
            ],
            "query-input" => "required name=search_term_string"
        ];
    }
    
    // WebPage Schema
    $webpage = [
        "@type" => "WebPage",
        "@id" => ($page_config['canonical'] ?? $site_config['site_url'] . $_SERVER['REQUEST_URI']) . "#webpage",
        "url" => $page_config['canonical'] ?? $site_config['site_url'] . $_SERVER['REQUEST_URI'],
        "name" => $page_config['title'] ?? $site_config['site_name'],
        "description" => $page_config['description'] ?? '',
        "isPartOf" => [
            "@id" => $site_config['site_url'] . "/#website"
        ],
        "about" => [
            "@id" => $site_config['site_url'] . "/#organization"
        ],
        "inLanguage" => "pt-BR"
    ];
    
    $schema["@graph"] = [$organization, $website, $webpage];
    
    // Add specific schemas based on page type
    if (isset($page_config['type'])) {
        switch ($page_config['type']) {
            case 'company':
                if (isset($page_config['company_data'])) {
                    $company = $page_config['company_data'];
                    $localBusiness = [
                        "@type" => "LocalBusiness",
                        "name" => $company['nome'],
                        "description" => $company['descricao'] ?? '',
                        "url" => $company['website'] ?? '',
                        "address" => [
                            "@type" => "PostalAddress",
                            "addressLocality" => $company['cidade'] ?? '',
                            "addressRegion" => $company['estado'] ?? '',
                            "addressCountry" => "BR"
                        ]
                    ];
                    
                    if (isset($company['rating']) && $company['rating_count']) {
                        $localBusiness["aggregateRating"] = [
                            "@type" => "AggregateRating",
                            "ratingValue" => $company['rating'],
                            "reviewCount" => $company['rating_count']
                        ];
                    }
                    
                    $schema["@graph"][] = $localBusiness;
                }
                break;
                
            case 'category':
                if (isset($page_config['category_data'])) {
                    $category = $page_config['category_data'];
                    $collectionPage = [
                        "@type" => "CollectionPage",
                        "name" => "Empresas de " . $category['nome'],
                        "description" => "Empresas parceiras da categoria " . $category['nome'] . " no Clube de Vantagens ANETI"
                    ];
                    $schema["@graph"][] = $collectionPage;
                }
                break;
        }
    }
    
    echo "\n<!-- Schema.org JSON-LD -->\n";
    echo "<script type=\"application/ld+json\">\n";
    echo json_encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    echo "\n</script>\n";
}

// Configurações específicas de páginas
$page_configs = [
    'homepage' => [
        'title' => 'Clube de Vantagens ANETI | Benefícios Exclusivos para Especialistas em TI',
        'description' => 'Aproveite descontos e condições especiais em dezenas de empresas parceiras. O Clube de Vantagens ANETI é exclusivo para profissionais de TI associados à maior comunidade do Brasil.',
        'keywords' => 'clube de vantagens ANETI, benefícios para TI, descontos tecnologia, clube aneti, associação ANETI, vantagens associados, tech perks, parceiros aneti',
        'type' => 'website',
        'is_homepage' => true
    ],
    'categorias' => [
        'title' => 'Empresas Parceiras | Clube de Vantagens ANETI',
        'description' => 'Explore todas as empresas parceiras do Clube de Vantagens ANETI. Encontre descontos exclusivos em tecnologia, alimentação, saúde, educação e muito mais.',
        'keywords' => 'empresas parceiras ANETI, descontos exclusivos, categorias benefícios, parceiros clube ANETI',
        'type' => 'website'
    ],
    'empresa-detalhes' => [
        'title' => '%s | Clube de Vantagens ANETI',
        'description' => 'Conheça os benefícios exclusivos da %s para membros ANETI. Descontos especiais e condições diferenciadas para profissionais de TI.',
        'keywords' => 'benefícios %s, desconto %s ANETI, parceiro clube ANETI',
        'type' => 'company'
    ],
    'login' => [
        'title' => 'Entrar | Clube de Vantagens ANETI',
        'description' => 'Acesse sua conta no Clube de Vantagens ANETI e aproveite todos os benefícios exclusivos para membros da associação.',
        'keywords' => 'login clube ANETI, entrar clube vantagens, acesso benefícios ANETI',
        'type' => 'website'
    ],
    'cadastro' => [
        'title' => 'Seja um Parceiro | Clube de Vantagens ANETI',
        'description' => 'Cadastre sua empresa como parceira do Clube de Vantagens ANETI e alcance mais de 1.800 profissionais de TI qualificados.',
        'keywords' => 'parceiro ANETI, cadastro empresa, clube vantagens parceiro, rede parceiros ANETI',
        'type' => 'website'
    ]
];

// Função para obter configuração da página atual
function getCurrentPageConfig() {
    global $page_configs;
    
    $current_page = basename($_SERVER['PHP_SELF'], '.php');
    $page_config = $page_configs[$current_page] ?? $page_configs['homepage'];
    
    return $page_config;
}

// Função para definir configuração customizada da página
function setPageConfig($config) {
    global $custom_page_config;
    $custom_page_config = $config;
}

// Função para renderizar SEO completo
function renderSEO($custom_config = null) {
    global $custom_page_config;
    
    $page_config = $custom_config ?? $custom_page_config ?? getCurrentPageConfig();
    
    generateSEOTags($page_config);
    generateSchemaOrg($page_config);
}

// Função para URLs amigáveis
function friendlyURL($text) {
    $text = strtolower($text);
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    $text = preg_replace('/[\s-]+/', '-', $text);
    $text = trim($text, '-');
    return $text;
}

// Função para breadcrumbs estruturados
function generateBreadcrumbs($items) {
    global $site_config;
    
    $breadcrumbs = [
        "@context" => "https://schema.org",
        "@type" => "BreadcrumbList",
        "itemListElement" => []
    ];
    
    foreach ($items as $index => $item) {
        $breadcrumbs["itemListElement"][] = [
            "@type" => "ListItem",
            "position" => $index + 1,
            "name" => $item['name'],
            "item" => $item['url']
        ];
    }
    
    echo "\n<!-- Breadcrumbs Schema -->\n";
    echo "<script type=\"application/ld+json\">\n";
    echo json_encode($breadcrumbs, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    echo "\n</script>\n";
}
?>