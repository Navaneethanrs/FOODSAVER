// api/lib/email.js
const nodemailer = require('nodemailer');

async function sendDonationAlert(ngo, donation) {
  const user = process.env.SMTP_USER || 'foodsaver71@gmail.com';
  const pass = process.env.SMTP_PASS || 'hpvgedgupcruhtxc';

  if (!user || !pass) {
    console.log(`[Email Mock] Donation alert for ${ngo.email}: ${donation.food_type} at ${donation.location}`);
    return true;
  }

  try {
    const transporter = nodemailer.createTransport({
      host: process.env.SMTP_HOST || 'smtp.gmail.com',
      port: Number(process.env.SMTP_PORT) || 587,
      secure: false,
      auth: { user, pass }
    });

    const htmlContent = `
      <div style="font-family: Arial, sans-serif; max-width: 600px; margin: auto; padding: 20px; border: 1px solid #ff7f50; border-radius: 10px;">
        <h2 style="color: #ff6347; text-align: center;">🍲 New Food Donation Available!</h2>
        <p>Dear <strong>${ngo.ngo_name || 'NGO Partner'}</strong>,</p>
        <p>A new food donation matches your service area:</p>
        <table style="width: 100%; border-collapse: collapse; margin-top: 15px;">
          <tr><td style="padding: 8px; font-weight: bold; width: 40%;">Food Type:</td><td style="padding: 8px;">${donation.food_type}</td></tr>
          <tr style="background-color: #fff5f2;"><td style="padding: 8px; font-weight: bold;">Quantity:</td><td style="padding: 8px;">${donation.quantity}</td></tr>
          <tr><td style="padding: 8px; font-weight: bold;">Location:</td><td style="padding: 8px;">${donation.location}</td></tr>
          <tr style="background-color: #fff5f2;"><td style="padding: 8px; font-weight: bold;">Donor Name:</td><td style="padding: 8px;">${donation.donor_name}</td></tr>
          <tr><td style="padding: 8px; font-weight: bold;">Donor Contact:</td><td style="padding: 8px;">${donation.donor_contact}</td></tr>
          <tr style="background-color: #fff5f2;"><td style="padding: 8px; font-weight: bold;">Preferred Pickup:</td><td style="padding: 8px;">${donation.pickup_time}</td></tr>
        </table>
        <p style="margin-top: 20px; font-size: 0.9em; color: #666; text-align: center;">
          Sent automatically via Food Saver. Thank you for making a difference!
        </p>
      </div>
    `;

    await transporter.sendMail({
      from: `"Food Saver Notification" <${user}>`,
      to: ngo.email,
      subject: `🍲 New Food Donation: ${donation.food_type} at ${donation.location}`,
      html: htmlContent
    });

    return true;
  } catch (err) {
    console.warn(`Failed to send email to ${ngo.email}:`, err.message);
    return false;
  }
}

module.exports = { sendDonationAlert };
