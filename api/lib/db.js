// api/lib/db.js
// Supports MySQL, PostgreSQL (Supabase / Neon), and safe in-memory fallback for Vercel

let pool = null;
let dbType = null; // 'mysql' | 'postgres' | 'memory'

// In-memory fallback storage
let memoryNgos = [
  {
    id: 1,
    ngo_name: "Feed the Hungry Foundation",
    contact_person: "Sarah Jenkins",
    phone_number: "9876543210",
    email: "contact@feedthehungry.org",
    address: "Downtown Shelter, Main Street",
    operating_hours: "9:00 AM - 8:00 PM",
    latitude: 12.9716,
    longitude: 77.5946,
    registration_date: new Date().toISOString()
  },
  {
    id: 2,
    ngo_name: "City Food Share",
    contact_person: "David Miller",
    phone_number: "9123456780",
    email: "info@cityfoodshare.org",
    address: "Community Center, 5th Avenue",
    operating_hours: "8:00 AM - 10:00 PM",
    latitude: 12.9352,
    longitude: 77.6245,
    registration_date: new Date().toISOString()
  }
];

let memoryDonations = [
  {
    id: 1,
    donor_name: "Grand Hotel",
    food_type: "Buffet Spread (Rice, Curries, Breads)",
    quantity: "35 Meals",
    location: "Downtown",
    donor_contact: "9887766554",
    pickup_time: "Within 2 Hours",
    latitude: 12.9720,
    longitude: 77.5950,
    status: "accepted",
    created_at: new Date(Date.now() - 3600000).toISOString()
  },
  {
    id: 2,
    donor_name: "Fresh Bakery",
    food_type: "Packaged Breads & Buns",
    quantity: "20 Packets",
    location: "5th Avenue",
    donor_contact: "9776655443",
    pickup_time: "Today evening",
    latitude: 12.9360,
    longitude: 77.6250,
    status: "accepted",
    created_at: new Date(Date.now() - 7200000).toISOString()
  }
];

async function getPool() {
  if (pool) return { pool, dbType };

  const dbUrl = process.env.MYSQL_URL || process.env.DATABASE_URL || process.env.MYSQL_URI;

  if (dbUrl && (dbUrl.startsWith('postgres://') || dbUrl.startsWith('postgresql://'))) {
    try {
      const { Pool } = require('pg');
      pool = new Pool({
        connectionString: dbUrl,
        ssl: { rejectUnauthorized: false }
      });
      dbType = 'postgres';
      await initPostgres(pool);
      return { pool, dbType };
    } catch (err) {
      console.warn("Postgres connection failed, falling back to memory:", err.message);
    }
  }

  if (dbUrl && (dbUrl.startsWith('mysql://') || process.env.MYSQL_HOST)) {
    try {
      const mysql = require('mysql2/promise');
      if (dbUrl) {
        pool = mysql.createPool(dbUrl);
      } else {
        pool = mysql.createPool({
          host: process.env.MYSQL_HOST,
          user: process.env.MYSQL_USER || 'root',
          password: process.env.MYSQL_PASSWORD || '',
          database: process.env.MYSQL_DATABASE || 'food_saver',
          port: Number(process.env.MYSQL_PORT) || 3306,
          waitForConnections: true,
          connectionLimit: 10
        });
      }
      dbType = 'mysql';
      await initMySQL(pool);
      return { pool, dbType };
    } catch (err) {
      console.warn("MySQL connection failed, falling back to memory:", err.message);
    }
  }

  dbType = 'memory';
  return { pool: null, dbType: 'memory' };
}

async function initMySQL(p) {
  await p.query(`
    CREATE TABLE IF NOT EXISTS donations (
      id INT AUTO_INCREMENT PRIMARY KEY,
      donor_name VARCHAR(255) NOT NULL,
      food_type VARCHAR(255) NOT NULL,
      quantity VARCHAR(255) NOT NULL,
      location TEXT NOT NULL,
      donor_contact VARCHAR(20) NOT NULL,
      pickup_time VARCHAR(50) NOT NULL,
      latitude DECIMAL(10, 8) NULL,
      longitude DECIMAL(11, 8) NULL,
      status VARCHAR(50) DEFAULT 'pending',
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )
  `);

  await p.query(`
    CREATE TABLE IF NOT EXISTS ngo_registrations (
      id INT AUTO_INCREMENT PRIMARY KEY,
      ngo_name VARCHAR(255) NOT NULL,
      contact_person VARCHAR(255) NOT NULL,
      phone_number VARCHAR(20) NOT NULL,
      email VARCHAR(255) UNIQUE NOT NULL,
      address TEXT NOT NULL,
      operating_hours VARCHAR(100) NOT NULL,
      latitude DECIMAL(10, 8) NULL,
      longitude DECIMAL(11, 8) NULL,
      registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )
  `);
}

async function initPostgres(p) {
  await p.query(`
    CREATE TABLE IF NOT EXISTS donations (
      id SERIAL PRIMARY KEY,
      donor_name VARCHAR(255) NOT NULL,
      food_type VARCHAR(255) NOT NULL,
      quantity VARCHAR(255) NOT NULL,
      location TEXT NOT NULL,
      donor_contact VARCHAR(20) NOT NULL,
      pickup_time VARCHAR(50) NOT NULL,
      latitude NUMERIC(10, 8),
      longitude NUMERIC(11, 8),
      status VARCHAR(50) DEFAULT 'pending',
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );
  `);

  await p.query(`
    CREATE TABLE IF NOT EXISTS ngo_registrations (
      id SERIAL PRIMARY KEY,
      ngo_name VARCHAR(255) NOT NULL,
      contact_person VARCHAR(255) NOT NULL,
      phone_number VARCHAR(20) NOT NULL,
      email VARCHAR(255) UNIQUE NOT NULL,
      address TEXT NOT NULL,
      operating_hours VARCHAR(100) NOT NULL,
      latitude NUMERIC(10, 8),
      longitude NUMERIC(11, 8),
      registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );
  `);
}

// ----------------- PUBLIC METHODS -----------------

async function getStats() {
  const { pool, dbType } = await getPool();
  if (dbType === 'mysql') {
    const [[mealsRow]] = await pool.query('SELECT COUNT(*) as count FROM donations');
    const [[savedRow]] = await pool.query("SELECT COUNT(*) as count FROM donations WHERE status = 'accepted'");
    const [[ngoRow]] = await pool.query('SELECT COUNT(*) as count FROM ngo_registrations');
    return {
      meals: Number(mealsRow?.count || 0),
      food_saved: Number(savedRow?.count || 0),
      volunteers: Number(ngoRow?.count || 0)
    };
  }
  if (dbType === 'postgres') {
    const mealsRes = await pool.query('SELECT COUNT(*) as count FROM donations');
    const savedRes = await pool.query("SELECT COUNT(*) as count FROM donations WHERE status = 'accepted'");
    const ngoRes = await pool.query('SELECT COUNT(*) as count FROM ngo_registrations');
    return {
      meals: Number(mealsRes.rows[0]?.count || 0),
      food_saved: Number(savedRes.rows[0]?.count || 0),
      volunteers: Number(ngoRes.rows[0]?.count || 0)
    };
  }
  return {
    meals: memoryDonations.length,
    food_saved: memoryDonations.filter(d => d.status === 'accepted').length,
    volunteers: memoryNgos.length
  };
}

async function getNotifications() {
  const { pool, dbType } = await getPool();
  if (dbType === 'mysql') {
    const [[row]] = await pool.query("SELECT COUNT(*) as count FROM donations WHERE status = 'accepted'");
    return { count: Number(row?.count || 0) };
  }
  if (dbType === 'postgres') {
    const res = await pool.query("SELECT COUNT(*) as count FROM donations WHERE status = 'accepted'");
    return { count: Number(res.rows[0]?.count || 0) };
  }
  return { count: memoryDonations.filter(d => d.status === 'accepted').length };
}

async function getAcceptedDonations() {
  const { pool, dbType } = await getPool();
  if (dbType === 'mysql') {
    const [rows] = await pool.query("SELECT * FROM donations WHERE status = 'accepted' ORDER BY created_at DESC LIMIT 10");
    return rows;
  }
  if (dbType === 'postgres') {
    const res = await pool.query("SELECT * FROM donations WHERE status = 'accepted' ORDER BY created_at DESC LIMIT 10");
    return res.rows;
  }
  return memoryDonations.filter(d => d.status === 'accepted');
}

async function getNGOs() {
  const { pool, dbType } = await getPool();
  if (dbType === 'mysql') {
    const [rows] = await pool.query("SELECT * FROM ngo_registrations ORDER BY registration_date DESC");
    return rows;
  }
  if (dbType === 'postgres') {
    const res = await pool.query("SELECT * FROM ngo_registrations ORDER BY registration_date DESC");
    return res.rows;
  }
  return memoryNgos;
}

async function getDonations() {
  const { pool, dbType } = await getPool();
  if (dbType === 'mysql') {
    const [rows] = await pool.query("SELECT * FROM donations ORDER BY created_at DESC");
    return rows;
  }
  if (dbType === 'postgres') {
    const res = await pool.query("SELECT * FROM donations ORDER BY created_at DESC");
    return res.rows;
  }
  return memoryDonations;
}

async function registerNGO(ngo) {
  const { pool, dbType } = await getPool();
  
  if (dbType === 'mysql') {
    const [existing] = await pool.query("SELECT id FROM ngo_registrations WHERE email = ?", [ngo.email]);
    if (existing.length > 0) {
      throw new Error("An NGO with this email is already registered");
    }
    const [result] = await pool.query(
      `INSERT INTO ngo_registrations (ngo_name, contact_person, phone_number, email, address, operating_hours, latitude, longitude) 
       VALUES (?, ?, ?, ?, ?, ?, ?, ?)`,
      [ngo.ngo_name, ngo.contact_person, ngo.phone_number, ngo.email, ngo.address, ngo.operating_hours, ngo.latitude || null, ngo.longitude || null]
    );
    return { id: result.insertId, ...ngo };
  }

  if (dbType === 'postgres') {
    const existing = await pool.query("SELECT id FROM ngo_registrations WHERE email = $1", [ngo.email]);
    if (existing.rows.length > 0) {
      throw new Error("An NGO with this email is already registered");
    }
    const res = await pool.query(
      `INSERT INTO ngo_registrations (ngo_name, contact_person, phone_number, email, address, operating_hours, latitude, longitude) 
       VALUES ($1, $2, $3, $4, $5, $6, $7, $8) RETURNING id`,
      [ngo.ngo_name, ngo.contact_person, ngo.phone_number, ngo.email, ngo.address, ngo.operating_hours, ngo.latitude || null, ngo.longitude || null]
    );
    return { id: res.rows[0].id, ...ngo };
  }

  // Memory fallback
  const exists = memoryNgos.some(n => n.email.toLowerCase() === ngo.email.toLowerCase());
  if (exists) {
    throw new Error("An NGO with this email is already registered");
  }
  const newNgo = {
    id: memoryNgos.length + 1,
    ...ngo,
    registration_date: new Date().toISOString()
  };
  memoryNgos.unshift(newNgo);
  return newNgo;
}

async function saveDonation(donation) {
  const { pool, dbType } = await getPool();

  if (dbType === 'mysql') {
    const [result] = await pool.query(
      `INSERT INTO donations (donor_name, food_type, quantity, location, donor_contact, pickup_time, latitude, longitude, status) 
       VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'accepted')`,
      [donation.donor_name, donation.food_type, donation.quantity, donation.location, donation.donor_contact, donation.pickup_time, donation.latitude || null, donation.longitude || null]
    );
    return { id: result.insertId, ...donation, status: 'accepted' };
  }

  if (dbType === 'postgres') {
    const res = await pool.query(
      `INSERT INTO donations (donor_name, food_type, quantity, location, donor_contact, pickup_time, latitude, longitude, status) 
       VALUES ($1, $2, $3, $4, $5, $6, $7, $8, 'accepted') RETURNING id`,
      [donation.donor_name, donation.food_type, donation.quantity, donation.location, donation.donor_contact, donation.pickup_time, donation.latitude || null, donation.longitude || null]
    );
    return { id: res.rows[0].id, ...donation, status: 'accepted' };
  }

  // Memory fallback
  const newDonation = {
    id: memoryDonations.length + 1,
    ...donation,
    status: 'accepted',
    created_at: new Date().toISOString()
  };
  memoryDonations.unshift(newDonation);
  return newDonation;
}

async function findMatchingNGOs(location) {
  const ngos = await getNGOs();
  if (!location) return ngos;
  const keywords = location.toLowerCase().split(',').map(s => s.trim()).filter(Boolean);
  if (keywords.length === 0) return ngos;

  return ngos.filter(ngo => {
    const addr = (ngo.address || '').toLowerCase();
    return keywords.some(k => addr.includes(k));
  });
}

module.exports = {
  getStats,
  getNotifications,
  getAcceptedDonations,
  getNGOs,
  getDonations,
  registerNGO,
  saveDonation,
  findMatchingNGOs
};
