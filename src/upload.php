<?php
// src/upload.php

function upload_file(array $file, string $destination_dir, array $allowed_types = ['pdf'], int $max_size = 16777216): array {
    // 16MB default limit
    
    if (!isset($file['error']) || is_array($file['error'])) {
        return ['success' => false, 'error' => 'Parâmetros inválidos no envio de arquivo.'];
    }

    $errors = [
        UPLOAD_ERR_OK => 'Upload concluído com sucesso.',
        UPLOAD_ERR_INI_SIZE => 'O arquivo enviado excede a diretiva upload_max_filesize do php.ini.',
        UPLOAD_ERR_FORM_SIZE => 'O arquivo enviado excede a diretiva MAX_FILE_SIZE do formulário HTML.',
        UPLOAD_ERR_PARTIAL => 'O arquivo foi apenas parcialmente enviado.',
        UPLOAD_ERR_NO_FILE => 'Nenhum arquivo foi enviado.',
        UPLOAD_ERR_NO_TMP_DIR => 'Faltando pasta temporária.',
        UPLOAD_ERR_CANT_WRITE => 'Falha ao escrever o arquivo no disco.',
        UPLOAD_ERR_EXTENSION => 'Uma extensão do PHP interrompeu o upload do arquivo.',
    ];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'error' => $errors[$file['error']] ?? 'Erro desconhecido de upload.'];
    }

    if ($file['size'] > $max_size) {
        return ['success' => false, 'error' => 'O arquivo é muito grande. O limite é ' . ($max_size / 1048576) . 'MB.'];
    }

    // MIME Validation
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime_type = $finfo->file($file['tmp_name']);
    
    $mime_map = [
        'pdf' => 'application/pdf',
        'doc' => 'application/msword',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'xls' => 'application/vnd.ms-excel',
        'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'jpeg' => 'image/jpeg',
        'jpg' => 'image/jpeg',
        'png' => 'image/png',
        'gif' => 'image/gif',
        'webp' => 'image/webp'
    ];
    
    $valid_mimes = [];
    foreach ($allowed_types as $ext) {
        if (isset($mime_map[$ext])) {
            $valid_mimes[] = $mime_map[$ext];
        }
    }
    
    if (!in_array($mime_type, $valid_mimes, true)) {
        return ['success' => false, 'error' => 'Tipo de arquivo não permitido. Enviado: ' . $mime_type];
    }
    
    // Gerar nome seguro baseado na extensão que foi aceita
    $ext_final = array_search($mime_type, $mime_map);
    if (!$ext_final) {
        $ext_final = pathinfo($file['name'], PATHINFO_EXTENSION);
    }
    
    $new_filename = uniqid('arq_', true) . '.' . $ext_final;
    $target_path = rtrim($destination_dir, '/\\') . DIRECTORY_SEPARATOR . $new_filename;
    
    // Certificar que a pasta existe
    if (!is_dir($destination_dir)) {
        mkdir($destination_dir, 0755, true);
    }

    if (move_uploaded_file($file['tmp_name'], $target_path)) {
        return [
            'success' => true, 
            'filename' => $new_filename,
            'original_name' => $file['name'],
            'path' => $target_path,
            'size' => $file['size']
        ];
    }
    
    return ['success' => false, 'error' => 'Falha ao mover o arquivo para o destino.'];
}

function upload_image(array $file, string $destination_dir, int $max_size = 5242880): array {
    // 5MB default limit for images
    return upload_file($file, $destination_dir, ['jpeg', 'jpg', 'png', 'gif', 'webp'], $max_size);
}

function delete_file(string $filepath): bool {
    if (file_exists($filepath) && is_file($filepath)) {
        return unlink($filepath);
    }
    return false;
}
