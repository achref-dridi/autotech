# AutoTech Boutique System - Navigation Map & URL Reference

## 🗺️ Complete Navigation Structure

```
ROOT: /autotechfinal/

PUBLIC AREA (No Login Required)
├── view/public/index.php
│   ├── Navbar: Home | Voitures | Boutiques | Login | Signup
│   └── Links to:
│       ├── voitures.php (Voitures)
│       └── boutiques.php (Boutiques) ← NEW
│
├── view/public/voitures.php
│   ├── Navbar: Home | Voitures | Boutiques | Login | Signup
│   └── Links to:
│       ├── index.php (Home)
│       ├── boutiques.php (Boutiques) ← NEW
│       ├── login.php (Login)
│       └── signup.php (Signup)
│
└── view/public/boutiques.php ← NEW
    ├── List all active boutiques (grid)
    ├── Click boutique → view details + vehicles (boutiques.php?id=X)
    └── Back link → return to boutique list


AUTHENTICATED USER AREA (Login Required)
│
├── view/auth/login.php
│   └── Redirects to user area on success
│
├── view/auth/signup.php
│   └── Redirects to login on success
│
├── view/user/profil.php (User Profile)
│   ├── Navbar: Profile | Mes Véhicules | Mes Boutiques | Logout
│   └── Links to:
│       ├── mes-vehicules.php
│       ├── mes-boutiques.php (Mes Boutiques) ← UPDATED
│       └── logout.php
│
├── view/user/mes-vehicules.php (Personal Vehicles)
│   ├── Navbar: Profile | Mes Véhicules | Mes Boutiques | Logout
│   ├── List personal vehicles (vehicles with id_boutique = NULL)
│   └── Links to:
│       ├── profil.php
│       ├── mes-boutiques.php (Mes Boutiques) ← UPDATED
│       ├── ajouter-vehicule.php (Add personal vehicle)
│       └── logout.php
│
├── view/user/mes-boutiques.php ← NEW
│   ├── Header: "Mes Boutiques"
│   ├── Grid of user's boutiques
│   ├── Buttons per boutique:
│   │   ├── View (click card) → voitures-boutique.php?id=X
│   │   ├── Edit → modifier-boutique.php?id=X
│   │   └── Delete → Delete with confirmation
│   ├── "Ajouter boutique" button → ajouter-boutique.php
│   └── Links in navbar:
│       ├── profil.php (Profile)
│       ├── mes-vehicules.php (Mes Véhicules)
│       └── logout.php
│
├── view/user/ajouter-boutique.php ← NEW
│   ├── Form: Name, Address, Phone, Logo (optional)
│   ├── Validation: Name ≥3 chars, Address ≥5 chars, Phone ≥8 chars
│   ├── Submit button
│   ├── Error messages
│   └── On success → redirects to mes-boutiques.php
│
├── view/user/modifier-boutique.php ← NEW
│   ├── Form: Pre-filled with boutique data
│   ├── Fields: Name, Address, Phone, Logo (optional upload)
│   ├── Submit button
│   ├── Same validation as ajouter-boutique.php
│   └── On success → redirects to mes-boutiques.php
│
├── view/user/voitures-boutique.php ← NEW
│   ├── Header: Boutique name
│   ├── Breadcrumb: Boutiques > [Boutique Name] > Vehicles
│   ├── Boutique info: Logo, Name, Address, Phone, Owner
│   ├── "Ajouter véhicule à cette boutique" button → ajouter-vehicule-boutique.php?id=X
│   ├── Grid of boutique's vehicles
│   ├── Buttons per vehicle:
│   │   ├── Edit → edit-vehicule.php?id=X
│   │   └── Delete → Delete with confirmation
│   └── Back link → mes-boutiques.php
│
├── view/user/ajouter-vehicule-boutique.php ← NEW
│   ├── Breadcrumb: Boutiques > [Boutique Name] > Add Vehicle
│   ├── Form fields:
│   │   ├── Marque, Modèle, Année, Couleur
│   │   ├── Carburant, Transmission, Kilométrage, Prix Journalier
│   │   ├── Description
│   │   └── Image (file upload with preview)
│   ├── Submit button
│   ├── Cancel button → voitures-boutique.php?id=X
│   └── On success → redirects to voitures-boutique.php?id=X
│
└── view/auth/logout.php
    └── Clears session → redirects to index.php
```

## 📍 Direct URLs

### Public Pages (No Login)

| Page | URL | Purpose |
|------|-----|---------|
| Home | `/autotechfinal/view/public/index.php` | Main landing page |
| Vehicles | `/autotechfinal/view/public/voitures.php` | Browse all personal rentals |
| Boutiques | `/autotechfinal/view/public/boutiques.php` | Browse all boutiques |
| Boutique Details | `/autotechfinal/view/public/boutiques.php?id=1` | View specific boutique + vehicles |

### Authentication Pages

| Page | URL | Purpose |
|------|-----|---------|
| Login | `/autotechfinal/view/auth/login.php` | User login form |
| Signup | `/autotechfinal/view/auth/signup.php` | User registration |
| Logout | `/autotechfinal/view/auth/logout.php` | Logout action |

### User Pages (Login Required)

| Page | URL | Purpose |
|------|-----|---------|
| Profile | `/autotechfinal/view/user/profil.php` | User profile management |
| My Vehicles | `/autotechfinal/view/user/mes-vehicules.php` | Personal vehicle inventory |
| My Boutiques | `/autotechfinal/view/user/mes-boutiques.php` | User's boutiques list |
| Add Boutique | `/autotechfinal/view/user/ajouter-boutique.php` | Create new boutique |
| Edit Boutique | `/autotechfinal/view/user/modifier-boutique.php?id=1` | Edit boutique #1 |
| Boutique Vehicles | `/autotechfinal/view/user/voitures-boutique.php?id=1` | Vehicles in boutique #1 |
| Add Vehicle to Boutique | `/autotechfinal/view/user/ajouter-vehicule-boutique.php?id=1` | Add vehicle to boutique #1 |

## 🔄 Complete User Journeys

### Journey 1: Create a Boutique

```
HOME → Login (if not logged in)
     → Navigate to "Mes Boutiques" (navbar link)
     → Click "Ajouter boutique" button
     → Fill form (Name, Address, Phone, optional Logo)
     → Click "Ajouter"
     → Success message → Redirected to "Mes Boutiques"
     → New boutique appears in grid
     
URL Flow:
view/public/index.php 
  → view/auth/login.php 
    → view/user/mes-boutiques.php 
      → view/user/ajouter-boutique.php 
        → [Submit] → view/user/mes-boutiques.php
```

### Journey 2: Add Vehicle to Boutique

```
USER AREA → Click "Mes Boutiques" (navbar)
          → Click on a boutique card
          → Click "Ajouter véhicule à cette boutique"
          → Fill form (Marque, Modèle, ..., Image)
          → Click "Ajouter"
          → Success message → Redirected back
          → New vehicle appears in boutique's vehicle grid
          
URL Flow:
view/user/mes-boutiques.php
  → view/user/voitures-boutique.php?id=1
    → view/user/ajouter-vehicule-boutique.php?id=1
      → [Submit] → view/user/voitures-boutique.php?id=1
```

### Journey 3: Edit a Boutique

```
USER AREA → Click "Mes Boutiques"
          → Click edit icon on boutique card
          → Form appears with current data
          → Modify fields
          → Click "Modifier"
          → Success message → Redirected to "Mes Boutiques"
          → Updated boutique visible
          
URL Flow:
view/user/mes-boutiques.php
  → view/user/modifier-boutique.php?id=1
    → [Submit] → view/user/mes-boutiques.php
```

### Journey 4: Delete a Boutique

```
USER AREA → Click "Mes Boutiques"
          → Click delete icon on boutique
          → Confirmation dialog
          → Click "Confirm"
          → Success message
          → Boutique removed from list
          
URL Flow:
view/user/mes-boutiques.php
  → [Delete confirmed] → view/user/mes-boutiques.php
```

### Journey 5: Browse Boutiques (Public)

```
VISITOR → HOME (no login)
        → Click "Boutiques" (navbar)
        → See grid of all active boutiques
        → Click "Voir les véhicules" button
        → View boutique details + vehicle list
        → Click "Retour aux boutiques"
        → Back to boutique grid
        
URL Flow:
view/public/index.php
  → view/public/boutiques.php
    → [Click boutique] → view/public/boutiques.php?id=1
      → [Back link] → view/public/boutiques.php
```

## 🔀 Navbar Navigation Links

### Public Navbar (index.php, voitures.php)
```html
Home        → index.php
Voitures    → voitures.php
Boutiques   → boutiques.php           ← NEW LINK
Login       → auth/login.php
Signup      → auth/signup.php
```

### Authenticated User Navbar (profil.php, mes-vehicules.php)
```html
Profile         → profil.php
Mes Véhicules   → mes-vehicules.php
Mes Boutiques   → mes-boutiques.php   ← NEW LINK
Déconnexion     → auth/logout.php
```

## 📊 Query Parameters Used

### GET Parameters

| Parameter | Page | Purpose | Example |
|-----------|------|---------|---------|
| `id` | `boutiques.php` | Boutique ID to view details | `boutiques.php?id=5` |
| `id` | `modifier-boutique.php` | Boutique ID to edit | `modifier-boutique.php?id=5` |
| `id` | `voitures-boutique.php` | Boutique ID for vehicle list | `voitures-boutique.php?id=5` |
| `id` | `ajouter-vehicule-boutique.php` | Boutique ID for new vehicle | `ajouter-vehicule-boutique.php?id=5` |

### POST Parameters

| Parameter | Form | Purpose | Type |
|-----------|------|---------|------|
| `nom_boutique` | ajouter-boutique.php | Boutique name | text |
| `adresse` | ajouter-boutique.php | Boutique address | text |
| `telephone` | ajouter-boutique.php | Boutique phone | text |
| `logo` | ajouter-boutique.php | Boutique logo image | file |
| `marque` | ajouter-vehicule-boutique.php | Vehicle brand | text |
| `modele` | ajouter-vehicule-boutique.php | Vehicle model | text |
| ... | ajouter-vehicule-boutique.php | (all vehicle fields) | ... |

## 🎯 Key Navigation Points

### For New Users
1. **Discovery**: Public → Browse in "Boutiques" page
2. **Exploration**: Click boutique to see vehicles + owner info
3. **Decision**: No login needed to browse

### For Business Users
1. **Setup**: Login → "Mes Boutiques" → Create boutique
2. **Operations**: Manage boutique details and inventory
3. **Growth**: Add vehicles to attract customers

### For Returning Users
1. **Management**: Login → "Mes Boutiques" dashboard
2. **Quick Actions**: Edit boutique, add vehicles, manage inventory
3. **Navigation**: Easy access from any user page via navbar

## 🔒 Protected Routes

All pages requiring authentication include:

```php
require_once __DIR__ . '/../../controller/UtilisateurController.php';
$userController = new UtilisateurController();

if (!$userController->estConnecte()) {
    header('Location: ../auth/login.php');
    exit;
}
```

**Protected Pages:**
- ✅ mes-boutiques.php
- ✅ ajouter-boutique.php
- ✅ modifier-boutique.php
- ✅ voitures-boutique.php
- ✅ ajouter-vehicule-boutique.php
- ✅ profil.php
- ✅ mes-vehicules.php

**Public Pages:**
- ✅ index.php
- ✅ voitures.php
- ✅ boutiques.php (anyone can view)

## 📱 Mobile Navigation Behavior

All pages use Bootstrap navbar toggle:
- Desktop (768px+): Full navbar visible
- Mobile (<768px): Hamburger menu (collapsed navbar)
- All links remain functional on mobile

## 🎨 Navigation Styling

### Navbar CSS Classes
```css
.navbar              /* Container */
.nav-item            /* Menu item */
.nav-link            /* Menu link */
.active              /* Current page indicator */
.navbar-toggler      /* Mobile hamburger button */
.collapse            /* Responsive menu container */
```

### Active Indicator
- Current page nav-link has `.active` class
- Highlights in primary color (#2563eb)

## 🚀 Default Starting Points

### For First-Time Visitors
```
1. Land on: /autotechfinal/
2. See: Homepage with featured vehicles
3. Options: Browse vehicles or log in
```

### For Registered Users
```
1. Land on: /autotechfinal/view/user/profil.php (after login)
2. See: User profile dashboard
3. Options: View vehicles, manage boutiques, etc.
```

### For Business Users
```
1. First visit to: /autotechfinal/view/user/mes-boutiques.php
2. See: "Mes Boutiques" (empty initially)
3. Action: Click "Ajouter boutique" to set up business
```

---

**Navigation is complete and intuitive!** Users can easily:
- ✅ Discover boutiques as public visitors
- ✅ Create and manage boutiques as owners
- ✅ Organize personal and business vehicles
- ✅ Navigate between different areas using navbar links
