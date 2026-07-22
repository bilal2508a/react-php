// Mehmaan Hub - Main JavaScript

// Navbar scroll effect
window.addEventListener('scroll', function() {
    var navbar = document.getElementById('navbar');
    if (navbar) {
        if (window.scrollY > 20) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    }
});

// Toggle mobile menu
function toggleMobileMenu() {
    var menu = document.getElementById('mobileMenu');
    if (menu) {
        menu.classList.toggle('show');
    }
}

// Toggle user menu
function toggleUserMenu() {
    var menu = document.getElementById('userMenu');
    if (menu) {
        menu.classList.toggle('show');
    }
}

// Close user menu on outside click
document.addEventListener('click', function(e) {
    var userMenu = document.getElementById('userMenu');
    if (userMenu && userMenu.classList.contains('show')) {
        var btn = e.target.closest('button[onclick="toggleUserMenu()"]');
        if (!btn && !userMenu.contains(e.target)) {
            userMenu.classList.remove('show');
        }
    }
});

// FAQ accordion
document.addEventListener('click', function(e) {
    var toggle = e.target.closest('.faq-toggle');
    if (toggle) {
        e.preventDefault();
        var answer = toggle.nextElementSibling;
        var icon = toggle.querySelector('.faq-icon');
        if (answer) {
            answer.classList.toggle('open');
        }
        if (icon) {
            icon.classList.toggle('bi-chevron-down');
            icon.classList.toggle('bi-chevron-up');
        }
    }
});

// Image gallery - set active image
function setActiveImage(src) {
    var mainImage = document.getElementById('mainImage');
    if (mainImage) {
        mainImage.src = src;
    }
    var thumbs = document.querySelectorAll('.gallery-thumb');
    thumbs.forEach(function(t) {
        t.classList.remove('active');
        if (t.getAttribute('src') === src) {
            t.classList.add('active');
        }
    });
}

// Toggle filters sidebar
function toggleFilters() {
    var sidebar = document.getElementById('filterSidebar');
    if (sidebar) {
        sidebar.classList.toggle('d-none');
    }
}

// Toggle amenity filter
function toggleAmenity(amenity, el) {
    el.classList.toggle('selected');
    var hidden = document.getElementById('amenitiesFilter');
    if (hidden) {
        var selected = [];
        document.querySelectorAll('.amenity-chip.selected').forEach(function(chip) {
            selected.push(chip.getAttribute('data-amenity'));
        });
        hidden.value = selected.join(',');
    }
}

// Toggle checklist item
function toggleChecklistItem(el) {
    el.classList.toggle('checked');
    var total = document.querySelectorAll('.checklist-item').length;
    var checked = document.querySelectorAll('.checklist-item.checked').length;
    var progressBar = document.getElementById('checklistProgress');
    var progressText = document.getElementById('checklistProgressText');
    if (progressBar) {
        var pct = total > 0 ? (checked / total) * 100 : 0;
        progressBar.style.width = pct + '%';
    }
    if (progressText) {
        progressText.textContent = checked + ' / ' + total;
    }
}

// Coupons
var coupons = {
    'EARLY20': 0.20,
    'STAY7': 0.15,
    'FAMILY4': 0.10,
    'WELCOME10': 0.10,
};

var appliedCoupon = null;
var appliedDiscount = 0;

// Apply coupon
function applyCoupon() {
    var input = document.getElementById('couponCode');
    var msg = document.getElementById('couponMessage');
    if (!input) return;
    var code = input.value.trim().toUpperCase();
    if (coupons.hasOwnProperty(code)) {
        appliedCoupon = code;
        appliedDiscount = coupons[code];
        if (msg) {
            msg.textContent = 'Coupon "' + code + '" applied! ' + (appliedDiscount * 100) + '% off';
            msg.className = 'text-success';
        }
    } else {
        appliedCoupon = null;
        appliedDiscount = 0;
        if (msg) {
            msg.textContent = 'Invalid coupon code';
            msg.className = 'text-error';
        }
    }
    recalculateTotal();
}

// Recalculate total
function recalculateTotal() {
    var pricePerNight = parseFloat(document.getElementById('pricePerNight') ? document.getElementById('pricePerNight').value : 0);
    var checkIn = document.getElementById('checkIn') ? document.getElementById('checkIn').value : '';
    var checkOut = document.getElementById('checkOut') ? document.getElementById('checkOut').value : '';

    var nights = 0;
    if (checkIn && checkOut) {
        var d1 = new Date(checkIn);
        var d2 = new Date(checkOut);
        nights = Math.round((d2 - d1) / (1000 * 60 * 60 * 24));
        if (nights < 0) nights = 0;
    }

    var subtotal = pricePerNight * nights;
    var serviceFee = subtotal * 0.05;
    var discount = subtotal * appliedDiscount;
    var total = subtotal + serviceFee - discount;

    var nightsEl = document.getElementById('nightsCount');
    if (nightsEl) nightsEl.textContent = nights;

    var subtotalEl = document.getElementById('subtotalAmount');
    if (subtotalEl) subtotalEl.textContent = 'PKR ' + subtotal.toLocaleString();

    var feeEl = document.getElementById('serviceFeeAmount');
    if (feeEl) feeEl.textContent = 'PKR ' + serviceFee.toLocaleString();

    var discountEl = document.getElementById('discountAmount');
    if (discountEl) discountEl.textContent = 'PKR ' + discount.toLocaleString();

    var totalEl = document.getElementById('totalAmount');
    if (totalEl) totalEl.textContent = 'PKR ' + total.toLocaleString();

    var hiddenTotal = document.getElementById('hiddenTotal');
    if (hiddenTotal) hiddenTotal.value = total;
}

// Scroll to top
function scrollToTop() {
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// AI Description Generator (for add-property page)
function generateDescription() {
    var title = document.getElementById('title') ? document.getElementById('title').value : '';
    var city = document.getElementById('city') ? document.getElementById('city').value : '';
    var type = document.getElementById('propertyType') ? document.getElementById('propertyType').value : '';
    var bedrooms = document.getElementById('bedrooms') ? document.getElementById('bedrooms').value : '';
    var guests = document.getElementById('maxGuests') ? document.getElementById('maxGuests').value : '';

    var desc = 'Welcome to ' + (title || 'this beautiful property') + ' in ' + (city || 'Pakistan') + '. ';
    desc += 'This ' + (type || 'property') + ' features ' + (bedrooms || 'multiple') + ' bedrooms and can accommodate up to ' + (guests || '4') + ' guests. ';
    desc += 'Enjoy modern amenities, comfortable living spaces, and a prime location. ';
    desc += 'Perfect for families, couples, and business travelers looking for a memorable stay. ';
    desc += 'Book now and experience the best of Pakistani hospitality!';

    var descEl = document.getElementById('description');
    if (descEl) {
        descEl.value = desc;
    }
}

// Add image URL to preview
function addImagePreview() {
    var input = document.getElementById('imageUrl');
    var preview = document.getElementById('imagePreview');
    if (!input || !preview) return;
    var url = input.value.trim();
    if (url) {
        var img = document.createElement('img');
        img.src = url;
        img.className = 'gallery-thumb';
        img.style.cssText = 'width:80px;height:80px;object-fit:cover;border-radius:10px;cursor:pointer;margin-right:8px;';
        preview.appendChild(img);
        input.value = '';

        var hidden = document.getElementById('imagesHidden');
        if (hidden) {
            var urls = hidden.value ? hidden.value.split(',') : [];
            urls.push(url);
            hidden.value = urls.join(',');
        }
    }
}

// Filter properties by category (FAQ page)
function filterFAQ(category) {
    var items = document.querySelectorAll('.faq-item');
    var btns = document.querySelectorAll('.faq-category-btn');
    btns.forEach(function(b) { b.classList.remove('active'); });
    event.target.classList.add('active');
    items.forEach(function(item) {
        if (category === 'all' || item.getAttribute('data-category') === category) {
            item.style.display = '';
        } else {
            item.style.display = 'none';
        }
    });
}

// Search FAQs
function searchFAQ() {
    var query = document.getElementById('faqSearch') ? document.getElementById('faqSearch').value.toLowerCase() : '';
    var items = document.querySelectorAll('.faq-item');
    items.forEach(function(item) {
        var text = item.textContent.toLowerCase();
        if (text.includes(query)) {
            item.style.display = '';
        } else {
            item.style.display = 'none';
        }
    });
}
