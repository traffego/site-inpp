/**
 * Instituto Nacional de Políticas Públicas
 * JavaScript principal — ES6+ Vanilla, sem jQuery
 */

'use strict';

document.addEventListener('DOMContentLoaded', () => {

    // =============================================
    // MENU MOBILE
    // =============================================
    const menuToggle = document.getElementById('mobile-menu-toggle');
    const mobileMenu = document.getElementById('mobile-menu');
    const menuOverlay = document.getElementById('menu-overlay');

    function fecharMenu() {
        if (mobileMenu) mobileMenu.classList.remove('active');
        if (menuToggle) menuToggle.classList.remove('active');
        if (menuOverlay) menuOverlay.classList.remove('active');
        document.body.classList.remove('menu-open');
    }

    if (menuToggle && mobileMenu) {
        menuToggle.addEventListener('click', () => {
            const aberto = mobileMenu.classList.toggle('active');
            menuToggle.classList.toggle('active', aberto);
            if (menuOverlay) menuOverlay.classList.toggle('active', aberto);
            document.body.classList.toggle('menu-open', aberto);
            menuToggle.setAttribute('aria-expanded', String(aberto));
        });

        // Fechar ao clicar em link do menu
        mobileMenu.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', fecharMenu);
        });
    }

    if (menuOverlay) {
        menuOverlay.addEventListener('click', fecharMenu);
    }

    // Fechar com tecla Escape
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') fecharMenu();
    });

    // =============================================
    // DROPDOWN DE TRANSPARÊNCIA (NAV DESKTOP)
    // =============================================
    document.querySelectorAll('.nav-dropdown').forEach(dropdown => {
        const btn = dropdown.querySelector('.nav-dropdown-btn');
        const menu = dropdown.querySelector('.dropdown-menu');

        if (!btn || !menu) return;

        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            const aberto = menu.classList.toggle('active');
            btn.setAttribute('aria-expanded', String(aberto));
        });

        btn.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                btn.click();
            }
            if (e.key === 'Escape') {
                menu.classList.remove('active');
                btn.setAttribute('aria-expanded', 'false');
            }
        });
    });

    // Fechar dropdowns ao clicar fora
    document.addEventListener('click', () => {
        document.querySelectorAll('.dropdown-menu.active').forEach(m => {
            m.classList.remove('active');
            const btn = m.previousElementSibling;
            if (btn) btn.setAttribute('aria-expanded', 'false');
        });
    });


    // =============================================
    // SELECT DINÂMICO: ESTADO → CIDADE
    // =============================================
    function iniciarSelectCidades() {
        document.querySelectorAll('[data-estado-select]').forEach(selectEstado => {
            const alvoId = selectEstado.dataset.estadoSelect;
            const selectCidade = document.querySelector(`[data-cidade-select="${alvoId}"]`)
                               || document.getElementById(alvoId);

            if (!selectCidade) return;

            async function carregarCidades(uf) {
                selectCidade.disabled = true;
                selectCidade.innerHTML = '<option value="">Carregando...</option>';

                if (!uf) {
                    selectCidade.innerHTML = '<option value="">Selecione o estado primeiro</option>';
                    selectCidade.disabled = true;
                    return;
                }

                try {
                    const res = await fetch(`/api/cidades.php?uf=${encodeURIComponent(uf)}`);
                    if (!res.ok) throw new Error('Erro na requisição');
                    const cidades = await res.json();

                    selectCidade.innerHTML = '<option value="">Selecione a cidade</option>';
                    cidades.forEach(c => {
                        const opt = document.createElement('option');
                        opt.value = c.nome;
                        opt.textContent = c.nome;
                        // Restaurar valor antigo se houver (repopulação de form)
                        if (selectCidade.dataset.valorAntigo === c.nome) {
                            opt.selected = true;
                        }
                        selectCidade.appendChild(opt);
                    });
                    selectCidade.disabled = false;
                } catch {
                    selectCidade.innerHTML = '<option value="">Erro ao carregar cidades</option>';
                    selectCidade.disabled = true;
                }
            }

            selectEstado.addEventListener('change', () => {
                carregarCidades(selectEstado.value);
            });

            // Carregar automaticamente se estado já estiver selecionado (repopulação)
            if (selectEstado.value) {
                carregarCidades(selectEstado.value);
            }
        });
    }

    iniciarSelectCidades();

    // =============================================
    // SELECT DINÂMICO: PROJETO → FUNÇÃO
    // =============================================
    function iniciarSelectFuncoes() {
        const selectProjeto = document.getElementById('projeto') 
                            || document.querySelector('[data-projeto-select]');
        const selectFuncao  = document.getElementById('funcao')
                            || document.querySelector('[data-funcao-select]');

        if (!selectProjeto || !selectFuncao) return;

        async function carregarFuncoes(projetoId) {
            selectFuncao.disabled = true;
            selectFuncao.innerHTML = '<option value="">Carregando funções...</option>';

            if (!projetoId) {
                selectFuncao.innerHTML = '<option value="">Selecione um projeto primeiro</option>';
                return;
            }

            try {
                const baseUrl = window.location.origin;
                const pathPrefix = window.location.pathname.substring(0, window.location.pathname.lastIndexOf('/'));
                const apiPath = `${baseUrl}${pathPrefix}/api/funcoes-projeto.php`.replace(/([^:]\/)\/+/g, "$1");
                const res = await fetch(`${apiPath}?projeto_id=${encodeURIComponent(projetoId)}`);
                if (!res.ok) throw new Error('Erro');
                const funcoes = await res.json();

                selectFuncao.innerHTML = '<option value="">Selecione a função/cargo</option>';
                if (funcoes.length === 0) {
                    selectFuncao.innerHTML = '<option value="">Nenhuma função disponível</option>';
                    return;
                }
                funcoes.forEach(f => {
                    const opt = document.createElement('option');
                    opt.value = f.id;
                    opt.textContent = f.funcao;
                    selectFuncao.appendChild(opt);
                });
                selectFuncao.disabled = false;
            } catch {
                selectFuncao.innerHTML = '<option value="">Erro ao carregar funções</option>';
            }
        }

        selectProjeto.addEventListener('change', () => {
            carregarFuncoes(selectProjeto.value);
        });

        if (selectProjeto.value) {
            carregarFuncoes(selectProjeto.value, selectFuncao.getAttribute('data-valor-antigo'));
        }
    }

    iniciarSelectFuncoes();

    // =============================================
    // VALIDAÇÃO CLIENT-SIDE E PRESERVAÇÃO DE CAMPOS
    // =============================================
    function iniciarValidacaoFormularios() {
        const formularios = document.querySelectorAll('#form-curriculo, #form-fornecedor, #form-contato');
        
        formularios.forEach(form => {
            form.addEventListener('submit', function(e) {
                const containerErros = document.getElementById('js-form-errors');
                const listaErros = document.getElementById('js-errors-list');
                const erros = [];

                // Remover destaques anteriores
                form.querySelectorAll('.has-error').forEach(el => el.classList.remove('has-error'));

                // Validação de campos obrigatórios
                form.querySelectorAll('[required]').forEach(campo => {
                    // Se for campo de arquivo PDF com temporario preservado, ignorar
                    if (campo.type === 'file' && form.querySelector('input[name="temp_pdf_path"]')) {
                        return;
                    }

                    if (!campo.value || campo.value.trim() === '') {
                        campo.classList.add('has-error');
                        const label = form.querySelector(`label[for="${campo.id}"]`);
                        const nomeCampo = label ? label.textContent.replace('*', '').trim() : 'Campo obrigatório';
                        erros.push(`${nomeCampo} é de preenchimento obrigatório.`);
                    } else if (campo.type === 'email' && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(campo.value)) {
                        campo.classList.add('has-error');
                        erros.push('Por favor, informe um e-mail válido.');
                    }
                });

                if (erros.length > 0) {
                    e.preventDefault();

                    if (containerErros && listaErros) {
                        listaErros.innerHTML = erros.map(msg => `<li>${msg}</li>`).join('');
                        containerErros.style.display = 'block';
                        containerErros.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    } else {
                        alert('Por favor, preencha todos os campos obrigatórios antes de enviar.');
                    }

                    const primeiroErro = form.querySelector('.has-error');
                    if (primeiroErro) primeiroErro.focus();
                } else {
                    if (containerErros) containerErros.style.display = 'none';
                    
                    // Exibir estado de carregamento no botão de envio
                    const btnSubmit = form.querySelector('button[type="submit"]');
                    if (btnSubmit) {
                        btnSubmit.disabled = true;
                        btnSubmit.style.opacity = '0.75';
                        btnSubmit.innerHTML = `<span class="btn-spinner" style="display:inline-block; width:16px; height:16px; border:2px solid currentColor; border-top-color:transparent; border-radius:50%; animation:spin 0.6s linear infinite; margin-right:8px; vertical-align:middle;"></span> Enviando dados, aguarde...`;
                    }
                }
            });
        });
    }

    iniciarValidacaoFormularios();

    // =============================================
    // CAMPOS CONDICIONAIS (RADIO → MOSTRAR/OCULTAR)
    // =============================================
    function iniciarCamposCondicionais() {
        // idioma_sim_nao → bloco-idiomas
        const idiomaRadios = document.querySelectorAll('input[name="idioma_sim_nao"]');
        const blocoIdiomas = document.getElementById('bloco-idiomas');

        if (idiomaRadios.length && blocoIdiomas) {
            function toggleIdiomas() {
                const selecionado = document.querySelector('input[name="idioma_sim_nao"]:checked');
                const visivel = selecionado && selecionado.value === '1';
                blocoIdiomas.style.display = visivel ? 'block' : 'none';
                if (!visivel) {
                    const input = blocoIdiomas.querySelector('input, textarea');
                    if (input) input.value = '';
                }
            }
            idiomaRadios.forEach(r => r.addEventListener('change', toggleIdiomas));
            toggleIdiomas();
        }

        // experiencia_sim_nao → bloco-experiencias
        const expRadios = document.querySelectorAll('input[name="experiencia_sim_nao"]');
        const blocoExp  = document.getElementById('bloco-experiencias');

        if (expRadios.length && blocoExp) {
            function toggleExperiencias() {
                const selecionado = document.querySelector('input[name="experiencia_sim_nao"]:checked');
                const visivel = selecionado && selecionado.value === '1';
                blocoExp.style.display = visivel ? 'block' : 'none';
            }
            expRadios.forEach(r => r.addEventListener('change', toggleExperiencias));
            toggleExperiencias();
        }
    }

    iniciarCamposCondicionais();

    // =============================================
    // MÁSCARAS DE INPUT
    // =============================================
    function aplicarMascara(input, mascara) {
        input.addEventListener('input', function () {
            let val = this.value.replace(/\D/g, '');
            let resultado = '';
            let i = 0;

            for (let j = 0; j < mascara.length && i < val.length; j++) {
                if (mascara[j] === 'X') {
                    resultado += val[i++];
                } else {
                    resultado += mascara[j];
                    if (val[i] === mascara[j]) i++;
                }
            }
            this.value = resultado;
        });
    }

    document.querySelectorAll('input[data-mask]').forEach(input => {
        const tipo = input.dataset.mask;
        const mascaras = {
            cnpj:     'XX.XXX.XXX/XXXX-XX',
            cpf:      'XXX.XXX.XXX-XX',
            cep:      'XXXXX-XXX',
            telefone: '(XX) XXXXX-XXXX',
        };
        if (mascaras[tipo]) aplicarMascara(input, mascaras[tipo]);
    });

    // =============================================
    // ACCORDION / COLLAPSE (Editais)
    // =============================================
    document.querySelectorAll('.accordion-header').forEach(header => {
        header.addEventListener('click', () => {
            const body   = header.nextElementSibling;
            const item   = header.parentElement;
            const aberto = item.classList.contains('open');

            // Fechar todos
            document.querySelectorAll('.accordion-item.open').forEach(el => {
                el.classList.remove('open');
                const b = el.querySelector('.accordion-body');
                if (b) { b.style.maxHeight = null; b.style.opacity = '0'; }
            });

            // Abrir o clicado (se estava fechado)
            if (!aberto && body) {
                item.classList.add('open');
                body.style.maxHeight = body.scrollHeight + 'px';
                body.style.opacity   = '1';
            }

            header.setAttribute('aria-expanded', String(!aberto));
        });
    });

    // =============================================
    // VALIDAÇÃO DE ARQUIVO (Upload)
    // =============================================
    document.querySelectorAll('input[type="file"][data-accept]').forEach(fileInput => {
        const aceitos   = (fileInput.dataset.accept || 'pdf').split(',').map(e => e.trim().toLowerCase());
        const maxMB     = parseInt(fileInput.dataset.maxMb || '16');
        const maxBytes  = maxMB * 1024 * 1024;
        const nomeAlvo  = fileInput.dataset.nomeAlvo;
        const nomeSpan  = nomeAlvo ? document.querySelector(nomeAlvo) : null;
        let   erroEl    = fileInput.parentElement?.querySelector('.file-error');

        if (!erroEl) {
            erroEl = document.createElement('span');
            erroEl.className = 'file-error text-error';
            fileInput.parentElement?.appendChild(erroEl);
        }

        fileInput.addEventListener('change', function () {
            erroEl.textContent = '';
            const arquivo = this.files[0];
            if (!arquivo) return;

            const ext = arquivo.name.split('.').pop().toLowerCase();
            if (!aceitos.includes(ext)) {
                erroEl.textContent = `Tipo inválido. Aceito: ${aceitos.join(', ').toUpperCase()}`;
                this.value = '';
                if (nomeSpan) nomeSpan.textContent = 'Nenhum arquivo selecionado';
                return;
            }

            if (arquivo.size > maxBytes) {
                erroEl.textContent = `Arquivo muito grande. Máximo: ${maxMB}MB`;
                this.value = '';
                if (nomeSpan) nomeSpan.textContent = 'Nenhum arquivo selecionado';
                return;
            }

            if (nomeSpan) nomeSpan.textContent = arquivo.name;
        });
    });

    // =============================================
    // FADE-IN AO ROLAR (Intersection Observer)
    // =============================================
    const observador = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                observador.unobserve(entry.target);
            }
        });
    }, { threshold: 0.12 });

    document.querySelectorAll('.fade-in-up, .fade-in').forEach(el => {
        observador.observe(el);
    });

    // =============================================
    // CONFIRMAR EXCLUSÃO (Admin)
    // =============================================
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('[data-confirm]');
        if (!btn) return;
        const mensagem = btn.dataset.confirm || 'Tem certeza que deseja excluir?';
        if (!confirm(mensagem)) {
            e.preventDefault();
            e.stopPropagation();
        }
    });

    // =============================================
    // PREVIEW DE IMAGEM (Admin uploads)
    // =============================================
    document.querySelectorAll('input[type="file"][data-preview]').forEach(input => {
        const seletor = input.dataset.preview;
        const img     = document.querySelector(seletor);
        if (!img) return;

        input.addEventListener('change', function () {
            const arquivo = this.files[0];
            if (!arquivo) return;

            const tipos = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            if (!tipos.includes(arquivo.type)) return;

            const reader = new FileReader();
            reader.onload = (e) => {
                img.src = e.target.result;
                img.style.display = 'block';
            };
            reader.readAsDataURL(arquivo);
        });
    });

    // =============================================
    // ADMIN: SIDEBAR MOBILE TOGGLE
    // =============================================
    const sidebarToggle  = document.getElementById('sidebar-toggle');
    const adminSidebar   = document.getElementById('admin-sidebar');
    const sidebarOverlay = document.getElementById('sidebar-overlay');

    if (sidebarToggle && adminSidebar) {
        sidebarToggle.addEventListener('click', () => {
            const aberto = document.body.classList.toggle('sidebar-open');
            adminSidebar.classList.toggle('open', aberto);
            if (sidebarOverlay) sidebarOverlay.classList.toggle('active', aberto);
        });

        if (sidebarOverlay) {
            sidebarOverlay.addEventListener('click', () => {
                document.body.classList.remove('sidebar-open');
                adminSidebar.classList.remove('open');
                sidebarOverlay.classList.remove('active');
            });
        }
    }

    // =============================================
    // BACK TO TOP
    // =============================================
    const backToTop = document.getElementById('back-to-top');
    if (backToTop) {
        const verificarScroll = () => {
            backToTop.classList.toggle('visible', window.scrollY > 300);
        };
        window.addEventListener('scroll', verificarScroll, { passive: true });
        backToTop.addEventListener('click', () => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
        verificarScroll();
    }

    // =============================================
    // AUTO-DISMISS FLASH MESSAGES
    // =============================================
    document.querySelectorAll('.flash-message').forEach(el => {
        setTimeout(() => {
            el.style.transition = 'opacity 0.4s ease';
            el.style.opacity    = '0';
            setTimeout(() => el.remove(), 400);
        }, 5000);
    });

    // =============================================
    // GLIGHTBOX INIT
    // =============================================
    if (typeof GLightbox !== 'undefined') {
        GLightbox({
            selector:    '.glightbox',
            openEffect:  'zoom',
            closeEffect: 'fade',
            touchNavigation: true,
            loop: true,
        });
    }

    // =============================================
    // TINYMCE INIT (Admin)
    // =============================================
    if (typeof tinymce !== 'undefined') {
        tinymce.init({
            selector:  '.tinymce-editor',
            language:  'pt_BR',
            height:    420,
            menubar:   false,
            plugins:   'lists link image table code wordcount',
            toolbar:   'undo redo | bold italic underline | alignleft aligncenter alignright | bullist numlist | link image | table | code',
            content_style: 'body { font-family: Inter, sans-serif; font-size: 15px; line-height: 1.6; }',
            branding:  false,
        });
    }

    // =============================================
    // SUBMENUS DO ADMIN (ACORDEÃO SIDEBAR)
    // =============================================
    document.querySelectorAll('.sidebar-submenu-toggle').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const submenu = btn.nextElementSibling;
            if (!submenu) return;
            const aberto = submenu.classList.toggle('active');
            btn.classList.toggle('open', aberto);
            btn.setAttribute('aria-expanded', String(aberto));
        });
    });

    // =============================================
    // CONTADOR ANIMADO (Home - números de impacto)
    // =============================================
    function animarContador(el) {
        const alvo    = parseInt(el.dataset.target || el.textContent, 10);
        const duracao = 1500;
        const inicio  = performance.now();

        function atualizar(agora) {
            const progresso = Math.min((agora - inicio) / duracao, 1);
            const easing    = 1 - Math.pow(1 - progresso, 3); // ease-out cubic
            el.textContent  = Math.floor(easing * alvo).toLocaleString('pt-BR');
            if (progresso < 1) requestAnimationFrame(atualizar);
        }

        requestAnimationFrame(atualizar);
    }

    const obsContadores = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                animarContador(entry.target);
                obsContadores.unobserve(entry.target);
            }
        });
    }, { threshold: 0.5 });

    document.querySelectorAll('[data-counter]').forEach(el => {
        obsContadores.observe(el);
    });

    // =============================================
    // FORM: REPOPULAÇÃO DE VALORES ANTIGOS (old())
    // =============================================
    // Os inputs com data-old preservam valor via PHP,
    // apenas garantir foco e validação visual
    document.querySelectorAll('input[required], select[required], textarea[required]').forEach(field => {
        field.addEventListener('blur', () => {
            const valido = field.value.trim() !== '';
            field.classList.toggle('input-error', !valido);
            field.classList.toggle('input-valid', valido);
        });
    });

    // =============================================
    // PESQUISA INLINE NA TABELA ADMIN
    // =============================================
    const inputBusca = document.getElementById('busca-tabela');
    const tbody      = document.querySelector('table.data-table tbody');

    if (inputBusca && tbody) {
        inputBusca.addEventListener('input', function () {
            const termo = this.value.toLowerCase();
            tbody.querySelectorAll('tr').forEach(tr => {
                const texto = tr.textContent.toLowerCase();
                tr.style.display = texto.includes(termo) ? '' : 'none';
            });
        });
    }

}); // fim DOMContentLoaded
