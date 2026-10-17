/**
 * GlassCSS Optical Engine — Versão de Produção
 * Física de refração óptica, dispersão cromática RGB e reflexo especular.
 * Zero dependências externas | Vanilla JS puro (~2KB).
 */
(function(window, document) {
    'use strict';

    const isChromium = /Chrome\//.test(navigator.userAgent) && !/Firefox/.test(navigator.userAgent);
    
    // Container SVG oculto para os filtros dinâmicos
    let defs = document.getElementById('glass-defs');
    if (!defs) {
        const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
        svg.setAttribute('width', '0');
        svg.setAttribute('height', '0');
        svg.style.position = 'absolute';
        svg.style.pointerEvents = 'none';
        svg.setAttribute('aria-hidden', 'true');
        defs = document.createElementNS('http://www.w3.org/2000/svg', 'defs');
        defs.id = 'glass-defs';
        svg.appendChild(defs);
        document.body.appendChild(svg);
    }

    // Os 5 Presets Ópticos Oficiais da Skill GlassCSS
    const PRESETS = {
        cristal: { refr: 55, blur: 2,  disp: 0.35, bez: 36 }, // Quartzo transparente, alta refração e prisma RGB
        fosco:   { refr: 15, blur: 12, disp: 0.05, bez: 20 }, // Jateado aveludado, leitura contínua e alto contraste
        gelo:    { refr: 35, blur: 6,  disp: 0.20, bez: 28 }, // Equilibrado fresco, tom ciano translúcido
        fume:    { refr: 40, blur: 8,  disp: 0.18, bez: 32 }, // Vidro denso escuro, aro chanfrado luminoso
        liquido: { refr: 75, blur: 3,  disp: 0.45, bez: 45 }  // Curvatura convexa pronunciada (lente óptica)
    };

    const normalMapCache = {};

    /**
     * Calcula o mapa de normais através de Signed Distance Field (SDF) num canvas in-memory
     */
    function getNormalMapURL(w, h, r, bevel) {
        const key = `${w}x${h}x${r}x${bevel}`;
        if (normalMapCache[key]) return normalMapCache[key];

        const sw = Math.max(2, w >> 1);
        const sh = Math.max(2, h >> 1);
        const canvas = document.createElement('canvas');
        canvas.width = sw;
        canvas.height = sh;

        const ctx = canvas.getContext('2d');
        const imgData = ctx.createImageData(sw, sh);
        const data = imgData.data;
        const k = sw / w;
        const hw = w / 2;
        const hh = h / 2;
        r = Math.min(r, hw, hh);

        for (let j = 0; j < sh; j++) {
            for (let i = 0; i < sw; i++) {
                const px = (i + 0.5) / k - hw;
                const py = (j + 0.5) / k - hh;
                const qx = Math.abs(px) - (hw - r);
                const qy = Math.abs(py) - (hh - r);
                const ox = Math.max(qx, 0);
                const oy = Math.max(qy, 0);
                const len = Math.hypot(ox, oy);
                const depth = -(len + Math.min(Math.max(qx, qy), 0) - r);

                const sx = px < 0 ? -1 : 1;
                const sy = py < 0 ? -1 : 1;
                let nx, ny;
                if (len > 0) {
                    nx = (ox / len) * sx;
                    ny = (oy / len) * sy;
                } else if (qx > qy) {
                    nx = sx; ny = 0;
                } else {
                    nx = 0; ny = sy;
                }

                const t = Math.min(1, Math.max(0, 1 - depth / bevel));
                const f = t * t * (3 - 2 * t); // Smoothstep
                const idx = (j * sw + i) * 4;

                data[idx]     = 128 - 127 * nx * f; // Canal R: deslocamento X
                data[idx + 1] = 128 - 127 * ny * f; // Canal G: deslocamento Y
                data[idx + 2] = 128;                // Canal B: neutro
                data[idx + 3] = 255;                // Alfa
            }
        }

        ctx.putImageData(imgData, 0, 0);
        return normalMapCache[key] = canvas.toDataURL();
    }

    /**
     * Constrói e injeta os filtros SVG com dispersão cromática RGB
     */
    function buildFilters() {
        const elements = document.querySelectorAll('.glass, .glass-light, .glass-dark, [data-glass]');
        let svgFilters = '';

        elements.forEach((el, index) => {
            const w = el.offsetWidth;
            const h = el.offsetHeight;
            if (!w || !h) return;

            const presetName = el.dataset.glass || (el.classList.contains('glass-dark') ? 'fume' : 'cristal');
            const conf = PRESETS[presetName] || PRESETS.cristal;

            // Fallback suave para Firefox / Safari WebKit
            if (!isChromium) {
                el.style.setProperty('--rf', `blur(${6 + conf.blur * 2}px) saturate(1.6) brightness(1.08)`);
                return;
            }

            const radius = parseFloat(getComputedStyle(el).borderTopLeftRadius) || 20;
            const bevel = Math.min(conf.bez * (parseFloat(el.dataset.bezelScale) || 1), Math.min(w, h) / 2);
            const mapURL = getNormalMapURL(w, h, radius, bevel);

            const scale = conf.refr;
            const dispOffset = conf.disp * 0.22;

            const dm = (sc, res) => `<feDisplacementMap in="blurLayer" in2="mapLayer" scale="${sc}" xChannelSelector="R" yChannelSelector="G" result="${res}"/>`;

            svgFilters += `
                <filter id="glass-filter-${index}" x="0" y="0" width="${w}" height="${h}" filterUnits="userSpaceOnUse" primitiveUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                    <feGaussianBlur in="SourceGraphic" stdDeviation="${conf.blur}" result="blurLayer"/>
                    <feImage href="${mapURL}" x="0" y="0" width="${w}" height="${h}" preserveAspectRatio="none" result="mapLayer"/>
                    ${dm(scale * (1 + dispOffset), 'r_disp')}
                    <feColorMatrix in="r_disp" type="matrix" values="1 0 0 0 0  0 0 0 0 0  0 0 0 0 0  0 0 0 1 0" result="red_chan"/>
                    ${dm(scale, 'g_disp')}
                    <feColorMatrix in="g_disp" type="matrix" values="0 0 0 0 0  0 1 0 0 0  0 0 0 0 0  0 0 0 1 0" result="green_chan"/>
                    ${dm(scale * (1 - dispOffset), 'b_disp')}
                    <feColorMatrix in="b_disp" type="matrix" values="0 0 0 0 0  0 0 0 0 0  0 0 1 0 0  0 0 0 1 0" result="blue_chan"/>
                    <feBlend in="red_chan" in2="green_chan" mode="screen" result="rg_blend"/>
                    <feBlend in="rg_blend" in2="blue_chan" mode="screen"/>
                </filter>
            `;

            el.style.setProperty('--rf', `url(#glass-filter-${index}) saturate(1.5) brightness(1.08)`);
        });

        defs.innerHTML = svgFilters;
    }

    let rafId = 0;
    function scheduleRebuild() {
        cancelAnimationFrame(rafId);
        rafId = requestAnimationFrame(buildFilters);
    }

    // ResizeObserver para manter refração proporcional
    const resizeObserver = new ResizeObserver(scheduleRebuild);
    function observeAll() {
        document.querySelectorAll('.glass, .glass-light, .glass-dark, [data-glass]').forEach(el => resizeObserver.observe(el));
    }

    // Rastreamento suave de cursor para reflexo especular no chanfro (Layer 2)
    let ptrRaf = 0;
    window.addEventListener('pointermove', function(e) {
        if (ptrRaf) return;
        ptrRaf = requestAnimationFrame(() => {
            ptrRaf = 0;
            const elements = document.querySelectorAll('.glass, .glass-light, .glass-dark, .glass-pill, [data-glass]');
            elements.forEach(el => {
                const rect = el.getBoundingClientRect();
                el.style.setProperty('--mx', (e.clientX - rect.left) + 'px');
                el.style.setProperty('--my', (e.clientY - rect.top) + 'px');
            });
        });
    }, { passive: true });

    // Inicialização automática
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            observeAll();
            scheduleRebuild();
        });
    } else {
        observeAll();
        scheduleRebuild();
    }
    if (document.fonts) document.fonts.ready.then(scheduleRebuild);

    // API pública para elementos dinâmicos
    window.GlassCSS = {
        rebuild: scheduleRebuild,
        observe: function(el) {
            resizeObserver.observe(el);
            scheduleRebuild();
        },
        presets: PRESETS
    };

})(window, document);
