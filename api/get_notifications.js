// api/get_notifications.js
const { getNotifications } = require('./lib/db');

module.exports = async function handler(req, res) {
  res.setHeader('Content-Type', 'application/json');
  res.setHeader('Access-Control-Allow-Origin', '*');

  try {
    const data = await getNotifications();
    return res.status(200).json(data);
  } catch {
    return res.status(200).json({ count: 0 });
  }
};
