<?php
require_once dirname(__DIR__) . '/src/config.php';
require_once ROOT_PATH . '/src/database.php';

echo "Iniciando migrações...\n";

// 1. tab_documentos_financeiro
try {
    $cols = db_fetch_all("SHOW COLUMNS FROM tab_documentos_financeiro LIKE 'habilitado'");
    if (empty($cols)) {
        db_execute("ALTER TABLE tab_documentos_financeiro ADD COLUMN habilitado TINYINT(1) NOT NULL DEFAULT 1");
        echo "tab_documentos_financeiro: coluna 'habilitado' adicionada.\n";
    } else {
        echo "tab_documentos_financeiro: coluna 'habilitado' já existe.\n";
    }
} catch (Throwable $e) {
    echo "Erro em tab_documentos_financeiro: " . $e->getMessage() . "\n";
}

// 2. tab_documentos_termo_colaboracao
try {
    $cols = db_fetch_all("SHOW COLUMNS FROM tab_documentos_termo_colaboracao LIKE 'habilitado'");
    if (empty($cols)) {
        db_execute("ALTER TABLE tab_documentos_termo_colaboracao ADD COLUMN habilitado TINYINT(1) NOT NULL DEFAULT 1");
        echo "tab_documentos_termo_colaboracao: coluna 'habilitado' adicionada.\n";
    } else {
        echo "tab_documentos_termo_colaboracao: coluna 'habilitado' já existe.\n";
    }
} catch (Throwable $e) {
    echo "Erro em tab_documentos_termo_colaboracao: " . $e->getMessage() . "\n";
}

// 3. tab_projetos_documentos
try {
    $cols = db_fetch_all("SHOW COLUMNS FROM tab_projetos_documentos LIKE 'habilitado'");
    if (empty($cols)) {
        db_execute("ALTER TABLE tab_projetos_documentos ADD COLUMN habilitado TINYINT(1) NOT NULL DEFAULT 1");
        echo "tab_projetos_documentos: coluna 'habilitado' adicionada.\n";
    } else {
        echo "tab_projetos_documentos: coluna 'habilitado' já existe.\n";
    }
} catch (Throwable $e) {
    echo "Erro em tab_projetos_documentos: " . $e->getMessage() . "\n";
}

// 4. Criar tab_transparencia_arquivos
try {
    db_execute("CREATE TABLE IF NOT EXISTS tab_transparencia_arquivos (
        id INT AUTO_INCREMENT PRIMARY KEY,
        tipo VARCHAR(50) NOT NULL,
        titulo VARCHAR(250) NOT NULL,
        arquivo VARCHAR(250) NOT NULL,
        data_documento DATE NULL,
        habilitado TINYINT(1) NOT NULL DEFAULT 1,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_tipo_hab (tipo, habilitado)
    )");
    echo "Tabela tab_transparencia_arquivos verificada/criada.\n";
} catch (Throwable $e) {
    echo "Erro ao criar tab_transparencia_arquivos: " . $e->getMessage() . "\n";
}

// 5. Semear arquivos existentes em tab_transparencia_arquivos
$pasta = UPLOAD_PATH . '/transparencia/';
if (is_dir($pasta)) {
    $arquivos_iniciais = [
        [
            'tipo'           => 'estatuto',
            'titulo'         => 'Estatuto Social Consolidado do INPP',
            'arquivo'        => 'estatuto.pdf',
            'data_documento' => '2012-05-10',
        ],
        [
            'tipo'           => 'dirigentes',
            'titulo'         => 'Ata Notarial de Eleição e Relação Oficial de Dirigentes',
            'arquivo'        => 'dirigentes.pdf',
            'data_documento' => '2024-01-15',
        ],
        [
            'tipo'           => 'regulamento',
            'titulo'         => 'Regulamento Próprio de Compras e Contratações',
            'arquivo'        => 'regulamento_compras.pdf',
            'data_documento' => '2024-01-15',
        ],
    ];

    // Adicionar arquivos do painel da pasta
    $scan = scandir($pasta);
    foreach ($scan as $arq) {
        if ($arq === '.' || $arq === '..' || in_array($arq, ['dirigentes.pdf', 'estatuto.pdf', 'regulamento_compras.pdf', '.gitkeep'])) continue;
        $ext = strtolower(pathinfo($arq, PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'xlsx', 'xls', 'pdf'])) continue;

        $dt = '2025-12-18';
        if (preg_match('/^(\d{4})_(\d{2})_(\d{2})_/', $arq, $m)) {
            $dt = "{$m[1]}-{$m[2]}-{$m[3]}";
        }

        $nome_limpo = preg_replace('/^\d{4}_\d{2}_\d{2}_/', '', pathinfo($arq, PATHINFO_FILENAME));
        $tit = ucwords(str_replace(['_', '-'], ' ', $nome_limpo));
        if (in_array($ext, ['jpg', 'jpeg', 'png'])) {
            $tit = 'Comprovante Visual de Transferência — ' . date('d/m/Y', strtotime($dt));
        } elseif (in_array($ext, ['xlsx', 'xls'])) {
            $tit = 'Planilha de Repasses & Transferências — ' . date('d/m/Y', strtotime($dt));
        }

        $arquivos_iniciais[] = [
            'tipo'           => 'painel',
            'titulo'         => $tit,
            'arquivo'        => $arq,
            'data_documento' => $dt,
        ];
    }

    foreach ($arquivos_iniciais as $item) {
        $existe = db_fetch("SELECT id FROM tab_transparencia_arquivos WHERE arquivo = ? LIMIT 1", [$item['arquivo']]);
        if (!$existe && file_exists($pasta . $item['arquivo'])) {
            db_insert('tab_transparencia_arquivos', [
                'tipo'           => $item['tipo'],
                'titulo'         => $item['titulo'],
                'arquivo'        => $item['arquivo'],
                'data_documento' => $item['data_documento'],
                'habilitado'     => 1,
            ]);
            echo "Arquivo semeado em tab_transparencia_arquivos: {$item['arquivo']} ({$item['tipo']})\n";
        }
    }
}

echo "Migrações concluídas com sucesso.\n";
