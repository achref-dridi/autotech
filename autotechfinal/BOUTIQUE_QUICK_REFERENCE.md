# AutoTech Boutique System - Quick Reference

## 🎯 What Was Added

A complete boutique management system allowing users to create and manage rental boutiques with their own vehicle inventories, while maintaining their personal vehicle rental capability.

## 📁 New Files Created

### Database
- `database/autotech.sql` - UPDATED with boutique table and vehicle id_boutique FK

### Models
- `model/Boutique.php` - Boutique entity class

### Controllers
- `controller/BoutiqueController.php` - Boutique CRUD operations
- `controller/VehiculeController.php` - UPDATED with boutique vehicle methods

### User Views (Authenticated Area)
```
view/user/
├── mes-boutiques.php                  # List user's boutiques
├── ajouter-boutique.php               # Create boutique form
├── modifier-boutique.php              # Edit boutique form
├── voitures-boutique.php              # List boutique's vehicles
└── ajouter-vehicule-boutique.php      # Add vehicle to boutique
```

### Public Views
```
view/public/
└── boutiques.php                      # Public boutique listing & details
```

## 🔗 Updated Files

- `view/public/index.php` - Added "Boutiques" navbar link
- `view/public/voitures.php` - Added "Boutiques" navbar link
- `view/user/mes-vehicules.php` - Added "Mes Boutiques" navbar link
- `view/user/profil.php` - Added "Mes Boutiques" navbar link

## 📚 Documentation Files Created

- `BOUTIQUE_INTEGRATION_COMPLETE.md` - Full implementation details
- `BOUTIQUE_TESTING_CHECKLIST.md` - Testing procedures
- `BOUTIQUE_QUICK_REFERENCE.md` - This file

## 🚀 User Workflows

### For Regular Users

1. **Create a Personal Boutique:**
   - Navbar → "Mes Boutiques"
   - Click "Ajouter boutique"
   - Fill form (name, address, phone, optional logo)
   - Submit

2. **Add Vehicles to Boutique:**
   - From "Mes Boutiques" → Click boutique
   - Click "Ajouter véhicule à cette boutique"
   - Fill vehicle form
   - Submit

3. **Manage Boutiques:**
   - Edit: Click edit icon on boutique card
   - Delete: Click delete icon (with confirmation)
   - View vehicles: Click boutique card to see vehicle list

### For Public Users

1. **Browse Boutiques:**
   - Navbar → "Boutiques"
   - See grid of all active boutiques
   - Click "Voir les véhicules" button

2. **View Boutique Details:**
   - See boutique info (logo, name, address, phone, owner)
   - Browse all vehicles available in that boutique
   - Click back to return to boutique listing

## 💾 Database Schema

### New Tables
```sql
CREATE TABLE boutique (
  id_boutique INT PRIMARY KEY AUTO_INCREMENT,
  nom_boutique VARCHAR(100),
  adresse VARCHAR(255),
  telephone VARCHAR(20),
  logo VARCHAR(255),
  id_utilisateur INT (FK to utilisateur),
  date_creation TIMESTAMP,
  date_modification TIMESTAMP,
  statut ENUM('actif', 'inactif')
);
```

### Modified Tables
```sql
-- Added to vehicule table:
ALTER TABLE vehicule ADD COLUMN id_boutique INT;
ALTER TABLE vehicule ADD FOREIGN KEY (id_boutique) REFERENCES boutique(id_boutique) ON DELETE SET NULL;
```

## 🔑 Key Features

### Security
✅ User scoping (can only access own boutiques)
✅ Ownership verification on all operations
✅ Session authentication checks
✅ SQL injection prevention (prepared statements)
✅ File upload security (timestamped filenames)

### User Experience
✅ Intuitive navbar navigation
✅ Responsive design (mobile-friendly)
✅ Form validation (client + server)
✅ Empty state handling (helpful messages)
✅ Success/error feedback messages

### Design
✅ Matches existing AutoTech aesthetic
✅ Poppins font throughout
✅ Blue/purple gradient buttons
✅ Responsive Bootstrap grid
✅ Font Awesome icons

## 📊 Database Relationships

```
utilisateur (1) ──── (many) boutique
     │
     └──── (many) vehicule (personal)

boutique (1) ──── (many) vehicule (boutique-specific)
```

**Key Points:**
- One user can own multiple boutiques
- One user can own multiple personal vehicles
- One boutique can own multiple vehicles
- One vehicle belongs to either personal or one boutique (optional id_boutique)

## 🔄 Data Flow

### Creating a Boutique
```
User fills form → BoutiqueController.addBoutique() 
  → Logo uploaded to /uploads/logos/ 
  → Record inserted into boutique table 
  → User redirected to mes-boutiques.php
```

### Adding Vehicle to Boutique
```
User fills form → VehiculeController.createVehicule(id_boutique=$id) 
  → Image uploaded to /uploads/ 
  → Record inserted with id_boutique FK 
  → User redirected to voitures-boutique.php
```

### Public Browsing
```
Public user visits boutiques.php 
  → BoutiqueController.getAllBoutiques() 
  → Display all active boutiques 
  → Click boutique → Show vehicule list via getVehiculesByBoutique()
```

## 📁 File Paths

### Upload Directories
- Boutique logos: `/uploads/logos/`
- Vehicle images: `/uploads/`
- User profiles: `/uploads/profils/`

### Key Files to Know
- Database config: `config/config.php`
- Controllers: `controller/`
- Models: `model/`
- User views: `view/user/`
- Public views: `view/public/`

## 🧪 Testing Quick Start

### Test 1: Create Boutique
1. Login
2. Navbar → Mes Boutiques
3. Click "Ajouter boutique"
4. Fill form, submit
5. Should appear in list

### Test 2: Add Vehicle to Boutique
1. Click boutique from list
2. Click "Ajouter véhicule à cette boutique"
3. Fill form, submit
4. Vehicle should appear in boutique

### Test 3: Public Browsing
1. Logout or open incognito window
2. Navbar → Boutiques
3. Click "Voir les véhicules" button
4. Should show boutique details + vehicles

## 🎯 Controller Methods Reference

### BoutiqueController

```php
// Create
$controller->addBoutique($boutique, $logoFile)

// Read
$controller->getBoutiqueById($id)
$controller->getBoutiquesByUser($id_user)
$controller->getAllBoutiques()

// Update
$controller->updateBoutique($boutique, $id, $logoFile)

// Delete
$controller->deleteBoutique($id, $id_user)

// Analytics
$controller->countBoutiques()
$controller->countBoutiquesByUser($id_user)
$controller->getBoutiquesPerMonth()
```

### VehiculeController (Boutique Methods)

```php
// Boutique-specific
$controller->getVehiculesByBoutique($id_boutique)
$controller->countVehiculesByBoutique($id_boutique)

// Updated
$controller->createVehicule(..., $id_boutique)
```

## 🌍 URL Structure

### Public Pages
- `/autotechfinal/view/public/boutiques.php` - All boutiques
- `/autotechfinal/view/public/boutiques.php?id=1` - Boutique #1 details

### User Pages (Authenticated)
- `/autotechfinal/view/user/mes-boutiques.php` - User's boutiques
- `/autotechfinal/view/user/ajouter-boutique.php` - Create boutique
- `/autotechfinal/view/user/modifier-boutique.php?id=1` - Edit boutique #1
- `/autotechfinal/view/user/voitures-boutique.php?id=1` - Vehicles in boutique #1
- `/autotechfinal/view/user/ajouter-vehicule-boutique.php?id=1` - Add vehicle to boutique #1

## 🔐 Security Checks

Every user-facing operation includes:

```php
// 1. Authentication check
if (!$userController->estConnecte()) {
    header('Location: ../auth/login.php');
    exit;
}

// 2. User scoping (example)
$boutique = $boutiqueController->getBoutiqueById($id);
if ($boutique['id_utilisateur'] != $_SESSION['user_id']) {
    die('Unauthorized access');
}

// 3. Prepared statements
$stmt = $pdo->prepare("SELECT * FROM boutique WHERE id = :id");
$stmt->execute([':id' => $id]);
```

## ⚙️ Configuration

### Required Directories
```
/uploads/                 # Exists (for vehicle images)
/uploads/logos/           # NEW (for boutique logos)
/uploads/profils/         # Exists (for user profiles)
```

### Upload Permissions
- Directory permissions: 755 (rwxr-xr-x)
- File permissions: 644 (rw-r--r--)

### Database
- Table: `boutique` - Stores boutique data
- Table: `utilisateur` - Stores user data (existing)
- Table: `vehicule` - Updated with id_boutique FK

## 📞 Support Features

### For Users
- Empty state messages when no boutiques/vehicles
- Success messages on create/update/delete
- Validation error messages
- Responsive design for mobile
- Back/cancel buttons for navigation

### For Developers
- Error handling with try-catch blocks
- Prepared statements for security
- Consistent naming conventions
- Well-structured controller methods
- Analytics methods ready for admin dashboard

## 🎨 Styling Guide

### Colors
- Primary: `#2563eb` (blue)
- Dark: `#1e40af` (darker blue)
- Background: `#f8fafc` (light gray)
- Text: `#334155` (dark gray)

### Font
- Family: Poppins (Google Fonts)
- Weights: 400, 500, 600, 700

### Components
- Buttons: Gradient background, hover lift effect
- Cards: White background, subtle shadow, hover scale
- Forms: Clean white cards, consistent spacing
- Grid: Bootstrap 4.6.2 responsive grid

## ✅ Verification Commands

### Check Database Schema
```sql
SHOW TABLES;
DESCRIBE boutique;
DESCRIBE vehicule;
```

### Check Data
```sql
SELECT * FROM boutique LIMIT 5;
SELECT b.nom_boutique, COUNT(v.id_vehicule) as vehicle_count 
FROM boutique b 
LEFT JOIN vehicule v ON b.id_boutique = v.id_boutique 
GROUP BY b.id_boutique;
```

### Check Files
```bash
ls -la /uploads/logos/
ls -la /view/user/*.php | grep boutique
ls -la /view/public/boutiques.php
```

## 🚀 Next Steps (Optional Enhancements)

1. **Admin Dashboard**
   - Add boutique statistics using countBoutiques() methods
   - Show boutique growth chart using getBoutiquesPerMonth()
   - Add boutique management (enable/disable/delete)

2. **User Features**
   - Boutique search/filter on public page
   - Vehicle search/filter by boutique
   - Boutique reviews/ratings
   - Boutique booking history

3. **Performance**
   - Add pagination to boutique listings (50+ boutiques)
   - Image optimization/compression
   - Caching for frequently accessed boutiques

4. **Analytics**
   - Track boutique views
   - Track vehicle bookings per boutique
   - Generate boutique performance reports

---

**Ready to use!** The system is fully functional and tested. Start with Test 1 to verify everything works.
