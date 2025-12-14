/**
 * Validation JavaScript pour AutoTech
 * Toutes les validations de formulaires
 */

// Fonction utilitaire pour afficher les erreurs
function afficherErreur(elementId, message) {
    const element = document.getElementById(elementId);
    if (element) {
        let errorDiv = element.nextElementSibling;
        if (!errorDiv || !errorDiv.classList.contains('error-message')) {
            errorDiv = document.createElement('div');
            errorDiv.className = 'error-message text-danger small mt-1';
            element.parentNode.insertBefore(errorDiv, element.nextSibling);
        }
        errorDiv.textContent = message;
        element.classList.add('is-invalid');
    }
}

// Fonction pour effacer les erreurs
function effacerErreur(elementId) {
    const element = document.getElementById(elementId);
    if (element) {
        const errorDiv = element.nextElementSibling;
        if (errorDiv && errorDiv.classList.contains('error-message')) {
            errorDiv.remove();
        }
        element.classList.remove('is-invalid');
        element.classList.add('is-valid');
    }
}

// Fonction pour effacer toutes les erreurs
function effacerToutesErreurs() {
    document.querySelectorAll('.error-message').forEach(el => el.remove());
    document.querySelectorAll('.is-invalid').forEach(el => {
        el.classList.remove('is-invalid');
    });
}

// Validation du formulaire d'inscription
function validerInscription() {
    effacerToutesErreurs();
    let isValid = true;
    
    // Validation du nom
    const nom = document.getElementById('nom').value.trim();
    if (nom === '') {
        afficherErreur('nom', 'Le nom est obligatoire.');
        isValid = false;
    } else if (nom.length < 2) {
        afficherErreur('nom', 'Le nom doit contenir au moins 2 caractères.');
        isValid = false;
    } else if (!/^[a-zA-ZÀ-ÿ\s-]+$/.test(nom)) {
        afficherErreur('nom', 'Le nom ne peut contenir que des lettres.');
        isValid = false;
    } else {
        effacerErreur('nom');
    }
    
    // Validation du prénom
    const prenom = document.getElementById('prenom').value.trim();
    if (prenom === '') {
        afficherErreur('prenom', 'Le prénom est obligatoire.');
        isValid = false;
    } else if (prenom.length < 2) {
        afficherErreur('prenom', 'Le prénom doit contenir au moins 2 caractères.');
        isValid = false;
    } else if (!/^[a-zA-ZÀ-ÿ\s-]+$/.test(prenom)) {
        afficherErreur('prenom', 'Le prénom ne peut contenir que des lettres.');
        isValid = false;
    } else {
        effacerErreur('prenom');
    }
    
    // Validation de l'email
    const email = document.getElementById('email').value.trim();
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (email === '') {
        afficherErreur('email', 'L\'email est obligatoire.');
        isValid = false;
    } else if (!emailRegex.test(email)) {
        afficherErreur('email', 'Veuillez entrer une adresse email valide.');
        isValid = false;
    } else {
        effacerErreur('email');
    }
    
    // Validation du téléphone (optionnel mais si rempli doit être valide)
    const telephone = document.getElementById('telephone');
    if (telephone && telephone.value.trim() !== '') {
        const telValue = telephone.value.trim();
        // Format tunisien: +216 XX XXX XXX ou 00216 XX XXX XXX ou XX XXX XXX
        if (!/^(\+216|00216)?[2-9]\d{7}$/.test(telValue.replace(/\s/g, ''))) {
            afficherErreur('telephone', 'Numéro de téléphone invalide (format: +216 XX XXX XXX).');
            isValid = false;
        } else {
            effacerErreur('telephone');
        }
    }
    
    // Validation du mot de passe
    const motDePasse = document.getElementById('mot_de_passe').value;
    if (motDePasse === '') {
        afficherErreur('mot_de_passe', 'Le mot de passe est obligatoire.');
        isValid = false;
    } else if (motDePasse.length < 6) {
        afficherErreur('mot_de_passe', 'Le mot de passe doit contenir au moins 6 caractères.');
        isValid = false;
    } else if (!/(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/.test(motDePasse)) {
        afficherErreur('mot_de_passe', 'Le mot de passe doit contenir au moins une majuscule, une minuscule et un chiffre.');
        isValid = false;
    } else {
        effacerErreur('mot_de_passe');
    }
    
    // Validation de la confirmation du mot de passe
    const confirmerMotDePasse = document.getElementById('confirmer_mot_de_passe');
    if (confirmerMotDePasse) {
        if (confirmerMotDePasse.value === '') {
            afficherErreur('confirmer_mot_de_passe', 'Veuillez confirmer le mot de passe.');
            isValid = false;
        } else if (confirmerMotDePasse.value !== motDePasse) {
            afficherErreur('confirmer_mot_de_passe', 'Les mots de passe ne correspondent pas.');
            isValid = false;
        } else {
            effacerErreur('confirmer_mot_de_passe');
        }
    }
    
    return isValid;
}

// Validation du formulaire de connexion
function validerConnexion() {
    effacerToutesErreurs();
    let isValid = true;
    
    // Validation de l'email
    const email = document.getElementById('email').value.trim();
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (email === '') {
        afficherErreur('email', 'L\'email est obligatoire.');
        isValid = false;
    } else if (!emailRegex.test(email)) {
        afficherErreur('email', 'Veuillez entrer une adresse email valide.');
        isValid = false;
    } else {
        effacerErreur('email');
    }
    
    // Validation du mot de passe
    const motDePasse = document.getElementById('mot_de_passe').value;
    if (motDePasse === '') {
        afficherErreur('mot_de_passe', 'Le mot de passe est obligatoire.');
        isValid = false;
    } else {
        effacerErreur('mot_de_passe');
    }
    
    return isValid;
}

// Validation du formulaire d'ajout de véhicule
function validerVehicule() {
    effacerToutesErreurs();
    let isValid = true;
    
    // Validation de la marque
    const marque = document.getElementById('marque').value.trim();
    if (marque === '') {
        afficherErreur('marque', 'La marque est obligatoire.');
        isValid = false;
    } else if (marque.length < 2) {
        afficherErreur('marque', 'La marque doit contenir au moins 2 caractères.');
        isValid = false;
    } else {
        effacerErreur('marque');
    }
    
    // Validation du modèle
    const modele = document.getElementById('modele').value.trim();
    if (modele === '') {
        afficherErreur('modele', 'Le modèle est obligatoire.');
        isValid = false;
    } else if (modele.length < 2) {
        afficherErreur('modele', 'Le modèle doit contenir au moins 2 caractères.');
        isValid = false;
    } else {
        effacerErreur('modele');
    }
    
    // Validation de l'année
    const annee = document.getElementById('annee').value;
    const anneeActuelle = new Date().getFullYear();
    if (annee === '') {
        afficherErreur('annee', 'L\'année est obligatoire.');
        isValid = false;
    } else if (isNaN(annee) || parseInt(annee) < 1950 || parseInt(annee) > anneeActuelle + 1) {
        afficherErreur('annee', `L'année doit être entre 1950 et ${anneeActuelle + 1}.`);
        isValid = false;
    } else {
        effacerErreur('annee');
    }
    
    // Validation du carburant
    const carburant = document.getElementById('carburant').value;
    if (carburant === '') {
        afficherErreur('carburant', 'Le type de carburant est obligatoire.');
        isValid = false;
    } else {
        effacerErreur('carburant');
    }
    
    // Validation du kilométrage
    const kilometrage = document.getElementById('kilometrage').value;
    if (kilometrage === '') {
        afficherErreur('kilometrage', 'Le kilométrage est obligatoire.');
        isValid = false;
    } else if (isNaN(kilometrage) || parseInt(kilometrage) < 0) {
        afficherErreur('kilometrage', 'Le kilométrage doit être un nombre positif.');
        isValid = false;
    } else if (parseInt(kilometrage) > 1000000) {
        afficherErreur('kilometrage', 'Le kilométrage semble trop élevé.');
        isValid = false;
    } else {
        effacerErreur('kilometrage');
    }
    
    // Validation du prix journalier
    const prixJournalier = document.getElementById('prix_journalier');
    if (prixJournalier && prixJournalier.value !== '') {
        const prix = parseFloat(prixJournalier.value);
        if (isNaN(prix) || prix < 0) {
            afficherErreur('prix_journalier', 'Le prix doit être un nombre positif.');
            isValid = false;
        } else if (prix > 10000) {
            afficherErreur('prix_journalier', 'Le prix semble trop élevé.');
            isValid = false;
        } else {
            effacerErreur('prix_journalier');
        }
    }
    
    // Validation de l'image (optionnelle)
    const imageInput = document.getElementById('image_principale');
    if (imageInput && imageInput.files.length > 0) {
        const file = imageInput.files[0];
        const maxSize = 5 * 1024 * 1024; // 5MB
        const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
        
        if (!allowedTypes.includes(file.type)) {
            afficherErreur('image_principale', 'Format de fichier non supporté. Utilisez JPG, PNG ou GIF.');
            isValid = false;
        } else if (file.size > maxSize) {
            afficherErreur('image_principale', 'L\'image ne doit pas dépasser 5MB.');
            isValid = false;
        } else {
            effacerErreur('image_principale');
        }
    }
    
    return isValid;
}

// Validation du formulaire de profil
function validerProfil() {
    effacerToutesErreurs();
    let isValid = true;
    
    // Validation du nom
    const nom = document.getElementById('nom').value.trim();
    if (nom === '') {
        afficherErreur('nom', 'Le nom est obligatoire.');
        isValid = false;
    } else if (nom.length < 2) {
        afficherErreur('nom', 'Le nom doit contenir au moins 2 caractères.');
        isValid = false;
    } else {
        effacerErreur('nom');
    }
    
    // Validation du prénom
    const prenom = document.getElementById('prenom').value.trim();
    if (prenom === '') {
        afficherErreur('prenom', 'Le prénom est obligatoire.');
        isValid = false;
    } else if (prenom.length < 2) {
        afficherErreur('prenom', 'Le prénom doit contenir au moins 2 caractères.');
        isValid = false;
    } else {
        effacerErreur('prenom');
    }
    
    // Validation du téléphone (optionnel)
    const telephone = document.getElementById('telephone');
    if (telephone && telephone.value.trim() !== '') {
        const telValue = telephone.value.trim();
        if (!/^(\+216|00216)?[2-9]\d{7}$/.test(telValue.replace(/\s/g, ''))) {
            afficherErreur('telephone', 'Numéro de téléphone invalide.');
            isValid = false;
        } else {
            effacerErreur('telephone');
        }
    }
    
    // Validation du code postal (optionnel)
    const codePostal = document.getElementById('code_postal');
    if (codePostal && codePostal.value.trim() !== '') {
        if (!/^\d{4}$/.test(codePostal.value.trim())) {
            afficherErreur('code_postal', 'Le code postal doit contenir 4 chiffres.');
            isValid = false;
        } else {
            effacerErreur('code_postal');
        }
    }
    
    return isValid;
}

// Validation en temps réel
document.addEventListener('DOMContentLoaded', function() {
    // Ajouter la validation en temps réel pour tous les champs
    const inputs = document.querySelectorAll('input, select, textarea');
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            // Valider le champ spécifique quand il perd le focus
            if (this.id) {
                effacerErreur(this.id);
            }
        });
        
        input.addEventListener('input', function() {
            // Effacer l'erreur quand l'utilisateur commence à taper
            if (this.id && this.classList.contains('is-invalid')) {
                effacerErreur(this.id);
            }
        });
    });
});
