import sharp from 'sharp';
import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const __dirname = path.dirname(fileURLToPath(import.meta.url));

function formatBytes(bytes) {
  if (bytes < 1024) return bytes + ' B';
  if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
  return (bytes / (1024 * 1024)).toFixed(2) + ' MB';
}

async function main() {
  const pwaPath = path.join(__dirname, 'public', 'game_pacu', 'assets', 'image', 'ui', 'pwa-icon-512.png');
  const tmpPath = path.join(__dirname, 'public', 'game_pacu', 'assets', 'image', 'ui', 'pwa-icon-512.tmp.png');
  
  if (fs.existsSync(pwaPath)) {
    const originalSize = fs.statSync(pwaPath).size;
    console.log(`Original: ${formatBytes(originalSize)}`);
    
    try {
      // Read the file into buffer first
      const inputBuffer = fs.readFileSync(pwaPath);
      
      const buffer = await sharp(inputBuffer)
        .resize(512, 512, { fit: 'contain', background: { r: 0, g: 0, b: 0, alpha: 0 } })
        .png({ compressionLevel: 9, adaptiveFiltering: true, effort: 10 })
        .toBuffer();
      
      console.log(`Compressed: ${formatBytes(buffer.length)}`);
      
      if (buffer.length < originalSize) {
        // Write to tmp first
        fs.writeFileSync(tmpPath, buffer);
        // Delete original
        fs.unlinkSync(pwaPath);
        // Rename tmp to original
        fs.renameSync(tmpPath, pwaPath);
        console.log(`✓ pwa-icon-512.png berhasil dikompres! Hemat ${((originalSize - buffer.length) / originalSize * 100).toFixed(1)}%`);
      } else {
        console.log(`⏭  sudah optimal`);
      }
    } catch (err) {
      console.error(`✗ Error: ${err.message}`);
      console.error(err.stack);
    }
  } else {
    console.log('File not found!');
  }
}

main().catch(console.error);
