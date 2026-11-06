// server.js - Local dev server simulating Vercel Serverless & Static hosting
const http = require('http');
const fs = require('fs');
const path = require('path');

const PORT = process.env.PORT || 3000;

const MIME_TYPES = {
  '.html': 'text/html',
  '.css': 'text/css',
  '.js': 'text/javascript',
  '.json': 'application/json',
  '.png': 'image/png',
  '.jpg': 'image/jpeg',
  '.jpeg': 'image/jpeg',
  '.svg': 'image/svg+xml',
  '.ico': 'image/x-icon'
};

const rewrites = {
  '/register_ngo_fixed.php': './api/register_ngo.js',
  '/register_ngo.php': './api/register_ngo.js',
  '/save_donation_fixed_gmail.php': './api/save_donation.js',
  '/save_donation.php': './api/save_donation.js',
  '/get_notifications.php': './api/get_notifications.js',
  '/get_accepted_donations.php': './api/get_accepted_donations.js',
  '/get_stats.php': './api/stats.js',
  '/view_ngos.php': 'view_ngos.html',
  '/admin_dashboard.php': 'admin.html',
  '/view_donations.php': 'admin.html'
};

const server = http.createServer(async (req, res) => {
  const parsedUrl = new URL(req.url, `http://${req.headers.host}`);
  let pathname = parsedUrl.pathname;

  if (pathname === '/') pathname = '/index.html';

  // Check rewrites
  if (rewrites[pathname]) {
    const target = rewrites[pathname];
    if (target.endsWith('.js')) {
      const handler = require(path.join(__dirname, target));
      res.status = (code) => { res.statusCode = code; return res; };
      res.json = (data) => {
        res.setHeader('Content-Type', 'application/json');
        res.end(JSON.stringify(data));
      };
      return handler(req, res);
    } else {
      pathname = '/' + target;
    }
  }

  // Check /api/ routes
  if (pathname.startsWith('/api/')) {
    const apiFile = pathname.replace('/api/', '') + '.js';
    const apiPath = path.join(__dirname, 'api', apiFile);
    if (fs.existsSync(apiPath)) {
      const handler = require(apiPath);
      res.status = (code) => { res.statusCode = code; return res; };
      res.json = (data) => {
        res.setHeader('Content-Type', 'application/json');
        res.end(JSON.stringify(data));
      };
      return handler(req, res);
    }
  }

  // Static file serving
  const filePath = path.join(__dirname, pathname.replace(/^\//, ''));
  fs.stat(filePath, (err, stats) => {
    if (err || !stats.isFile()) {
      res.statusCode = 404;
      res.end('Not Found');
      return;
    }

    const ext = path.extname(filePath).toLowerCase();
    const contentType = MIME_TYPES[ext] || 'application/octet-stream';
    res.writeHead(200, { 'Content-Type': contentType });
    fs.createReadStream(filePath).pipe(res);
  });
});

server.listen(PORT, () => {
  console.log(`Food Saver server running at http://localhost:${PORT}`);
});
