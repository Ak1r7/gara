document.addEventListener('DOMContentLoaded', function() {
    // Slider hero
    const heroSlider = document.querySelector('.hero-slider');
    if (heroSlider) {
        let currentSlide = 0;
        const slides = document.querySelectorAll('.slide');
        const totalSlides = slides.length;
        
        function showSlide(index) {
            slides.forEach((slide, i) => {
                slide.style.display = i === index ? 'block' : 'none';
            });
        }
        
        function nextSlide() {
            currentSlide = (currentSlide + 1) % totalSlides;
            showSlide(currentSlide);
        }
        
        // Initialize slider
        showSlide(0);
        setInterval(nextSlide, 5000);
    }
    
    // Form validation
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            let valid = true;
            const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
            
            inputs.forEach(input => {
                if (!input.value.trim()) {
                    valid = false;
                    input.classList.add('error');
                    const errorMsg = document.createElement('span');
                    errorMsg.className = 'error-message';
                    errorMsg.textContent = 'Acest câmp este obligatoriu';
                    
                    if (!input.nextElementSibling || !input.nextElementSibling.classList.contains('error-message')) {
                        input.parentNode.insertBefore(errorMsg, input.nextSibling);
                    }
                } else {
                    input.classList.remove('error');
                    if (input.nextElementSibling && input.nextElementSibling.classList.contains('error-message')) {
                        input.nextElementSibling.remove();
                    }
                }
            });
            
            if (!valid) {
                e.preventDefault();
            }
        });
    });
    
    // Mobile menu toggle
    const menuToggle = document.createElement('div');
    menuToggle.className = 'menu-toggle';
    menuToggle.innerHTML = '<i class="fas fa-bars"></i>';
    const header = document.querySelector('header .container');
    if (header) {
        header.insertBefore(menuToggle, header.firstChild);
        
        menuToggle.addEventListener('click', function() {
            const nav = document.querySelector('nav');
            nav.style.display = nav.style.display === 'none' ? 'block' : 'none';
        });
        
        // Check window size on load and resize
        function checkWindowSize() {
            const nav = document.querySelector('nav');
            if (window.innerWidth > 768) {
                nav.style.display = 'block';
            } else {
                nav.style.display = 'none';
            }
        }
        
        window.addEventListener('load', checkWindowSize);
        window.addEventListener('resize', checkWindowSize);
    }
    
    // Admin sidebar toggle for mobile
    const adminToggle = document.createElement('div');
    adminToggle.className = 'admin-toggle';
    adminToggle.innerHTML = '<i class="fas fa-bars"></i> Meniu Admin';
    const adminContent = document.querySelector('.admin-content');
    if (adminContent) {
        adminContent.insertBefore(adminToggle, adminContent.firstChild);
        
        adminToggle.addEventListener('click', function() {
            const sidebar = document.querySelector('.admin-sidebar');
            sidebar.style.display = sidebar.style.display === 'none' ? 'block' : 'none';
        });
        
        // Check window size for admin sidebar
        function checkAdminWindowSize() {
            const sidebar = document.querySelector('.admin-sidebar');
            if (window.innerWidth > 992) {
                sidebar.style.display = 'block';
            } else {
                sidebar.style.display = 'none';
            }
        }
        
        window.addEventListener('load', checkAdminWindowSize);
        window.addEventListener('resize', checkAdminWindowSize);
    }
    
    // Password strength indicator
    const passwordInput = document.getElementById('parola');
    if (passwordInput) {
        const passwordStrength = document.createElement('div');
        passwordStrength.className = 'password-strength';
        passwordInput.parentNode.appendChild(passwordStrength);
        
        passwordInput.addEventListener('input', function() {
            const strength = calculatePasswordStrength(this.value);
            passwordStrength.textContent = strength.message;
            passwordStrength.style.color = strength.color;
        });
        
        function calculatePasswordStrength(password) {
            let strength = 0;
            
            // Length check
            if (password.length > 0) strength += 1;
            if (password.length >= 8) strength += 1;
            
            // Character diversity
            if (/[A-Z]/.test(password)) strength += 1;
            if (/[0-9]/.test(password)) strength += 1;
            if (/[^A-Za-z0-9]/.test(password)) strength += 1;
            
            let message, color;
            switch(strength) {
                case 0:
                    message = '';
                    color = 'transparent';
                    break;
                case 1:
                case 2:
                    message = 'Slabă';
                    color = 'red';
                    break;
                case 3:
                case 4:
                    message = 'Medie';
                    color = 'orange';
                    break;
                case 5:
                    message = 'Puternică';
                    color = 'green';
                    break;
                default:
                    message = 'Foarte puternică';
                    color = 'darkgreen';
            }
            
            return { message, color };
        }
    }
});// Funcții specifice pentru paginile adăugate

// Pagina bilete.php - Calcul preț estimativ
function setupTicketPriceCalculator() {
    const trainSelect = document.getElementById('train_id');
    const classSelect = document.getElementById('clasa');
    const ticketCount = document.getElementById('numar_bilete');
    const priceDisplay = document.getElementById('price-estimation');
    
    if (trainSelect && classSelect && ticketCount && priceDisplay) {
        function calculatePrice() {
            const trainId = trainSelect.value;
            const classValue = classSelect.value;
            const count = ticketCount.value;
            
            if (!trainId || !classValue || !count) {
                priceDisplay.textContent = '-';
                return;
            }
            
            // Aici ar trebui să faceți un request AJAX pentru a obține prețul real din baza de date
            // Pentru exemplu, folosim un calcul simplu
            const basePrice = 50;
            let classMultiplier = 1;
            let typeMultiplier = 1;
            
            if (classValue === '1') classMultiplier = 1.5;
            else if (classValue === '2') classMultiplier = 1.2;
            
            // Presupunem că tipul trenului este în valoarea selectată
            const trainText = trainSelect.options[trainSelect.selectedIndex].text;
            if (trainText.includes('InterCity')) typeMultiplier = 1.8;
            else if (trainText.includes('Rapid')) typeMultiplier = 1.5;
            else if (trainText.includes('Express')) typeMultiplier = 1.3;
            
            const totalPrice = basePrice * classMultiplier * typeMultiplier * count;
            priceDisplay.textContent = totalPrice.toFixed(2) + ' RON';
        }
        
        trainSelect.addEventListener('change', calculatePrice);
        classSelect.addEventListener('change', calculatePrice);
        ticketCount.addEventListener('input', calculatePrice);
        
        // Calcul inițial
        calculatePrice();
    }
}

// Inițializare funcții la încărcarea paginii
document.addEventListener('DOMContentLoaded', function() {
    // Funcții existente...
    
    // Adăugare noi funcții
    setupTicketPriceCalculator();
    
    // Validare formular contact
    const contactForm = document.querySelector('.contact-form form');
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            let valid = true;
            const requiredFields = contactForm.querySelectorAll('[required]');
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    valid = false;
                    field.classList.add('error');
                    
                    if (!field.nextElementSibling || !field.nextElementSibling.classList.contains('error-message')) {
                        const errorMsg = document.createElement('span');
                        errorMsg.className = 'error-message';
                        errorMsg.textContent = 'Acest câmp este obligatoriu';
                        field.parentNode.insertBefore(errorMsg, field.nextSibling);
                    }
                } else {
                    field.classList.remove('error');
                    if (field.nextElementSibling && field.nextElementSibling.classList.contains('error-message')) {
                        field.nextElementSibling.remove();
                    }
                }
            });
            
            // Validare specială pentru mesaj
            const mesajField = contactForm.querySelector('#mesaj');
            if (mesajField && mesajField.value.trim().length < 10) {
                valid = false;
                mesajField.classList.add('error');
                
                if (!mesajField.nextElementSibling || !mesajField.nextElementSibling.classList.contains('error-message')) {
                    const errorMsg = document.createElement('span');
                    errorMsg.className = 'error-message';
                    errorMsg.textContent = 'Mesajul trebuie să conțină minim 10 caractere';
                    mesajField.parentNode.insertBefore(errorMsg, mesajField.nextSibling);
                }
            }
            
            if (!valid) {
                e.preventDefault();
            }
        });
    }
});