<!-- Floating Contact Widget -->
@if($globalCompany && ($globalCompany->whatsapp_number || $globalCompany->mobile_number || $globalCompany->company_phone))
<div class="floating-contact-widget" id="floating-contact-widget">
    <div class="contact-toggle" onclick="toggleContactWidget()">
        <i class="fas fa-phone-alt"></i>
        <span class="contact-badge">{{ collect([$globalCompany->whatsapp_number, $globalCompany->mobile_number, $globalCompany->company_phone, $globalCompany->alternate_phone])->filter()->count() }}</span>
    </div>
    
    <div class="contact-options" id="contact-options">
        @if($globalCompany->whatsapp_number)
        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $globalCompany->whatsapp_number) }}?text=Hi, I'm interested in your products" 
           target="_blank" class="contact-option whatsapp" title="Chat on WhatsApp">
            <i class="fab fa-whatsapp"></i>
            <span>WhatsApp</span>
        </a>
        @endif
        
        @if($globalCompany->mobile_number)
        <a href="tel:{{ $globalCompany->mobile_number }}" class="contact-option mobile" title="Call Mobile">
            <i class="fas fa-mobile-alt"></i>
            <span>Call Mobile</span>
        </a>
        @endif
        
        @if($globalCompany->company_phone)
        <a href="tel:{{ $globalCompany->company_phone }}" class="contact-option office" title="Call Office">
            <i class="fas fa-phone"></i>
            <span>Call Office</span>
        </a>
        @endif
        
        @if($globalCompany->alternate_phone)
        <a href="tel:{{ $globalCompany->alternate_phone }}" class="contact-option alternate" title="Alternate Number">
            <i class="fas fa-phone-alt"></i>
            <span>Alternate</span>
        </a>
        @endif
    </div>
</div>

<style>
.floating-contact-widget {
    position: fixed;
    bottom: 100px;
    left: 20px;
    z-index: 1000;
}

.contact-toggle {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #25d366, #128c7e);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 24px;
    cursor: pointer;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
    transition: all 0.3s ease;
    position: relative;
}

.contact-toggle:hover {
    transform: scale(1.1);
    box-shadow: 0 6px 25px rgba(0, 0, 0, 0.4);
}

.contact-badge {
    position: absolute;
    top: -8px;
    right: -8px;
    background: #ff4444;
    color: white;
    border-radius: 50%;
    width: 24px;
    height: 24px;
    font-size: 12px;
    font-weight: bold;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid white;
}

.contact-options {
    position: absolute;
    bottom: 70px;
    left: 0;
    display: none;
    flex-direction: column;
    gap: 10px;
    animation: slideUp 0.3s ease;
}

.contact-options.show {
    display: flex;
}

.contact-option {
    display: flex;
    align-items: center;
    padding: 12px 16px;
    background: white;
    border-radius: 25px;
    text-decoration: none;
    color: #333;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
    transition: all 0.3s ease;
    font-weight: 500;
    min-width: 140px;
}

.contact-option:hover {
    transform: translateX(5px);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
    color: white;
}

.contact-option.whatsapp:hover {
    background: #25d366;
}

.contact-option.mobile:hover {
    background: #007bff;
}

.contact-option.office:hover {
    background: #28a745;
}

.contact-option.alternate:hover {
    background: #6f42c1;
}

.contact-option i {
    margin-right: 8px;
    font-size: 18px;
}

.contact-option span {
    font-size: 14px;
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@media (max-width: 768px) {
    .floating-contact-widget {
        bottom: 80px;
        left: 15px;
    }
    
    .contact-toggle {
        width: 55px;
        height: 55px;
        font-size: 20px;
    }
    
    .contact-option {
        padding: 10px 14px;
        min-width: 120px;
    }
}
</style>

<script>
function toggleContactWidget() {
    const options = document.getElementById('contact-options');
    options.classList.toggle('show');
}

// Close widget when clicking outside
document.addEventListener('click', function(event) {
    const widget = document.getElementById('floating-contact-widget');
    if (!widget.contains(event.target)) {
        document.getElementById('contact-options').classList.remove('show');
    }
});
</script>
@endif
