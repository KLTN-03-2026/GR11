const fs = require('fs');
const path = require('path');

function walk(dir, files = []) {
  for (const ent of fs.readdirSync(dir, { withFileTypes: true })) {
    const p = path.join(dir, ent.name);
    if (ent.isDirectory() && ent.name !== 'node_modules') walk(p, files);
    else if (/\.(vue|js)$/.test(ent.name)) files.push(p);
  }
  return files;
}

const srcDir = path.join(__dirname, '..', 'src');

for (const file of walk(srcDir)) {
  const norm = file.replace(/\\/g, '/');
  if (norm.endsWith('api/http.js')) continue;

  let c = fs.readFileSync(file, 'utf8');
  const orig = c;

  // Fix mistaken double-quoted "${API_BASE}/api/..."
  c = c.replace(/"(\$\{API_BASE\}\/api\/[^"]+)"/g, '`$1`');
  c = c.replace(/'(\$\{API_BASE\}\/api\/[^']+)'/g, '`$1`');

  // storage helper
  c = c.replace(
    /`\$\{API_BASE\}\/storage\/\$\{([^}]+)\}`/g,
    'storageUrl($1)'
  );
  c = c.replace(
    /`\$\{API_BASE\}\/storage\/\$\{String\(path\)\.replace\(\/\^\\\/\+\/, ""\)\}`/g,
    'storageUrl(path)'
  );

  if (c !== orig) {
    const needsStorage = /\bstorageUrl\(/.test(c) && !/storageUrl.*from/.test(c);
    if (needsStorage && /from ['"].*api\/http\.js['"]/.test(c)) {
      c = c.replace(
        /import \{ ([^}]+) \} from (['"].*api\/http\.js['"])/,
        (m, imp, from) => {
          if (imp.includes('storageUrl')) return m;
          return `import { ${imp.trim()}, storageUrl } from ${from}`;
        }
      );
    }
    fs.writeFileSync(file, c);
    console.log('fixed:', path.relative(path.join(__dirname, '..'), file));
  }
}
