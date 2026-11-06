// api/lib/parseBody.js
// Handles JSON, urlencoded, and multipart FormData payloads

async function parseBody(req) {
  // If already parsed by Vercel
  if (req.body && typeof req.body === 'object' && !Buffer.isBuffer(req.body)) {
    return req.body;
  }

  // Read raw body stream if not buffered
  let raw = '';
  if (Buffer.isBuffer(req.body)) {
    raw = req.body.toString('utf-8');
  } else if (typeof req.body === 'string') {
    raw = req.body;
  } else {
    raw = await new Promise((resolve, reject) => {
      let data = '';
      req.on('data', chunk => { data += chunk; });
      req.on('end', () => resolve(data));
      req.on('error', err => reject(err));
    });
  }

  if (!raw) return {};

  const contentType = req.headers['content-type'] || '';

  // 1. JSON
  if (contentType.includes('application/json')) {
    try {
      return JSON.parse(raw);
    } catch {
      return {};
    }
  }

  // 2. URL-encoded
  if (contentType.includes('application/x-www-form-urlencoded')) {
    const params = new URLSearchParams(raw);
    const result = {};
    for (const [key, value] of params.entries()) {
      result[key] = value;
    }
    return result;
  }

  // 3. Multipart / form-data
  if (contentType.includes('multipart/form-data')) {
    const boundaryMatch = contentType.match(/boundary=(?:"([^"]+)"|([^;]+))/i);
    const boundary = boundaryMatch ? (boundaryMatch[1] || boundaryMatch[2]) : null;
    if (boundary) {
      const result = {};
      const parts = raw.split('--' + boundary);
      for (const part of parts) {
        const match = part.match(/name="([^"]+)"[\r\n]+([\s\S]*?)[\r\n]*$/);
        if (match) {
          const name = match[1];
          const val = match[2].trim();
          result[name] = val;
        }
      }
      return result;
    }
  }

  // Fallback: try JSON parse
  try {
    return JSON.parse(raw);
  } catch {
    return {};
  }
}

module.exports = { parseBody };
