// Gera os PNGs do carrossel (1080x1350) a partir de carrossel.html.
// Uso: node marketing/black-friday-carrossel/render.js
const { chromium } = require('playwright');
const path = require('path');

(async () => {
  const dir = __dirname;
  const browser = await chromium.launch();
  const page = await browser.newPage({ viewport: { width: 7560, height: 1350 }, deviceScaleFactor: 1 });
  await page.goto('file://' + path.join(dir, 'carrossel.html'));
  await page.evaluate(() => document.fonts.ready);
  await page.waitForTimeout(400);
  for (let i = 0; i < 7; i++) {
    const file = path.join(dir, `slide-${String(i + 1).padStart(2, '0')}.png`);
    await page.screenshot({ path: file, clip: { x: i * 1080, y: 0, width: 1080, height: 1350 } });
    console.log('ok', file);
  }
  await browser.close();
})();
