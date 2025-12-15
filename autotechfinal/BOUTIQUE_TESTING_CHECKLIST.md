# Boutique Integration - Testing Checklist

## ✅ Pre-Testing Verification

- ✅ Database schema updated (boutique table created, vehicule.id_boutique added)
- ✅ Models created (Boutique.php)
- ✅ Controllers updated (BoutiqueController.php, VehiculeController.php)
- ✅ User management views created (5 files)
- ✅ Public browsing view created (boutiques.php)
- ✅ Navigation links added (4 files)
- ✅ Upload directory created (/uploads/logos/)
- ✅ All file paths are absolute and properly configured

## 🧪 Testing Workflows

### Test 1: Create a New Boutique

**User Action:**
1. Login to account
2. Click "Mes Boutiques" in navbar → redirects to `view/user/mes-boutiques.php`
3. Click "Ajouter boutique" button
4. Fill form:
   - Nom Boutique: "Test Boutique"
   - Adresse: "123 Rue de Test, Paris"
   - Téléphone: "01234567890"
   - Logo: Upload a sample image
5. Click "Ajouter" button

**Expected Results:**
- ✅ Form validates input (client-side error messages if invalid)
- ✅ Logo uploaded to `/uploads/logos/` with timestamp prefix
- ✅ Boutique record created in database with id_utilisateur
- ✅ Redirected to `mes-boutiques.php` with success message
- ✅ New boutique appears in grid
- ✅ Logo displays in boutique card (or Font Awesome placeholder if no logo)

**Database Check:**
```sql
SELECT * FROM boutique WHERE nom_boutique = 'Test Boutique';
```

---

### Test 2: Add Vehicle to Boutique

**User Action:**
1. From `mes-boutiques.php`, click on a boutique card
2. Should redirect to `voitures-boutique.php?id=X`
3. View boutique details (logo, name, address, phone)
4. Click "Ajouter véhicule à cette boutique" button
5. Fill vehicle form:
   - Marque: "Toyota"
   - Modèle: "Corolla"
   - Année: "2022"
   - Carburant: "Essence"
   - Transmission: "Automatique"
   - Kilométrage: "15000"
   - Couleur: "Noir"
   - Prix Journalier: "50"
   - Description: "Excellent state"
   - Image: Upload vehicle image
6. Click "Ajouter" button

**Expected Results:**
- ✅ Form validates input
- ✅ Image uploaded to `/uploads/vehicule_` directory
- ✅ Vehicle record created with id_boutique FK
- ✅ Redirected to `voitures-boutique.php` with success message
- ✅ New vehicle appears in boutique's vehicle grid

**Database Check:**
```sql
SELECT * FROM vehicule WHERE id_boutique = X;
```

---

### Test 3: Edit Boutique

**User Action:**
1. From `mes-boutiques.php`, click edit icon on boutique card
2. Should redirect to `modifier-boutique.php?id=X`
3. Modify fields (e.g., change phone number)
4. Optionally change logo (upload new or keep existing)
5. Click "Modifier" button

**Expected Results:**
- ✅ Form pre-populated with current boutique data
- ✅ Changes saved to database
- ✅ Redirected to `mes-boutiques.php` with success message
- ✅ Updated information visible in boutique card

---

### Test 4: Delete Vehicle from Boutique

**User Action:**
1. From `voitures-boutique.php`, click delete icon on vehicle
2. Should show confirmation dialog
3. Confirm deletion

**Expected Results:**
- ✅ Vehicle record deleted from database (id_boutique set to NULL, not deleted)
- ✅ Vehicle disappears from boutique inventory
- ✅ Success message displayed
- ✅ Page refreshes to show updated vehicle count

---

### Test 5: Delete Boutique

**User Action:**
1. From `mes-boutiques.php`, click delete icon on boutique card
2. Should show confirmation dialog
3. Confirm deletion

**Expected Results:**
- ✅ Boutique record deleted from database
- ✅ All vehicles in boutique have id_boutique set to NULL (ON DELETE SET NULL)
- ✅ Boutique disappears from grid
- ✅ Success message displayed

**Database Check:**
```sql
SELECT COUNT(*) FROM vehicule WHERE id_boutique = X; -- Should return 0
```

---

### Test 6: Public Boutique Browsing

**User Action (Public - No Login Required):**
1. Navigate to `/autotechfinal/view/public/boutiques.php`
2. See grid of all active boutiques
3. Click "Voir les véhicules" button on a boutique
4. Should show `boutiques.php?id=X`
5. View boutique details and vehicle inventory
6. Click "Retour aux boutiques" link
7. Return to main boutique listing

**Expected Results:**
- ✅ Boutique grid displays all boutiques with logos
- ✅ Boutique names, addresses, phone numbers visible
- ✅ Owner/proprietaire name displayed
- ✅ Clicking boutique shows detailed view
- ✅ Vehicle grid shows all vehicles in boutique
- ✅ Back link returns to listing
- ✅ No login required for public access

---

### Test 7: User Scoping Verification

**User Action:**
1. Login as User A
2. Click "Mes Boutiques"
3. Create Boutique A1
4. Logout
5. Login as User B
6. Click "Mes Boutiques"
7. Create Boutique B1
8. Try to access `modifier-boutique.php?id=A1` directly (User A's boutique)

**Expected Results:**
- ✅ User A only sees Boutique A1 in their `mes-boutiques.php`
- ✅ User B only sees Boutique B1 in their `mes-boutiques.php`
- ✅ User B accessing User A's boutique ID shows error or redirects
- ✅ No cross-user access possible

**Security Check:**
```php
// In modifier-boutique.php, verify line checks:
if ($boutique['id_utilisateur'] != $_SESSION['user_id']) {
    // Show error or redirect
}
```

---

### Test 8: Navigation Links

**User Action:**
1. Browse public pages:
   - Home page (index.php) - click "Boutiques" link → goes to `boutiques.php`
   - Vehicles page (voitures.php) - click "Boutiques" link → goes to `boutiques.php`
2. Login and browse user pages:
   - Profile page (profil.php) - click "Mes Boutiques" link → goes to `mes-boutiques.php`
   - Vehicles page (mes-vehicules.php) - click "Mes Boutiques" link → goes to `mes-boutiques.php`

**Expected Results:**
- ✅ All navbar links present and functional
- ✅ Navigation leads to correct pages
- ✅ No broken links (404 errors)

---

### Test 9: Responsive Design

**User Action:**
1. Open `boutiques.php` on:
   - Desktop (1200px+)
   - Tablet (768px)
   - Mobile (375px)
2. Open `voitures-boutique.php` on same devices
3. Open `ajouter-boutique.php` on same devices

**Expected Results:**
- ✅ Desktop: Grid displays 3+ boutiques per row
- ✅ Tablet: Grid displays 2 boutiques per row
- ✅ Mobile: Grid displays 1 boutique per row
- ✅ Form inputs remain readable on mobile
- ✅ Buttons clickable on mobile (appropriate size)
- ✅ Images scale properly

---

### Test 10: Logo Upload & Display

**User Action:**
1. Upload various image types:
   - PNG (transparent background)
   - JPG (photo)
   - GIF (animated)
2. Check file size handling

**Expected Results:**
- ✅ Logo appears in boutique card after upload
- ✅ Logo appears in boutique details page
- ✅ Logo appears in public boutique listing
- ✅ Files stored in `/uploads/logos/` with timestamp prefix
- ✅ File system doesn't overwrite existing files (timestamp prevents collision)

**File System Check:**
```
/uploads/logos/
├── 1234567890_logo1.png
├── 1234567891_logo2.jpg
└── 1234567892_logo3.gif
```

---

## 🔍 Error Handling Tests

### Test 11: Form Validation Errors

**User Action:**
1. Try to create boutique with:
   - Empty name
   - Name with 1-2 characters (should require ≥3)
   - Empty address
   - Address with 1-4 characters (should require ≥5)
   - Empty phone
   - Phone with <8 characters

**Expected Results:**
- ✅ Client-side validation shows error messages
- ✅ Server-side validation prevents invalid data in database
- ✅ User stays on form page with error messages
- ✅ Form data is retained (except file inputs)

---

### Test 12: Database Connection Error

**Scenario:** Simulate database downtime

**Expected Results:**
- ✅ Error message displayed (user-friendly, no technical details)
- ✅ No white-screen-of-death errors
- ✅ User redirected or shown helpful message

---

### Test 13: Missing Required Fields

**User Action:**
Submit form with missing required fields:
- No logo (should be optional) ✅
- No boutique name (should be required)
- No address (should be required)

**Expected Results:**
- ✅ Logo optional - boutique created without logo (Font Awesome icon shown as placeholder)
- ✅ Name/Address required - form validation prevents submission

---

## 📊 Performance Tests

### Test 14: Load Listing Pages with Multiple Boutiques

**Setup:** Create 50+ boutiques in database

**User Action:**
1. Load `boutiques.php`
2. Load `mes-boutiques.php` for user with 20+ boutiques
3. Check page load time and responsiveness

**Expected Results:**
- ✅ Page loads in <2 seconds
- ✅ Grid renders smoothly
- ✅ No UI freezing when scrolling
- ✅ Images lazy-load or display quickly

---

## 📝 SQL Verification Tests

### Test 15: Database Integrity

**Checks to run:**

```sql
-- 1. Verify boutique table exists
DESCRIBE boutique;

-- 2. Verify all boutiques created by logged-in users
SELECT COUNT(*) FROM boutique WHERE statut = 'actif';

-- 3. Verify unique constraint on boutique(nom_boutique, id_utilisateur)
-- Try to create duplicate:
INSERT INTO boutique (nom_boutique, adresse, telephone, id_utilisateur) 
VALUES ('Test Boutique', 'Test Address', '123', 1);
-- Should fail on second insert with same user_id

-- 4. Verify vehicle-boutique relationships
SELECT v.id_vehicule, v.marque, b.nom_boutique 
FROM vehicule v 
LEFT JOIN boutique b ON v.id_boutique = b.id_boutique
WHERE v.id_boutique IS NOT NULL;

-- 5. Verify ON DELETE SET NULL works
DELETE FROM boutique WHERE id_boutique = X;
SELECT COUNT(*) FROM vehicule WHERE id_boutique = X; -- Should return 0
SELECT COUNT(*) FROM vehicule WHERE id_vehicule IN (...); -- Vehicles should exist

-- 6. Verify proprietaire names display correctly
SELECT b.nom_boutique, u.nom, u.prenom 
FROM boutique b 
JOIN utilisateur u ON b.id_utilisateur = u.id_utilisateur;
```

---

## ✨ Visual/UX Tests

### Test 16: Design Consistency

**Checks:**
- ✅ Font: All text uses Poppins font (check CSS)
- ✅ Colors: Blue (#2563eb) and purple gradients match throughout
- ✅ Buttons: Gradient styling consistent across all pages
- ✅ Cards: Same shadow, border-radius, hover effects
- ✅ Icons: Font Awesome icons consistent and properly colored
- ✅ Spacing: Padding/margins consistent
- ✅ Placeholders: Font Awesome icons used when no image/logo

---

## 📋 Completion Checklist

Use this checklist to verify all tests pass:

- [ ] Test 1: Create Boutique - PASS
- [ ] Test 2: Add Vehicle to Boutique - PASS
- [ ] Test 3: Edit Boutique - PASS
- [ ] Test 4: Delete Vehicle from Boutique - PASS
- [ ] Test 5: Delete Boutique - PASS
- [ ] Test 6: Public Boutique Browsing - PASS
- [ ] Test 7: User Scoping Verification - PASS
- [ ] Test 8: Navigation Links - PASS
- [ ] Test 9: Responsive Design - PASS
- [ ] Test 10: Logo Upload & Display - PASS
- [ ] Test 11: Form Validation Errors - PASS
- [ ] Test 12: Database Connection Error - PASS
- [ ] Test 13: Missing Required Fields - PASS
- [ ] Test 14: Load Listing Pages - PASS
- [ ] Test 15: Database Integrity - PASS
- [ ] Test 16: Design Consistency - PASS

**All Tests Status:** [ ] PASS | [ ] PARTIAL | [ ] NEEDS WORK

---

## 🚀 Go-Live Checklist

Before deploying to production:

- [ ] All 16 tests completed and passing
- [ ] Database backups created
- [ ] Logo upload directory permissions set (755)
- [ ] Error logging configured
- [ ] Admin dashboard updated with boutique stats (optional)
- [ ] User documentation created
- [ ] Email notifications configured (optional - for boutique creation)
- [ ] Performance monitoring configured
- [ ] Security audit completed (SQL injection, XSS prevention)

---

**Testing Status**: Ready to execute
**Last Updated**: Today
**Next Step**: Run Test 1 through Test 16 in order
