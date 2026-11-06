// api/stats.js
const { getStats } = require('./lib/db');

module.exports = async function handler(req, res) {
  res.setHeader('Content-Type', 'application/json');
  res.setHeader('Access-Control-Allow-Origin', '*');

  try {
    const stats = await getStats();
    return res.status(200).json(stats);
  } catch {
    return res.status(200).json({ meals: 0, food_saved: 0, volunteers: 0 });
  }
};
