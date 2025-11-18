# 🍽️ Food Saver - Complete Donation System

## 📋 System Overview
Complete food donation system that connects donors with registered NGOs through automatic notifications.

## 🗂️ Files Included

### Core Pages
- `index.html` - Homepage
- `donate.html` - Enhanced donation form with validation
- `registerngo.html` - NGO registration form
- `about.html` - About page
- `contact.html` - Contact page
- `how.html` - How it works page
- **Admin Panel**: View all registered NGOs in a clean table format
- **Responsive Design**: Works on desktop, tablet, and mobile devices

## Setup Instructions

### Prerequisites

1. **XAMPP** installed on your system
2. Web browser (Chrome, Firefox, Safari, Edge)

### Installation Steps

1. **Start XAMPP Services**
   - Open XAMPP Control Panel
   - Start Apache and MySQL services
   - Make sure both services show green status

2. **Place Files in htdocs**
   - Copy all project files to your XAMPP htdocs folder
   - Example: `C:\xampp\htdocs\food-saver\`

3. **Setup Database**
   - Open your web browser
   - Navigate to: `http://localhost/food-saver/setup_database.php`
   - This will create the database and table automatically
   - You should see success messages

4. **Test the System**
   - Navigate to: `http://localhost/food-saver/registerngo.html`
   - Fill out the registration form
   - Submit and verify data is stored

5. **View Registered NGOs**
   - Navigate to: `http://localhost/food-saver/view_ngos.php`
   - View all registered NGOs in table format

## File Structure

```
food-saver/
├── registerngo.html          # NGO registration form
├── register_ngo.php          # Backend processing script
├── view_ngos.php            # Admin panel to view NGOs
├── setup_database.php       # Database setup script
├── save_contact.php         # Contact form handler (existing)
├── index.html               # Main homepage
├── about.html               # About page
├── contact.html             # Contact page
├── how.html                 # How it works page
├── img*.jpg                 # Image assets
└── README.md               # This file
```

## Database Schema

### Table: ngo_registrations

| Column | Type | Description |
|--------|------|-------------|
| id | INT(11) | Primary key, auto-increment |
| ngo_name | VARCHAR(255) | Name of the NGO |
| contact_person | VARCHAR(255) | Contact person's name |
| phone_number | VARCHAR(20) | Phone number |
| email | VARCHAR(255) | Email address (unique) |
| address | TEXT | Full address |
| operating_hours | VARCHAR(100) | Operating hours |
| registration_date | TIMESTAMP | Registration timestamp |

## Features Details

### Form Validation
- All fields are required
- Email format validation
- Phone number format validation (minimum 10 digits)
- Duplicate email prevention

### Security Features
- SQL injection prevention using prepared statements
- Input sanitization
- XSS prevention with htmlspecialchars()

### User Experience
- Loading states during form submission
- Success/error messages
- Form auto-reset after successful submission
- Responsive design for all devices

## Troubleshooting

### Common Issues

1. **Database Connection Error**
   - Ensure MySQL service is running in XAMPP
   - Check if database credentials are correct

2. **Form Not Submitting**
   - Ensure Apache service is running
   - Check file permissions
   - Verify PHP is enabled in XAMPP

3. **Page Not Loading**
   - Check if files are in correct htdocs folder
   - Verify Apache service is running
   - Check browser console for errors

### Database Credentials

- **Server**: localhost
- **Username**: root
- **Password**: (empty)
- **Database**: food_saver

## Support

If you encounter any issues:
1. Check XAMPP error logs
2. Verify all services are running
3. Ensure proper file permissions
4. Check browser console for JavaScript errors

## License

This project is created for educational purposes.
