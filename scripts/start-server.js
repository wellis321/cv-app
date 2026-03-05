#!/usr/bin/env node
/**
 * Start PHP built-in server with upload limits needed for job application files.
 * Port is read from PORT env, or from .env APP_URL (e.g. http://localhost:8001 → 8001).
 * Usage: npm start
 *        PORT=8001 npm start   (override port)
 */
const { spawn } = require('child_process');
const fs = require('fs');
const path = require('path');

function getPort() {
  if (process.env.PORT) return process.env.PORT;
  try {
    const envPath = path.resolve(__dirname, '../.env');
    const content = fs.readFileSync(envPath, 'utf8');
    const line = content.split('\n').find(l => /^\s*APP_URL\s*=/.test(l));
    if (line) {
      const val = line.split('=')[1].trim().replace(/^["']|["']\s*$/g, '');
      const u = new URL(val);
      if (u.port) return u.port;
    }
  } catch (_) {}
  return '8000';
}

const port = getPort();
const php = spawn('php', [
  '-d', 'upload_max_filesize=25M',
  '-d', 'post_max_size=26M',
  '-S', `localhost:${port}`
], {
  stdio: 'inherit',
  cwd: path.resolve(__dirname, '..')
});

php.on('error', (err) => {
  console.error('Failed to start PHP:', err.message);
  process.exit(1);
});

php.on('close', (code) => {
  process.exit(code || 0);
});
