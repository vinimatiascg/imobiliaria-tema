#!/usr/bin/env node
/*
 * Gera os slides do carrossel do Instagram (1080x1350) a partir de um JSON.
 * Uso: node render-carrossel.js caminho/carrossel.json [pasta-de-saida]
 */
const fs = require('fs');
const path = require('path');
const { chromium } = require('playwright');

const CONTATO = {
	nome: 'Vinicius Matias',
	cargo: 'Corretor e Avaliador de Imóveis',
	registro: 'CRECI-PB 9972-F | CNAI 35503',
	whatsapp: '(83) 99926-7089',
	site: 'www.viniciusmatias.com.br',
	instagram: '@viniciusmatiascorretor',
};

const W = 1080;
const H = 1350;

const esc = (s) =>
	String(s ?? '').replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));

const MIME = { '.jpg': 'image/jpeg', '.jpeg': 'image/jpeg', '.png': 'image/png', '.webp': 'image/webp' };

function imgData(base, rel) {
	if (!rel) return '';
	const p = path.resolve(base, rel);
	if (!fs.existsSync(p)) throw new Error(`Imagem não encontrada: ${p}`);
	const mime = MIME[path.extname(p).toLowerCase()] || 'image/jpeg';
	return `data:${mime};base64,${fs.readFileSync(p).toString('base64')}`;
}

function posicao(p) {
	return { top: 'center top', bottom: 'center bottom', left: 'left center', right: 'right center' }[p] || p || 'center';
}

const CSS = `
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');
:root { --azul:#0E1A2B; --azul2:#162B45; --dourado:#B2915A; --azulc:#2F80ED; --claro:#F5F2EC; }
* { box-sizing:border-box; margin:0; padding:0; }
body { width:${W}px; height:${H}px; font-family:'Inter',sans-serif; background:var(--azul); color:#fff; overflow:hidden; }
.slide { position:relative; width:${W}px; height:${H}px; overflow:hidden; }
.bg { position:absolute; inset:0; background-size:cover; background-position:center; }
.grad-b { position:absolute; inset:0; background:linear-gradient(180deg, rgba(14,26,43,0) 35%, rgba(14,26,43,.75) 62%, rgba(14,26,43,.97) 100%); }
.grad-t { position:absolute; inset:0; background:linear-gradient(180deg, rgba(14,26,43,.7) 0%, rgba(14,26,43,0) 22%); }
.topo { position:absolute; top:56px; left:72px; right:72px; display:flex; justify-content:space-between; align-items:center; font-size:24px; font-weight:600; letter-spacing:.14em; text-transform:uppercase; }
.topo .marca { color:#fff; }
.topo .marca b { color:var(--dourado); font-weight:700; }
.topo .pg { color:rgba(255,255,255,.75); font-weight:500; letter-spacing:.06em; }
.base { position:absolute; left:72px; right:72px; bottom:110px; }
.chamada { display:inline-block; background:var(--dourado); color:var(--azul); font-weight:700; font-size:28px; letter-spacing:.12em; text-transform:uppercase; padding:14px 26px; border-radius:6px; margin-bottom:30px; }
h1 { font-size:96px; line-height:1.02; font-weight:800; letter-spacing:-.02em; }
h2 { font-size:68px; line-height:1.08; font-weight:800; letter-spacing:-.015em; }
.sub { margin-top:24px; font-size:38px; font-weight:500; color:rgba(255,255,255,.9); }
.linha { width:110px; height:6px; background:var(--dourado); border-radius:3px; margin:30px 0 28px; }
.txt { font-size:36px; line-height:1.4; color:rgba(255,255,255,.92); font-weight:400; }
.rodape { position:absolute; left:72px; right:72px; bottom:44px; display:flex; justify-content:space-between; font-size:20px; color:rgba(255,255,255,.6); }
.dark .rodape { color:rgba(14,26,43,.55); }
.metade { position:absolute; top:0; left:0; right:0; height:600px; background-size:cover; background-position:center; }
.metade::after { content:''; position:absolute; inset:0; background:linear-gradient(180deg, rgba(14,26,43,.55) 0%, rgba(14,26,43,0) 30%, rgba(14,26,43,0) 70%, var(--azul) 100%); }
.lista { list-style:none; margin-top:10px; }
.lista li { position:relative; padding-left:58px; font-size:38px; line-height:1.3; margin-bottom:24px; font-weight:500; }
.lista li::before { content:''; position:absolute; left:0; top:10px; width:30px; height:30px; border-radius:50%; background:var(--dourado); box-shadow: inset 0 0 0 8px var(--azul), 0 0 0 0; }
.ficha { margin-top:10px; border-top:2px solid rgba(178,145,90,.5); }
.ficha .row { display:flex; justify-content:space-between; gap:30px; padding:24px 0; border-bottom:2px solid rgba(178,145,90,.35); font-size:34px; }
.ficha .row span { color:rgba(255,255,255,.7); font-weight:500; }
.ficha .row b { text-align:right; font-weight:700; }
.dark { background:var(--claro); color:var(--azul); }
.dark .topo .marca { color:var(--azul); }
.dark .topo .pg { color:rgba(14,26,43,.6); }
.dark .txt { color:rgba(14,26,43,.8); }
.planta { position:absolute; top:150px; left:72px; right:72px; height:840px; background:#fff; border-radius:18px; box-shadow:0 20px 50px rgba(14,26,43,.12); display:flex; align-items:center; justify-content:center; padding:30px; }
.planta img { max-width:100%; max-height:100%; object-fit:contain; }
.planta-txt { position:absolute; left:72px; right:72px; top:1030px; }
.planta-txt h2 { font-size:56px; }
.planta-txt .txt { margin-top:12px; }
.contato .card { position:absolute; left:60px; right:60px; bottom:60px; background:rgba(14,26,43,.94); border:2px solid rgba(178,145,90,.6); border-radius:22px; padding:52px 56px 40px; }
.contato h2 { font-size:64px; }
.contato .nome { margin-top:26px; font-size:40px; font-weight:800; }
.contato .cargo { font-size:28px; color:var(--dourado); font-weight:600; margin-top:4px; }
.contato .reg { font-size:26px; color:rgba(255,255,255,.75); margin-top:6px; }
.contato .itens { margin-top:26px; display:grid; gap:12px; font-size:30px; font-weight:500; }
.contato .itens span { color:var(--dourado); font-weight:700; display:inline-block; width:180px; }
.contato .legal { margin-top:28px; padding-top:22px; border-top:1px solid rgba(255,255,255,.18); font-size:21px; line-height:1.45; color:rgba(255,255,255,.7); }
.contato .legal b { color:#fff; font-weight:600; }
`;

function topo(cfg, i, n) {
	return `<div class="topo"><div class="marca">Vinicius <b>Matias</b></div><div class="pg">${i}/${n}</div></div>`;
}

function rodape(cfg) {
	return `<div class="rodape"><div>${esc(cfg.empreendimento || '')}</div><div>Imagens meramente ilustrativas</div></div>`;
}

function slideHtml(s, cfg, base, i, n) {
	const bg = s.imagem ? imgData(base, s.imagem) : '';
	const pos = posicao(s.posicao);
	switch (s.tipo) {
		case 'capa':
			return `<div class="slide">
				<div class="bg" style="background-image:url('${bg}');background-position:${pos}"></div>
				<div class="grad-t"></div><div class="grad-b"></div>
				${topo(cfg, i, n)}
				<div class="base">
					${s.chamada ? `<div class="chamada">${esc(s.chamada)}</div>` : ''}
					<h1>${esc(s.titulo || cfg.empreendimento)}</h1>
					<div class="linha"></div>
					${s.subtitulo ? `<div class="sub">${esc(s.subtitulo)}</div>` : ''}
				</div>
				${rodape(cfg)}
			</div>`;
		case 'foto':
			return `<div class="slide">
				<div class="bg" style="background-image:url('${bg}');background-position:${pos}"></div>
				<div class="grad-t"></div><div class="grad-b"></div>
				${topo(cfg, i, n)}
				<div class="base">
					<h2>${esc(s.titulo)}</h2>
					<div class="linha"></div>
					${s.texto ? `<div class="txt">${esc(s.texto)}</div>` : ''}
				</div>
				${rodape(cfg)}
			</div>`;
		case 'lista':
			return `<div class="slide">
				${bg ? `<div class="metade" style="background-image:url('${bg}');background-position:${pos}"></div>` : ''}
				${topo(cfg, i, n)}
				<div class="base" style="top:${bg ? 560 : 200}px;bottom:auto">
					<h2>${esc(s.titulo)}</h2>
					<div class="linha"></div>
					<ul class="lista">${(s.itens || []).slice(0, 6).map((t) => `<li>${esc(t)}</li>`).join('')}</ul>
				</div>
				${rodape(cfg)}
			</div>`;
		case 'ficha':
			return `<div class="slide">
				${bg ? `<div class="metade" style="height:420px;background-image:url('${bg}');background-position:${pos}"></div>` : ''}
				${topo(cfg, i, n)}
				<div class="base" style="top:${bg ? 400 : 190}px;bottom:auto">
					<h2>${esc(s.titulo || 'Ficha técnica')}</h2>
					<div class="linha"></div>
					<div class="ficha">${(s.itens || []).slice(0, 8).map(([k, v]) => `<div class="row"><span>${esc(k)}</span><b>${esc(v)}</b></div>`).join('')}</div>
				</div>
				${rodape(cfg)}
			</div>`;
		case 'planta':
			return `<div class="slide dark">
				${topo(cfg, i, n)}
				<div class="planta"><img src="${bg}"></div>
				<div class="planta-txt">
					<h2>${esc(s.titulo)}</h2>
					${s.texto ? `<div class="txt">${esc(s.texto)}</div>` : ''}
				</div>
				${rodape(cfg)}
			</div>`;
		case 'contato':
			return `<div class="slide contato">
				${bg ? `<div class="bg" style="background-image:url('${bg}');background-position:${pos}"></div><div class="grad-t"></div>` : ''}
				${topo(cfg, i, n)}
				<div class="card">
					<h2>${esc(s.titulo || 'Agende sua visita')}</h2>
					${s.texto ? `<div class="txt" style="margin-top:14px;font-size:30px">${esc(s.texto)}</div>` : ''}
					<div class="nome">${CONTATO.nome}</div>
					<div class="cargo">${CONTATO.cargo}</div>
					<div class="reg">${CONTATO.registro}</div>
					<div class="itens">
						<div><span>WhatsApp</span>${CONTATO.whatsapp}</div>
						<div><span>Site</span>${CONTATO.site}</div>
						<div><span>Instagram</span>${CONTATO.instagram}</div>
					</div>
					<div class="legal">
						${s.ri ? `<b>RI:</b> ${esc(s.ri)}<br>` : ''}
						Imagens meramente ilustrativas.
					</div>
				</div>
			</div>`;
		default:
			throw new Error(`Tipo de slide desconhecido: ${s.tipo}`);
	}
}

(async () => {
	const jsonPath = process.argv[2];
	if (!jsonPath) {
		console.error('Uso: node render-carrossel.js carrossel.json [pasta-de-saida]');
		process.exit(1);
	}
	const cfg = JSON.parse(fs.readFileSync(jsonPath, 'utf8'));
	const base = path.dirname(path.resolve(jsonPath));
	const out = path.resolve(process.argv[3] || path.join(base, 'carrossel'));
	fs.mkdirSync(out, { recursive: true });
	const slides = cfg.slides || [];
	if (!slides.length || slides[slides.length - 1].tipo !== 'contato') {
		console.warn('Aviso: o último slide deveria ser do tipo "contato".');
	}

	const browser = await chromium.launch();
	const page = await browser.newPage({ viewport: { width: W, height: H }, deviceScaleFactor: 1 });
	for (let i = 0; i < slides.length; i++) {
		const html = `<!doctype html><html lang="pt-BR"><head><meta charset="utf-8"><style>${CSS}</style></head><body>${slideHtml(slides[i], cfg, base, i + 1, slides.length)}</body></html>`;
		await page.setContent(html, { waitUntil: 'networkidle' }).catch(() => page.setContent(html));
		await page.evaluate(() => document.fonts.ready);
		const file = path.join(out, `slide-${String(i + 1).padStart(2, '0')}.png`);
		await page.screenshot({ path: file, clip: { x: 0, y: 0, width: W, height: H } });
		console.log(file);
	}
	await browser.close();
})().catch((e) => {
	console.error(e.message || e);
	process.exit(1);
});
