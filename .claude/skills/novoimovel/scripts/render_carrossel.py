#!/usr/bin/env python3
"""
Gera os slides do carrossel do Instagram (1080x1350) a partir de um JSON.
Versão em Python puro (só precisa do Pillow), para rodar em qualquer lugar:
Claude Code, claude.ai (web, desktop, celular) ou qualquer computador.

Uso: python3 render_carrossel.py caminho/carrossel.json [pasta-de-saida]
"""
import json
import os
import sys

from PIL import Image, ImageDraw, ImageFont

AQUI = os.path.dirname(os.path.abspath(__file__))
ASSETS = os.path.join(AQUI, "..", "assets")
FONTES = os.path.join(ASSETS, "fonts")

W, H = 1080, 1350
M = 72  # margem lateral

AZUL = (14, 26, 43)
DOURADO = (178, 145, 90)
CLARO = (245, 242, 236)
BRANCO = (255, 255, 255)

CONTATO = {
    "nome": "Vinicius Matias",
    "cargo": "Corretor e Avaliador de Imóveis",
    "registro": "CRECI-PB 9972-F | CNAI 35503",
    "whatsapp": "(83) 99926-7089",
    "site": "www.viniciusmatias.com.br",
    "instagram": "@viniciusmatiascorretor",
}

_cache = {}
_grad = {}


def fonte(peso, tamanho):
    chave = (peso, tamanho)
    if chave not in _cache:
        _cache[chave] = ImageFont.truetype(os.path.join(FONTES, f"Inter-{peso}.otf"), tamanho)
    return _cache[chave]


def rgba(cor, a=1.0):
    return (cor[0], cor[1], cor[2], int(round(255 * a)))


# ---------- texto ----------

def largura(txt, f, esp=0):
    if not txt:
        return 0
    return f.getlength(txt) + esp * (len(txt) - 1)


def quebrar(txt, f, max_w):
    linhas = []
    for par in str(txt).split("\n"):
        atual = ""
        for palavra in par.split():
            teste = (atual + " " + palavra).strip()
            if largura(teste, f) <= max_w or not atual:
                atual = teste
            else:
                linhas.append(atual)
                atual = palavra
        linhas.append(atual)
    return linhas


def escrever(d, x, y, txt, f, cor, lh=1.2, max_w=None, esp=0, alinhar="esquerda"):
    """Escreve o texto com a caixa de linha começando em y. Retorna o y final."""
    tamanho = f.size
    linhas = quebrar(txt, f, max_w) if max_w else str(txt).split("\n")
    asc, desc = f.getmetrics()
    altura_linha = tamanho * lh
    for linha in linhas:
        base = y + (altura_linha - (asc + desc)) / 2 + asc
        if esp:
            cx = x if alinhar == "esquerda" else x - largura(linha, f, esp)
            for ch in linha:
                d.text((cx, base), ch, font=f, fill=cor, anchor="ls")
                cx += f.getlength(ch) + esp
        else:
            if alinhar == "direita":
                d.text((x, base), linha, font=f, fill=cor, anchor="rs")
            else:
                d.text((x, base), linha, font=f, fill=cor, anchor="ls")
        y += altura_linha
    return y


def altura_texto(txt, f, lh, max_w):
    return len(quebrar(txt, f, max_w)) * f.size * lh


# ---------- imagem ----------

def posicao(p):
    mapa = {"top": (50, 0), "bottom": (50, 100), "left": (0, 50), "right": (100, 50), "center": (50, 50)}
    if not p:
        return (50, 50)
    if p in mapa:
        return mapa[p]
    partes = p.replace("%", "").split()
    nomes = {"center": 50, "top": 0, "bottom": 100, "left": 0, "right": 100}
    vals = [float(nomes.get(v, v)) for v in partes]
    if len(vals) == 1:
        vals.append(50)
    return tuple(vals[:2])


def cobrir(img, w, h, pos):
    img = img.convert("RGB")
    esc = max(w / img.width, h / img.height)
    nw, nh = max(w, round(img.width * esc)), max(h, round(img.height * esc))
    img = img.resize((nw, nh), Image.LANCZOS)
    px, py = pos
    x = round((nw - w) * px / 100)
    y = round((nh - h) * py / 100)
    return img.crop((x, y, x + w, y + h))


def conter(img, w, h):
    img = img.convert("RGBA")
    esc = min(w / img.width, h / img.height)
    return img.resize((max(1, round(img.width * esc)), max(1, round(img.height * esc))), Image.LANCZOS)


def gradiente(w, h, paradas, cor=AZUL):
    """paradas: lista de (posição 0..1, alpha 0..1) de cima para baixo."""
    chave = (w, h, tuple(paradas), cor)
    if chave in _grad:
        return _grad[chave]
    dados = []
    for y in range(h):
        t = y / max(1, h - 1)
        a = paradas[0][1] if t < paradas[0][0] else paradas[-1][1]
        for (p0, a0), (p1, a1) in zip(paradas, paradas[1:]):
            if p0 <= t <= p1:
                a = a0 + (a1 - a0) * ((t - p0) / (p1 - p0) if p1 > p0 else 0)
                break
        dados.append(rgba(cor, a))
    col = Image.new("RGBA", (1, h))
    col.putdata(dados)
    _grad[chave] = col.resize((w, h))
    return _grad[chave]


GRAD_T = [(0, 0.7), (0.22, 0), (1, 0)]
GRAD_B = [(0, 0), (0.35, 0), (0.62, 0.75), (1, 0.97)]
GRAD_METADE = [(0, 0.8), (0.14, 0.35), (0.32, 0), (0.70, 0), (1, 1)]


def abrir(base, rel):
    p = os.path.normpath(os.path.join(base, rel))
    if not os.path.exists(p):
        raise SystemExit(f"Imagem não encontrada: {p}")
    return Image.open(p)


# ---------- partes comuns ----------

def logo(claro):
    nome = "logo-escura.png" if claro else "logo-branca.png"
    im = Image.open(os.path.join(ASSETS, nome)).convert("RGBA")
    alt = 44
    im = im.resize((round(im.width * alt / im.height), alt), Image.LANCZOS)
    a = im.getchannel("A").point(lambda v: int(v * 0.88))
    im.putalpha(a)
    return im


def topo(tela, i, n, claro=False):
    lg = logo(claro)
    tela.alpha_composite(lg, (M, 56))
    d = ImageDraw.Draw(tela)
    cor = rgba(AZUL, 0.6) if claro else rgba(BRANCO, 0.75)
    escrever(d, W - M, 56 + (44 - 29) / 2, f"{i}/{n}", fonte("Medium", 24), cor, lh=1.2, esp=1.4, alinhar="direita")


def rodape(tela, nome, claro=False):
    d = ImageDraw.Draw(tela)
    f = fonte("Regular", 20)
    cor = rgba(AZUL, 0.55) if claro else rgba(BRANCO, 0.6)
    y = H - 44 - 24
    escrever(d, M, y, nome, f, cor)
    escrever(d, W - M, y, "Imagens meramente ilustrativas", f, cor, alinhar="direita")


def linha_dourada(d, x, y):
    d.rounded_rectangle((x, y, x + 110, y + 6), radius=3, fill=rgba(DOURADO))


def fundo_foto(tela, base, s, topo_grad=True, baixo_grad=True):
    if s.get("imagem"):
        tela.paste(cobrir(abrir(base, s["imagem"]), W, H, posicao(s.get("posicao"))), (0, 0))
    if topo_grad:
        tela.alpha_composite(gradiente(W, H, GRAD_T))
    if baixo_grad:
        tela.alpha_composite(gradiente(W, H, GRAD_B))


# ---------- tipos de slide ----------

def s_capa(tela, s, cfg, base):
    fundo_foto(tela, base, s)
    d = ImageDraw.Draw(tela)
    mw = W - 2 * M
    f_h1, f_sub, f_ch = fonte("ExtraBold", 96), fonte("Medium", 38), fonte("Bold", 28)
    titulo = s.get("titulo") or cfg.get("empreendimento", "")
    h_tit = altura_texto(titulo, f_h1, 1.02, mw)
    h_sub = 38 * 1.2 + 24 if s.get("subtitulo") else 0
    total = (28 * 1.2 + 28 + 30 if s.get("chamada") else 0) + h_tit + 30 + 6 + 28 + h_sub
    y = H - 110 - total
    if s.get("chamada"):
        txt = s["chamada"].upper()
        esp = 28 * 0.12
        bw = largura(txt, f_ch, esp) + 52
        bh = 28 * 1.2 + 28
        d.rounded_rectangle((M, y, M + bw, y + bh), radius=6, fill=rgba(DOURADO))
        escrever(d, M + 26, y + 14, txt, f_ch, rgba(AZUL), esp=esp)
        y += bh + 30
    y = escrever(d, M, y, titulo, f_h1, rgba(BRANCO), lh=1.02, max_w=mw)
    y += 30
    linha_dourada(d, M, y)
    y += 6 + 28
    if s.get("subtitulo"):
        escrever(d, M, y + 24, s["subtitulo"], f_sub, rgba(BRANCO, 0.9), max_w=mw)


def s_foto(tela, s, cfg, base):
    fundo_foto(tela, base, s)
    d = ImageDraw.Draw(tela)
    mw = W - 2 * M
    f_h2, f_txt = fonte("ExtraBold", 68), fonte("Regular", 36)
    h = altura_texto(s.get("titulo", ""), f_h2, 1.08, mw) + 30 + 6 + 28
    if s.get("texto"):
        h += altura_texto(s["texto"], f_txt, 1.4, mw)
    y = H - 110 - h
    y = escrever(d, M, y, s.get("titulo", ""), f_h2, rgba(BRANCO), lh=1.08, max_w=mw) + 30
    linha_dourada(d, M, y)
    y += 6 + 28
    if s.get("texto"):
        escrever(d, M, y, s["texto"], f_txt, rgba(BRANCO, 0.92), lh=1.4, max_w=mw)


def metade(tela, base, s, altura):
    img = cobrir(abrir(base, s["imagem"]), W, altura, posicao(s.get("posicao")))
    tela.paste(img, (0, 0))
    tela.alpha_composite(gradiente(W, altura, GRAD_METADE), (0, 0))


def s_lista(tela, s, cfg, base):
    tela.paste(Image.new("RGB", (W, H), AZUL), (0, 0))
    tem = bool(s.get("imagem"))
    if tem:
        metade(tela, base, s, 600)
    d = ImageDraw.Draw(tela)
    mw = W - 2 * M
    y = 560 if tem else 200
    y = escrever(d, M, y, s.get("titulo", ""), fonte("ExtraBold", 68), rgba(BRANCO), lh=1.08, max_w=mw) + 30
    linha_dourada(d, M, y)
    y += 6 + 28 + 10
    f = fonte("Medium", 38)
    for item in (s.get("itens") or [])[:6]:
        cy = y + 10 + 15
        d.ellipse((M + 8, cy - 7, M + 22, cy + 7), fill=rgba(DOURADO))
        y = escrever(d, M + 58, y, item, f, rgba(BRANCO), lh=1.3, max_w=mw - 58) + 24


def s_ficha(tela, s, cfg, base):
    tela.paste(Image.new("RGB", (W, H), AZUL), (0, 0))
    tem = bool(s.get("imagem"))
    if tem:
        metade(tela, base, s, 420)
    d = ImageDraw.Draw(tela)
    mw = W - 2 * M
    y = 400 if tem else 190
    y = escrever(d, M, y, s.get("titulo") or "Ficha técnica", fonte("ExtraBold", 68), rgba(BRANCO), lh=1.08, max_w=mw) + 30
    linha_dourada(d, M, y)
    y += 6 + 28 + 10
    f_k, f_v = fonte("Medium", 34), fonte("Bold", 34)
    d.rectangle((M, y, W - M, y + 1), fill=rgba(DOURADO, 0.5))
    y += 2
    for k, v in (s.get("itens") or [])[:8]:
        escrever(d, M, y + 24, k, f_k, rgba(BRANCO, 0.7))
        escrever(d, W - M, y + 24, v, f_v, rgba(BRANCO), alinhar="direita")
        y += 24 * 2 + 34 * 1.2
        d.rectangle((M, y, W - M, y + 1), fill=rgba(DOURADO, 0.35))
        y += 2


def s_planta(tela, s, cfg, base):
    tela.paste(Image.new("RGB", (W, H), CLARO), (0, 0))
    # sombra suave do cartão
    sombra = Image.new("RGBA", (W, H), (0, 0, 0, 0))
    ImageDraw.Draw(sombra).rounded_rectangle((M, 170, W - M, 150 + 840 + 20), radius=18, fill=rgba(AZUL, 0.10))
    from PIL import ImageFilter
    tela.alpha_composite(sombra.filter(ImageFilter.GaussianBlur(24)))
    d = ImageDraw.Draw(tela)
    d.rounded_rectangle((M, 150, W - M, 150 + 840), radius=18, fill=rgba(BRANCO))
    if s.get("imagem"):
        im = conter(abrir(base, s["imagem"]), W - 2 * M - 60, 840 - 60)
        tela.alpha_composite(im, (round(M + (W - 2 * M - im.width) / 2), round(150 + (840 - im.height) / 2)))
    d = ImageDraw.Draw(tela)
    mw = W - 2 * M
    y = escrever(d, M, 1030, s.get("titulo", ""), fonte("ExtraBold", 56), rgba(AZUL), lh=1.08, max_w=mw)
    if s.get("texto"):
        escrever(d, M, y + 12, s["texto"], fonte("Regular", 36), rgba(AZUL, 0.8), lh=1.4, max_w=mw)


def s_contato(tela, s, cfg, base):
    if s.get("imagem"):
        fundo_foto(tela, base, s, baixo_grad=False)
    else:
        tela.paste(Image.new("RGB", (W, H), AZUL), (0, 0))
    pad_x, pad_t, pad_b = 56, 52, 40
    cx0, cx1 = 60, W - 60
    mw = cx1 - cx0 - 2 * pad_x
    f_h2 = fonte("ExtraBold", 60)
    f_leg, f_leg_b = fonte("Regular", 21), fonte("SemiBold", 21)
    legal = []
    if s.get("ri"):
        legal.append(("RI: ", s["ri"]))
    if s.get("aviso"):
        legal.append(("", s["aviso"]))
    legal.append(("", "Imagens meramente ilustrativas."))
    h_legal = sum(altura_texto(p + t, f_leg, 1.45, mw) for p, t in legal)
    h = pad_t + 60 * 1.1 * 2 + 26 + 40 * 1.2 + 4 + 28 * 1.2 + 6 + 26 * 1.2 + 26 + 3 * 30 * 1.2 + 2 * 12 + 28 + 22 + h_legal + pad_b
    y0 = H - 60 - h
    card = Image.new("RGBA", (W, H), (0, 0, 0, 0))
    ImageDraw.Draw(card).rounded_rectangle((cx0, y0, cx1, H - 60), radius=22, fill=rgba(AZUL, 0.94), outline=rgba(DOURADO, 0.6), width=2)
    tela.alpha_composite(card)
    d = ImageDraw.Draw(tela)
    x = cx0 + pad_x
    y = y0 + pad_t
    y = escrever(d, x, y, "Faça sua simulação\nsem compromisso", f_h2, rgba(BRANCO), lh=1.1)
    y = escrever(d, x, y + 26, CONTATO["nome"], fonte("ExtraBold", 40), rgba(BRANCO))
    y = escrever(d, x, y + 4, CONTATO["cargo"], fonte("SemiBold", 28), rgba(DOURADO))
    y = escrever(d, x, y + 6, CONTATO["registro"], fonte("Regular", 26), rgba(BRANCO, 0.75))
    y += 26
    f_lbl, f_val = fonte("Bold", 30), fonte("Medium", 30)
    for i, (rot, val) in enumerate([("WhatsApp", CONTATO["whatsapp"]), ("Site", CONTATO["site"]), ("Instagram", CONTATO["instagram"])]):
        escrever(d, x, y, rot, f_lbl, rgba(DOURADO))
        y = escrever(d, x + 180, y, val, f_val, rgba(BRANCO)) + (12 if i < 2 else 0)
    y += 28
    d.rectangle((x, y, cx1 - pad_x, y), fill=rgba(BRANCO, 0.18))
    y += 22
    for prefixo, txt in legal:
        if prefixo:
            escrever(d, x, y, prefixo, f_leg_b, rgba(BRANCO), lh=1.45)
            # o texto do RI continua depois do rótulo, com quebra normal
            linhas = quebrar(prefixo + txt, f_leg, mw)
            primeira = linhas[0][len(prefixo):]
            escrever(d, x + largura(prefixo, f_leg_b), y, primeira, f_leg, rgba(BRANCO, 0.7), lh=1.45)
            y += 21 * 1.45
            if len(linhas) > 1:
                y = escrever(d, x, y, "\n".join(linhas[1:]), f_leg, rgba(BRANCO, 0.7), lh=1.45)
        else:
            y = escrever(d, x, y, txt, f_leg, rgba(BRANCO, 0.7), lh=1.45, max_w=mw)


TIPOS = {"capa": s_capa, "foto": s_foto, "lista": s_lista, "ficha": s_ficha, "planta": s_planta, "contato": s_contato}


def main():
    if len(sys.argv) < 2:
        raise SystemExit("Uso: python3 render_carrossel.py carrossel.json [pasta-de-saida]")
    caminho = os.path.abspath(sys.argv[1])
    cfg = json.load(open(caminho, encoding="utf-8"))
    base = os.path.dirname(caminho)
    saida = os.path.abspath(sys.argv[2]) if len(sys.argv) > 2 else os.path.join(base, "carrossel")
    os.makedirs(saida, exist_ok=True)
    slides = cfg.get("slides") or []
    if not slides or slides[-1].get("tipo") != "contato":
        print('Aviso: o último slide deveria ser do tipo "contato".', file=sys.stderr)
    n = len(slides)
    for i, s in enumerate(slides, 1):
        tipo = s.get("tipo")
        if tipo not in TIPOS:
            raise SystemExit(f"Tipo de slide desconhecido: {tipo}")
        tela = Image.new("RGBA", (W, H), rgba(AZUL))
        TIPOS[tipo](tela, s, cfg, base)
        claro = tipo == "planta"
        topo(tela, i, n, claro)
        if tipo != "contato":
            rodape(tela, cfg.get("empreendimento", ""), claro)
        arq = os.path.join(saida, f"slide-{i:02d}.png")
        tela.convert("RGB").save(arq, optimize=True)
        print(arq)


if __name__ == "__main__":
    main()
