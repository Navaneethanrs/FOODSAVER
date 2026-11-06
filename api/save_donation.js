// api/save_donation.js
const { parseBody } = require('./lib/parseBody');
const { saveDonation, findMatchingNGOs } = require('./lib/db');
const { sendDonationAlert } = require('./lib/email');

module.exports = async function handler(req, res) {
  res.setHeader('Content-Type', 'application/json');
  res.setHeader('Access-Control-Allow-Origin', '*');
  res.setHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');
  res.setHeader('Access-Control-Allow-Headers', 'Content-Type');

  if (req.method === 'OPTIONS') {
    return res.status(200).end();
  }

  if (req.method !== 'POST') {
    return res.status(405).json({ success: false, message: 'Invalid request method' });
  }

  try {
    const data = await parseBody(req);
    const { donor_name, food_type, quantity, location, donor_contact, pickup_time, latitude, longitude } = data;

    if (!donor_name || !food_type || !quantity || !location || !donor_contact || !pickup_time) {
      return res.status(400).json({ success: false, message: 'All fields are required' });
    }

    const saved = await saveDonation({
      donor_name: donor_name.trim(),
      food_type: food_type.trim(),
      quantity: quantity.trim(),
      location: location.trim(),
      donor_contact: donor_contact.trim(),
      pickup_time: pickup_time.trim(),
      latitude: latitude ? parseFloat(latitude) : null,
      longitude: longitude ? parseFloat(longitude) : null
    });

    // Alert matching NGOs in background
    let emailsSent = 0;
    try {
      const matching = await findMatchingNGOs(location);
      for (const ngo of matching) {
        const sent = await sendDonationAlert(ngo, saved);
        if (sent) emailsSent++;
      }
    } catch (e) {
      console.warn("Error alerting NGOs:", e.message);
    }

    const message = emailsSent > 0
      ? `Donation submitted! ${emailsSent} NGO(s) notified.`
      : 'Donation submitted! Matching NGOs will be alerted.';

    return res.status(200).json({
      success: true,
      message,
      data: { id: saved.id }
    });
  } catch (err) {
    return res.status(500).json({
      success: false,
      message: err.message || 'Error saving donation'
    });
  }
};
