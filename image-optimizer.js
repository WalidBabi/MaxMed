import fs from 'fs';
import path from 'path';
import sharp from 'sharp';
import { fileURLToPath } from 'url';

const __dirname = path.dirname(fileURLToPath(import.meta.url));

// Source and destination directories
const sourceDir = path.join(__dirname, 'public', 'Images');
const destDir = path.join(__dirname, 'public', 'Images', 'optimized');

// Create destination directory if it doesn't exist
if (!fs.existsSync(destDir)) {
    fs.mkdirSync(destDir, { recursive: true });
}

// Common image sizes - adjust based on your needs
const sizes = {
    banner: { width: 1920, height: null }, // Full width banners
    product: { width: 600, height: null },  // Product images
    thumbnail: { width: 300, height: null }, // Thumbnails
    logo: { width: 200, height: null }      // Logos
};

// Process images
async function optimizeImages() {
    try {
        const files = fs.readdirSync(sourceDir);
        
        for (const file of files) {
            const filePath = path.join(sourceDir, file);
            const stats = fs.statSync(filePath);
            
            if (!stats.isFile()) continue;
            
            const ext = path.extname(file).toLowerCase();
            if (!['.jpg', '.jpeg', '.png', '.gif'].includes(ext)) continue;
            
            const fileName = path.basename(file, ext);
            
            // Skip already optimized images
            if (fileName.includes('-optimized')) continue;

            console.log(`Processing: ${file}`);
            
            // Determine appropriate size based on filename or file size
            let targetSize = sizes.product; // Default size
            
            if (file.includes('banner') || file.includes('Header')) {
                targetSize = sizes.banner;
            } else if (file.includes('logo') || file.includes('supplier')) {
                targetSize = sizes.logo;
            } else if (stats.size < 100000) { // Less than 100KB
                targetSize = sizes.thumbnail;
            }
            
            // Original WebP at full size (for responsive loading)
            await sharp(filePath)
                .webp({ quality: 80 })
                .toFile(path.join(destDir, `${fileName}-original.webp`));
                
            // Resize and optimize
            await sharp(filePath)
                .resize({
                    width: targetSize.width,
                    height: targetSize.height,
                    fit: 'inside',
                    withoutEnlargement: true
                })
                .webp({ quality: 80 })
                .toFile(path.join(destDir, `${fileName}-optimized.webp`));
                
            console.log(`Optimized: ${fileName}`);
        }
        
        console.log('Image optimization complete!');
    } catch (error) {
        console.error('Error optimizing images:', error);
    }
}

optimizeImages(); 