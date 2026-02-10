#!/usr/bin/env node
/**
 * Sync b2b-cv-app (web app) into simple-cv-builder-desktop/app/
 * Preserves desktop-only files: app/php/config.php, app/php/database.php
 *
 * Usage:
 *   SIMPLE_CV_DESKTOP_PATH=/path/to/simple-cv-builder-desktop node scripts/sync-to-desktop.js
 *   node scripts/sync-to-desktop.js /path/to/simple-cv-builder-desktop
 *
 * Run from b2b-cv-app repo root.
 */

const fs = require('fs');
const path = require('path');

const SOURCE_ROOT = path.resolve(__dirname, '..');
// Preserve desktop-only files (org checks, SQLite config)
const DESKTOP_PRESERVE = ['php/config.php', 'php/database.php', 'php/authorisation.php'];
const DIRS_TO_COPY = ['php', 'views', 'api', 'js', 'static', 'templates', 'resources'];
const SKIP_DIR_NAMES = new Set(['.env', '.git', 'node_modules', 'vendor', 'b2b-cv-app']);

function getTargetRoot() {
  const env = process.env.SIMPLE_CV_DESKTOP_PATH;
  if (env) return path.resolve(env);
  const arg = process.argv[2];
  if (arg) return path.resolve(arg);
  console.error('Usage: SIMPLE_CV_DESKTOP_PATH=/path/to/simple-cv-builder-desktop node scripts/sync-to-desktop.js');
  console.error('   or: node scripts/sync-to-desktop.js /path/to/simple-cv-builder-desktop');
  process.exit(1);
}

function mkdirRecursive(dirPath) {
  const parent = path.dirname(dirPath);
  if (!fs.existsSync(parent)) mkdirRecursive(parent);
  if (!fs.existsSync(dirPath)) fs.mkdirSync(dirPath, { recursive: true });
}

function copyFile(src, dest, relativePath) {
  const normalized = relativePath.replace(/\\/g, '/');
  const preserve = DESKTOP_PRESERVE.some(p => normalized === p || normalized.endsWith('/' + p));
  if (preserve) {
    console.log('  [skip] ' + relativePath + ' (desktop version preserved)');
    return;
  }
  mkdirRecursive(path.dirname(dest));
  fs.copyFileSync(src, dest);
  console.log('  ' + relativePath);
}

function copyDir(srcDir, destDir, relativePrefix, dirName) {
  if (!fs.existsSync(srcDir)) return;
  const entries = fs.readdirSync(srcDir, { withFileTypes: true });
  for (const ent of entries) {
    const srcPath = path.join(srcDir, ent.name);
    const destPath = path.join(destDir, ent.name);
    const rel = relativePrefix ? relativePrefix + '/' + ent.name : ent.name;
    if (ent.isDirectory()) {
      if (SKIP_DIR_NAMES.has(ent.name)) continue;
      copyDir(srcPath, destPath, rel, ent.name);
    } else {
      copyFile(srcPath, destPath, rel);
    }
  }
}

function main() {
  const targetRoot = getTargetRoot();
  const appRoot = path.join(targetRoot, 'app');
  // Desktop uses app/php/b2b-cv-app as document root - sync there when it exists
  const b2bAppDir = path.join(appRoot, 'php', 'b2b-cv-app');
  const appDest = fs.existsSync(b2bAppDir) ? b2bAppDir : appRoot;

  if (!fs.existsSync(targetRoot)) {
    console.error('Target not found: ' + targetRoot);
    process.exit(1);
  }
  if (!fs.existsSync(appRoot)) {
    console.error('Target app/ not found: ' + appRoot);
    process.exit(1);
  }

  console.log('Syncing b2b-cv-app -> ' + appDest + '\n');

  for (const dir of DIRS_TO_COPY) {
    const src = path.join(SOURCE_ROOT, dir);
    const dest = path.join(appDest, dir);
    if (!fs.existsSync(src)) {
      console.log('[skip dir] ' + dir + ' (not found in source)');
      continue;
    }
    console.log('Dir: ' + dir + '/');
    copyDir(src, dest, dir, dir);
  }

  console.log('\nRoot *.php');
  const rootPhp = fs.readdirSync(SOURCE_ROOT, { withFileTypes: true })
    .filter(e => e.isFile() && e.name.endsWith('.php'))
    .map(e => e.name);
  for (const name of rootPhp) {
    const src = path.join(SOURCE_ROOT, name);
    const dest = path.join(appDest, name);
    fs.copyFileSync(src, dest);
    console.log('  ' + name);
  }

  const composerSrc = path.join(SOURCE_ROOT, 'composer.json');
  const composerDest = path.join(appDest, 'composer.json');
  if (fs.existsSync(composerSrc)) {
    fs.copyFileSync(composerSrc, composerDest);
    console.log('\ncomposer.json');
  }

  console.log('\nDone. Desktop config/database (php/config.php, php/database.php) were not overwritten.');
}

main();
