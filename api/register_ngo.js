// api/register_ngo.js
const { parseBody } = require('./lib/parseBody');
const { registerNGO } = require('./lib/db');

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
    const { ngo_name, contact_person, phone_number, email, address, operating_hours, latitude, longitude } = data;

    if (!ngo_name || !contact_person || !phone_number || !email || !address || !operating_hours) {
      return res.status(400).json({ success: false, message: 'All fields are required' });
    }

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
      return res.status(400).json({ success: false, message: 'Invalid email format' });
    }

    const saved = await registerNGO({
      ngo_name: ngo_name.trim(),
      contact_person: contact_person.trim(),
      phone_number: phone_number.trim(),
      email: email.trim(),
      address: address.trim(),
      operating_hours: operating_hours.trim(),
      latitude: latitude ? parseFloat(latitude) : null,
      longitude: longitude ? parseFloat(longitude) : null
    });

    return res.status(200).json({
      success: true,
      message: 'NGO registered successfully!',
      data: { id: saved.id }
    });
  } catch (err) {
    return res.status(200).json({
      success: false,
      message: err.message || 'Registration failed'
    });
  }
};
