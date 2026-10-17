<?php
// src/mail.php

function send_mail(string $to, string $subject, string $body, ?string $from_name = null, ?string $from_email = null): bool {
    $from_name = $from_name ?? SMTP_FROM_NAME;
    $from_email = $from_email ?? SMTP_FROM;
    
    // Limpeza para evitar Injeção de Cabeçalho
    $from_name = str_replace(["\r", "\n"], '', $from_name);
    $from_email = str_replace(["\r", "\n"], '', filter_var($from_email, FILTER_SANITIZE_EMAIL));
    $to = str_replace(["\r", "\n"], '', filter_var($to, FILTER_SANITIZE_EMAIL));
    $subject = str_replace(["\r", "\n"], '', $subject);
    
    $headers = [];
    $headers[] = 'MIME-Version: 1.0';
    $headers[] = 'Content-type: text/html; charset=utf-8';
    
    // Se o FROM nome existir, formata bonitinho
    if (!empty($from_name)) {
        $headers[] = sprintf('From: =?UTF-8?B?%s?= <%s>', base64_encode($from_name), $from_email);
    } else {
        $headers[] = sprintf('From: %s', $from_email);
    }
    
    $headers[] = sprintf('Reply-To: %s', $from_email);
    $headers[] = 'X-Mailer: PHP/' . phpversion();

    $header_string = implode("\r\n", $headers);
    
    // Opcional: Usar PHPMailer seria melhor, mas conforme regras: funcão nativa
    return mail($to, '=?UTF-8?B?' . base64_encode($subject) . '?=', $body, $header_string);
}

function send_contact_mail(string $nome, string $email, string $telefone, string $mensagem): bool {
    $to = CONTACT_EMAIL;
    $subject = 'Novo Contato pelo Site: ' . $nome;
    
    $html = sprintf('
        <h2>Novo Contato - Site Instituto Nacional de Políticas Públicas</h2>
        <p><strong>Nome:</strong> %s</p>
        <p><strong>E-mail:</strong> %s</p>
        <p><strong>Telefone:</strong> %s</p>
        <p><strong>Mensagem:</strong><br>%s</p>
        <hr>
        <p><small>Enviado em %s via IP %s</small></p>
    ', 
        e($nome), 
        e($email), 
        e($telefone), 
        nl2br(e($mensagem)),
        date('d/m/Y H:i:s'),
        $_SERVER['REMOTE_ADDR'] ?? 'Desconhecido'
    );
    
    return send_mail($to, $subject, $html, $nome, $email);
}
