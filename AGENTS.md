# Regras do Projeto (INPP - Instituto Nacional de Políticas Públicas)

## Git & Credenciais
- Conta GitHub padrão: `traffego`
- Configuração de credencial fixada no repositório local:
  `git config credential.https://github.com.username traffego`

## Padrão Visual Institucional (INPP)
Sempre que o contexto envolver o **Instituto Nacional de Políticas Públicas (INPP)**, seguir estritamente as diretrizes visuais abaixo:

### 1. Paleta de Cores Oficial (Adobe Color)
- **Azul Primário (`--color-primary`):** `#0A7ABF` — Botões de ação principal, links ativos, contornos de foco, elementos de destaque.
- **Azul Marinho Profundo (`--color-primary-dark`):** `#07598C` — Topbar/header, rodapé institucional, títulos escuros, estados `:hover`.
- **Azul Céu (`--color-primary-light`):** `#14A1D9` — Gradientes, ícones secundários, badges sutis, bordas ativas.
- **Verde INPP (`--color-accent` / `--color-success`):** `#268C3F` — Destaques especiais (extraído do ícone central da logo), badges positivas, botões de ação/inscrição.
- **Cinza Claro / Off-White (`--bg-body` / `--color-neutral-light`):** `#F2F2F2` — Fundo principal da página (modo claro), superfícies neutras.

### 2. Uso da Marca e Logotipo
- **Arquivo Oficial:** `assets/img/logo_inpp.png`
- **Integridade da Logo:**
  - NUNCA aplicar filtros monocromáticos destrutivos como `filter: brightness(0) invert(1)`.
  - A logo original possui cores próprias (azul, verde e tipografia).
  - Em cabeçalhos escuros (`#07598C`), deve ser exibida com fidelidade visual e respiro adequado.
  - Em modo escuro, garantir contraste sem alterar as cores nativas do arquivo.

### 3. Superfícies e Contraste
- Seguir WCAG AA (mínimo 4.5:1 para texto normal e 3:1 para títulos).
- Modo Claro: Fundo `#F2F2F2`, cartões brancos (`#FFFFFF`), textos principais em `#07598C` e `#1A202C`.
- Modo Escuro: Fundo e superfícies em tons de azul escuro/navy profundo (ex: `#0B1320`, `#111D30`), evitando preto puro `#000000`.

### 4. Tipografia Oficial
- **Família Tipográfica:** `Inter` (`'Inter', system-ui, -apple-system, sans-serif`) com suporte do corte óptico e pesos de 100 a 900.
- **Importação Google Fonts:**
  ```html
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
  ```
