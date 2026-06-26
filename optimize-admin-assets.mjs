/**
 * optimize-admin-assets.mjs
 * Mengompresi gambar di public/admin/assets/images
 */

import sharp from 'sharp';
import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const ADMIN_IMAGES = path.join(__dirname, 'public', 'admin', 'assets', 'images');

let totalOriginal = 0;
let totalOptimized = 0;
let filesProcessed = 0;
let filesSkipped = 0;
const results = [];

function formatBytes(bytes) {
  if (bytes < 1024) return bytes + ' B';
  if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
  return (bytes / (1024 * 1024)).toFixed(2) + ' MB';
}

async function optimizeImage(inputPath) {
  const stat = fs.statSync(inputPath);
  const originalSize = stat.size;
  const ext = path.extname(inputPath).toLowerCase();

  try {
    let inputBuffer;
    try {
      inputBuffer = fs.readFileSync(inputPath);
    } catch (readErr) {
      console.log(`  ⚠  Skip ${path.basename(inputPath)}: cannot read`);
      totalOriginal += originalSize;
      totalOptimized += originalSize;
      filesSkipped++;
      return;
    }

    let buffer;
    
    if (ext === '.jpg' || ext === '.jpeg') {
      buffer = await sharp(inputBuffer)
        .jpeg({ quality: 75, mozjpeg: true, progressive: true })
        .toBuffer();
    } else if (ext === '.png') {
      buffer = await sharp(inputBuffer)
        .png({ compressionLevel: 9, adaptiveFiltering: true, effort: 10 })
        .toBuffer();
    } else {
      totalOriginal += originalSize;
      totalOptimized += originalSize;
      filesSkipped++;
      return;
    }

    if (buffer.length < originalSize * 0.98) {
      // Write to tmp then replace
      const tmpPath = inputPath + '.tmp';
      fs.writeFileSync(tmpPath, buffer);
      fs.unlinkSync(inputPath);
      fs.renameSync(tmpPath, inputPath);
      
      const saved = originalSize - buffer.length;
      const pct = ((saved / originalSize) * 100).toFixed(1);
      totalOriginal += originalSize;
      totalOptimized += buffer.length;
      filesProcessed++;
      results.push({
        file: path.relative(__dirname, inputPath),
        original: formatBytes(originalSize),
        optimized: formatBytes(buffer.length),
        saved: formatBytes(saved),
        pct: pct + '%'
      });
      console.log(`  ✓ ${path.basename(inputPath)}: ${formatBytes(originalSize)} → ${formatBytes(buffer.length)} (-${pct}%)`);
    } else {
      totalOriginal += originalSize;
      totalOptimized += originalSize;
      filesSkipped++;
      process.stdout.write('.');
    }
  } catch (err) {
    console.log(`  ⚠  Skip ${path.basename(inputPath)}: ${err.message.split('\n')[0]}`);
    totalOriginal += originalSize;
    totalOptimized += originalSize;
    filesSkipped++;
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
  console.log('🚀 ADMIN ASSETS OPTIMIZER');
  console.log('='.repeat(60));

  const imagePaths = [];
  walkDir(ADMIN_IMAGES, (f) => {
    const ext = path.extname(f).toLowerCase();
    if (['.png', '.jpg', '.jpeg'].includes(ext)) {
      imagePaths.push(f);
    }
  });

  console.log(`\n📷 Ditemukan ${imagePaths.length} gambar di admin/assets/images`);
  console.log('-'.repeat(40));

  // Process in batches to avoid memory issues
  const BATCH = 10;
  for (let i = 0; i < imagePaths.length; i += BATCH) {
    const batch = imagePaths.slice(i, i + BATCH);
    await Promise.all(batch.map(p => optimizeImage(p)));
  }

  const totalSaved = totalOriginal - totalOptimized;
  const totalPct = totalOriginal > 0 ? ((totalSaved / totalOriginal) * 100).toFixed(1) : 0;

  console.log('\n\n' + '='.repeat(60));
  console.log('📊 RINGKASAN');
  console.log('='.repeat(60));
  console.log(`Files dioptimasi : ${filesProcessed}`);
  console.log(`Files dilewati   : ${filesSkipped} (sudah optimal/format lain)`);
  console.log(`Total sebelum    : ${formatBytes(totalOriginal)}`);
  console.log(`Total sesudah    : ${formatBytes(totalOptimized)}`);
  console.log(`Total hemat      : ${formatBytes(totalSaved)} (${totalPct}%)`);
  console.log('='.repeat(60));
  console.log('\n✅ Admin assets berhasil dioptimasi!');
}

main().catch(console.error);
