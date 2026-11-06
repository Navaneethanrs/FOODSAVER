// api/get_accepted_donations.js
const { getAcceptedDonations } = require('./lib/db');

module.exports = async function handler(req, res) {
  res.setHeader('Content-Type', 'application/json');
  res.setHeader('Access-Control-Allow-Origin', '*');

  try {
    const list = await getAcceptedDonations();
    return res.status(200).json(list);
  } catch {
    return res.status(200).json([]);
  }
};
