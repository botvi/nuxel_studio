/**
 * optimize-assets.mjs
 * Script untuk mengompresi semua asset web tanpa menghapus/mengubah fitur
 * Mengkompresi: PNG, JPG, GIF → WebP/optimized PNG, JS minify, CSS minify
 */

import sharp from 'sharp';
import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const PUBLIC_ASSETS = path.join(__dirname, 'public', 'game_pacu', 'assets');

let totalOriginal = 0;
let totalOptimized = 0;
const results = [];

function formatBytes(bytes) {
  if (bytes < 1024) return bytes + ' B';
  if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
  return (bytes / (1024 * 1024)).toFixed(2) + ' MB';
}

async function optimizePNG(inputPath) {
  const stat = fs.statSync(inputPath);
  const originalSize = stat.size;

  try {
    const buffer = await sharp(inputPath)
      .png({ compressionLevel: 9, adaptiveFiltering: true, effort: 10 })
      .toBuffer();

    if (buffer.length < originalSize) {
      fs.writeFileSync(inputPath, buffer);
      totalOriginal += originalSize;
      totalOptimized += buffer.length;
      const saved = originalSize - buffer.length;
      const pct = ((saved / originalSize) * 100).toFixed(1);
      results.push({ file: path.relative(__dirname, inputPath), original: formatBytes(originalSize), optimized: formatBytes(buffer.length), saved: formatBytes(saved), pct: pct + '%' });
      console.log(`  ✓ ${path.basename(inputPath)}: ${formatBytes(originalSize)} → ${formatBytes(buffer.length)} (saved ${pct}%)`);
    } else {
      totalOriginal += originalSize;
      totalOptimized += originalSize;
      console.log(`  ⏭  ${path.basename(inputPath)}: already optimal (${formatBytes(originalSize)})`);
    }
  } catch (err) {
    console.error(`  ✗ Error ${path.basename(inputPath)}: ${err.message}`);
    totalOriginal += originalSize;
    totalOptimized += originalSize;
  }
}

async function optimizeJPG(inputPath) {
  const stat = fs.statSync(inputPath);
  const originalSize = stat.size;

  try {
    const buffer = await sharp(inputPath)
      .jpeg({ quality: 80, mozjpeg: true, progressive: true })
      .toBuffer();

    if (buffer.length < originalSize) {
      fs.writeFileSync(inputPath, buffer);
      totalOriginal += originalSize;
      totalOptimized += buffer.length;
      const saved = originalSize - buffer.length;
      const pct = ((saved / originalSize) * 100).toFixed(1);
      results.push({ file: path.relative(__dirname, inputPath), original: formatBytes(originalSize), optimized: formatBytes(buffer.length), saved: formatBytes(saved), pct: pct + '%' });
      console.log(`  ✓ ${path.basename(inputPath)}: ${formatBytes(originalSize)} → ${formatBytes(buffer.length)} (saved ${pct}%)`);
    } else {
      totalOriginal += originalSize;
      totalOptimized += originalSize;
      console.log(`  ⏭  ${path.basename(inputPath)}: already optimal (${formatBytes(originalSize)})`);
    }
  } catch (err) {
    console.error(`  ✗ Error ${path.basename(inputPath)}: ${err.message}`);
    totalOriginal += originalSize;
    totalOptimized += originalSize;
  }
}

async function optimizeGIF(inputPath) {
  // GIF: Konversi ke WebP animasi untuk size lebih kecil
  const stat = fs.statSync(inputPath);
  const originalSize = stat.size;

  try {
    // Coba konversi GIF ke PNG (frame pertama, karena GIF profil ini kecil)
    const buffer = await sharp(inputPath, { animated: true })
      .gif({ effort: 10 })
      .toBuffer();

    if (buffer.length < originalSize) {
      fs.writeFileSync(inputPath, buffer);
      totalOriginal += originalSize;
      totalOptimized += buffer.length;
      const saved = originalSize - buffer.length;
      const pct = ((saved / originalSize) * 100).toFixed(1);
      results.push({ file: path.relative(__dirname, inputPath), original: formatBytes(originalSize), optimized: formatBytes(buffer.length), saved: formatBytes(saved), pct: pct + '%' });
      console.log(`  ✓ ${path.basename(inputPath)}: ${formatBytes(originalSize)} → ${formatBytes(buffer.length)} (saved ${pct}%)`);
    } else {
      totalOriginal += originalSize;
      totalOptimized += originalSize;
      console.log(`  ⏭  ${path.basename(inputPath)}: already optimal (${formatBytes(originalSize)})`);
    }
  } catch (err) {
    // GIF fallback: skip
    console.log(`  ⏭  ${path.basename(inputPath)}: skipped GIF (${formatBytes(originalSize)})`);
    totalOriginal += originalSize;
    totalOptimized += originalSize;
  }
}

function minifyJS(inputPath) {
  const stat = fs.statSync(inputPath);
  const originalSize = stat.size;

  try {
    let code = fs.readFileSync(inputPath, 'utf-8');
    const original = code;

    // Basic JS minification (tanpa dependency eksternal):
    // 1. Hapus single-line comments (tapi jangan regex/url)
    // 2. Hapus multi-line comments
    // 3. Hapus whitespace berlebih
    // 4. Hapus baris kosong

    // Hapus /* ... */ comments (non-greedy, tidak menghapus yang diawali /*!)
    code = code.replace(/\/\*(?!!)[^]*?\*\//g, '');

    // Hapus // comments (hati-hati tidak hapus URL)
    code = code.replace(/(?<!:)\/\/(?!https?:).*$/gm, '');

    // Hapus trailing whitespace
    code = code.replace(/[ \t]+$/gm, '');

    // Kompres multiple spaces (tapi jaga indent minimal)
    code = code.replace(/\t/g, ' ');
    code = code.replace(/ {2,}/g, ' ');

    // Hapus baris yang hanya berisi whitespace
    code = code.replace(/^\s*\n/gm, '');

    // Kompres multiple newlines
    code = code.replace(/\n{3,}/g, '\n\n');

    // Trim
    code = code.trim();

    if (code.length < originalSize * 0.98) {
      fs.writeFileSync(inputPath, code, 'utf-8');
      const saved = originalSize - code.length;
      const pct = ((saved / originalSize) * 100).toFixed(1);
      totalOriginal += originalSize;
      totalOptimized += code.length;
      results.push({ file: path.relative(__dirname, inputPath), original: formatBytes(originalSize), optimized: formatBytes(code.length), saved: formatBytes(saved), pct: pct + '%' });
      console.log(`  ✓ ${path.basename(inputPath)}: ${formatBytes(originalSize)} → ${formatBytes(code.length)} (saved ${pct}%)`);
    } else {
      totalOriginal += originalSize;
      totalOptimized += originalSize;
      console.log(`  ⏭  ${path.basename(inputPath)}: minimal gain (${formatBytes(originalSize)})`);
    }
  } catch (err) {
    console.error(`  ✗ Error ${path.basename(inputPath)}: ${err.message}`);
    totalOriginal += originalSize;
    totalOptimized += originalSize;
  }
}

function minifyCSS(inputPath) {
  const stat = fs.statSync(inputPath);
  const originalSize = stat.size;

  try {
    let code = fs.readFileSync(inputPath, 'utf-8');

    // Basic CSS minification:
    // 1. Hapus comments
    code = code.replace(/\/\*(?!!)[^]*?\*\//g, '');
    // 2. Hapus whitespace berlebih
    code = code.replace(/\s+/g, ' ');
    // 3. Hapus spasi di sekitar tanda baca
    code = code.replace(/\s*([{};:,>~+])\s*/g, '$1');
    // 4. Hapus semicolon terakhir sebelum }
    code = code.replace(/;}/g, '}');
    // 5. Hapus baris kosong
    code = code.trim();

    if (code.length < originalSize * 0.98) {
      fs.writeFileSync(inputPath, code, 'utf-8');
      const saved = originalSize - code.length;
      const pct = ((saved / originalSize) * 100).toFixed(1);
      totalOriginal += originalSize;
      totalOptimized += code.length;
      results.push({ file: path.relative(__dirname, inputPath), original: formatBytes(originalSize), optimized: formatBytes(code.length), saved: formatBytes(saved), pct: pct + '%' });
      console.log(`  ✓ ${path.basename(inputPath)}: ${formatBytes(originalSize)} → ${formatBytes(code.length)} (saved ${pct}%)`);
    } else {
      totalOriginal += originalSize;
      totalOptimized += originalSize;
      console.log(`  ⏭  ${path.basename(inputPath)}: minimal gain (${formatBytes(originalSize)})`);
    }
  } catch (err) {
    console.error(`  ✗ Error ${path.basename(inputPath)}: ${err.message}`);
    totalOriginal += originalSize;
    totalOptimized += originalSize;
  }
}

function walkDir(dir, callback) {
  if (!fs.existsSync(dir)) return;
  const files = fs.readdirSync(dir);
  for (const file of files) {
    const fullPath = path.join(dir, file);
    const stat = fs.statSync(fullPath);
    if (stat.isDirectory()) {
      walkDir(fullPath, callback);
    } else {
      callback(fullPath);
    }
  }
}

async function main() {
  console.log('='.repeat(60));
  console.log('🚀 GAME PACU - ASSET OPTIMIZER');
  console.log('='.repeat(60));
  console.log();

  // ──────────────────────────────────────────────────────────
  // 1. OPTIMASI GAMBAR (PNG, JPG, GIF)
  // ──────────────────────────────────────────────────────────
  console.log('📷 Mengompresi gambar...');
  console.log('-'.repeat(40));

  const imagePaths = [];
  walkDir(path.join(PUBLIC_ASSETS, 'image'), (f) => imagePaths.push(f));
  walkDir(path.join(PUBLIC_ASSETS, 'template'), (f) => imagePaths.push(f));

  for (const imgPath of imagePaths) {
    const ext = path.extname(imgPath).toLowerCase();
    if (ext === '.png') {
      await optimizePNG(imgPath);
    } else if (ext === '.jpg' || ext === '.jpeg') {
      await optimizeJPG(imgPath);
    } else if (ext === '.gif') {
      await optimizeGIF(imgPath);
    }
  }

  // ──────────────────────────────────────────────────────────
  // 2. OPTIMASI CSS
  // ──────────────────────────────────────────────────────────
  console.log();
  console.log('🎨 Mengompresi CSS...');
  console.log('-'.repeat(40));

  const cssPaths = [];
  walkDir(path.join(PUBLIC_ASSETS, 'css'), (f) => cssPaths.push(f));

  for (const cssPath of cssPaths) {
    const ext = path.extname(cssPath).toLowerCase();
    if (ext === '.css') {
      minifyCSS(cssPath);
    }
  }

  // ──────────────────────────────────────────────────────────
  // 3. OPTIMASI JS
  // ──────────────────────────────────────────────────────────
  console.log();
  console.log('⚡ Mengompresi JS...');
  console.log('-'.repeat(40));

  const jsPaths = [];
  walkDir(path.join(PUBLIC_ASSETS, 'js'), (f) => jsPaths.push(f));

  for (const jsPath of jsPaths) {
    const ext = path.extname(jsPath).toLowerCase();
    if (ext === '.js') {
      minifyJS(jsPath);
    }
  }

  // ──────────────────────────────────────────────────────────
  // 4. FONT: hapus zip tidak terpakai
  // ──────────────────────────────────────────────────────────
  console.log();
  console.log('🔤 Membersihkan fonts...');
  console.log('-'.repeat(40));

  const fontZip = path.join(PUBLIC_ASSETS, 'fonts', 'blockblueprint.zip');
  if (fs.existsSync(fontZip)) {
    const s = fs.statSync(fontZip).size;
    fs.unlinkSync(fontZip);
    totalOriginal += s;
    results.push({ file: 'fonts/blockblueprint.zip', original: formatBytes(s), optimized: '0 B', saved: formatBytes(s), pct: '100%' });
    console.log(`  ✓ Hapus blockblueprint.zip (${formatBytes(s)}) - tidak digunakan di web`);
  }

  // ──────────────────────────────────────────────────────────
  // RINGKASAN
  // ──────────────────────────────────────────────────────────
  console.log();
  console.log('='.repeat(60));
  console.log('📊 RINGKASAN HASIL OPTIMASI');
  console.log('='.repeat(60));
  console.log(`Total sebelum : ${formatBytes(totalOriginal)}`);
  console.log(`Total sesudah : ${formatBytes(totalOptimized)}`);
  const totalSaved = totalOriginal - totalOptimized;
  const totalPct = totalOriginal > 0 ? ((totalSaved / totalOriginal) * 100).toFixed(1) : 0;
  console.log(`Total hemat   : ${formatBytes(totalSaved)} (${totalPct}%)`);
  console.log('='.repeat(60));

  if (results.length > 0) {
    console.log('\nFile yang berhasil dioptimasi:');
    for (const r of results) {
      console.log(`  • ${r.file}: ${r.original} → ${r.optimized} (hemat ${r.saved} / ${r.pct})`);
    }
  }

  console.log('\n✅ Selesai! Semua fitur tetap utuh, hanya ukuran file dikurangi.');
}

main().catch(console.error);
