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

const patterns = [
  [/apiBase:\s*\(import\.meta\.env\.VITE_API_URL \|\| ['"]\$\{API_BASE\}['"]\)\.replace\(\/\\\/\$\/, ['"]\),?/g, 'apiBase: API_BASE,'],
  [/apiBase:\s*import\.meta\.env\.VITE_API_URL \|\| ['"]\$\{API_BASE\}['"],?/g, 'apiBase: API_BASE,'],
  [/const base = \(import\.meta\.env\.VITE_API_URL \|\| ['"]\$\{API_BASE\}['"]\)\.replace\(\/\\\/\$\/, ['"]\)/g, 'const base = API_BASE'],
  [/import\.meta\.env\.VITE_API_URL \|\| ['"]\$\{API_BASE\}['"]/g, 'API_BASE'],
  [/const API_BASE = \(import\.meta\.env\.VITE_API_URL \|\| ['"]\$\{API_BASE\}['"]\)\.replace\(\/\\\/\$\/, ['"]\);\n?/g, ''],
  [/const u = \(import\.meta\.env\.VITE_API_URL \|\| ['"]\$\{API_BASE\}['"]\)\.replace\(\/\\\/\$\/, ['"]\)/g, 'const u = API_BASE'],
  [/authEndpoint:\s*\(import\.meta\.env\.VITE_API_URL \|\| ['"]\$\{API_BASE\}['"]\)\.replace\(\/\\\/\$\/, ['"]\) \+ '\/api\/broadcasting\/auth'/g,
    "authEndpoint: API_BASE + '/api/broadcasting/auth'"],
];

for (const file of walk(path.join(__dirname, '..', 'src'))) {
  if (file.replace(/\\/g, '/').endsWith('api/http.js')) continue;
  let c = fs.readFileSync(file, 'utf8');
  const orig = c;
  for (const [re, rep] of patterns) c = c.replace(re, rep);
  if (c !== orig) {
    fs.writeFileSync(file, c);
    console.log('fixed:', path.relative(path.join(__dirname, '..'), file));
  }
}
