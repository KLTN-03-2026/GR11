const fs = require('fs');
const path = require('path');

function walk(dir, files = []) {
  for (const ent of fs.readdirSync(dir, { withFileTypes: true })) {
    const p = path.join(dir, ent.name);
    if (ent.isDirectory() && ent.name !== 'node_modules' && ent.name !== 'api') {
      walk(p, files);
    } else if (/\.(vue|js)$/.test(ent.name)) {
      const norm = p.replace(/\\/g, '/');
      if (!norm.endsWith('api/http.js') && !norm.endsWith('api/endpoints.js')) {
        files.push(p);
      }
    }
  }
  return files;
}

function relImport(file) {
  const depth = file.replace(/\\/g, '/').split('/src/')[1].split('/').length - 1;
  return (depth === 0 ? './' : '../'.repeat(depth)) + 'api/http.js';
}

const srcDir = path.join(__dirname, '..', 'src');
const files = walk(srcDir);

for (const file of files) {
  let c = fs.readFileSync(file, 'utf8');
  const orig = c;

  c = c.replace(/http:\/\/127\.0\.0\.1:8000/g, '${API_BASE}');
  c = c.replace(
    /\(import\.meta\.env\.VITE_API_URL \|\| ['"]http:\/\/127\.0\.0\.1:8000['"]\)\.replace\(\/\\\/\$\/, ['"]\)/g,
    'API_BASE'
  );
  c = c.replace(/import\.meta\.env\.VITE_API_URL \|\| ['"]http:\/\/127\.0\.0\.1:8000['"]/g, 'API_BASE');
  c = c.replace(
    /import\.meta\.env\.VITE_API_URL \|\| ['"]http:\/\/127\.0\.0\.1:8000\/api['"]/g,
    "API_BASE + '/api'"
  );
  c = c.replace(
    /const API_BASE = \(import\.meta\.env\.VITE_API_URL \|\| ['"]http:\/\/127\.0\.0\.1:8000['"]\)\.replace\(\/\\\/\$\/, ['"]\);\n?/g,
    ''
  );
  c = c.replace(/const API = ['"]\$\{API_BASE\}\/api['"];\n?/g, '');
  c = c.replace(/const API_URL = ['"]http:\/\/127\.0\.0\.1:8000\/api\/admin\/quan-ly-tai-khoan['"]/g,
    "const API_URL = API_BASE + '/api/admin/quan-ly-tai-khoan'");

  if (c === orig) continue;

  const hasImport = /from ['"].*api\/http\.js['"]/.test(c);
  if (c.includes('API_BASE') && !hasImport) {
    const rel = relImport(file);
    const imports = ['API_BASE'];
    if (/\bstorageUrl\(/.test(c)) imports.push('storageUrl');
    if (/\bpublicHttp\b/.test(c)) imports.push('publicHttp');
    const importLine = `import { ${imports.join(', ')} } from '${rel}';\n`;

    if (c.includes('<script setup>')) {
      c = c.replace('<script setup>', `<script setup>\n${importLine}`);
    } else if (/<script[^>]*>\s*\nimport /.test(c)) {
      c = c.replace(/(<script[^>]*>\s*\n)(import )/, `$1${importLine}$2`);
    } else if (c.includes('<script>')) {
      c = c.replace('<script>', `<script>\n${importLine}`);
    } else {
      c = importLine + c;
    }
  }

  fs.writeFileSync(file, c);
  console.log('updated:', path.relative(path.join(__dirname, '..'), file));
}
