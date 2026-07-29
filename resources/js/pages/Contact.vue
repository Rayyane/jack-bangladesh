<!-- ContactView.vue -->
<script setup lang="ts">
import Navbar from '@/components/custom/Navbar.vue';
import Footer from '@/components/custom/FooterSection.vue';
import { ref } from 'vue';

const form = ref({
  name: '',
  email: '',
  phone: '',
  subject: '',
  message: ''
});

const handleSubmit = () => {
  console.log('Submitted contact form:', form.value);
  // Handle form submission logic here
};
</script>

<template>
  <Navbar />
  <div class="min-h-screen bg-background font-sans">
    
    <!-- PAGE HEADER -->
    <section class="bg-jack-blue text-white py-12 lg:py-16">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-2">
        <h1 class="text-3xl sm:text-4xl font-extrabold tracking-normal">Contact Us</h1>
        <p class="text-sm text-white/80 font-roboto max-w-lg mx-auto">
          Get in touch with our expert sales and technical support teams. We're here to assist with equipment inquiries and dealer support.
        </p>
      </div>
    </section>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16 space-y-16">
      
      <!-- ========================================== -->
      <!-- SECTION 1: FORM & MAP (2-COLUMN GRID)      -->
      <!-- ========================================== -->
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-start">
        
        <!-- LEFT COLUMN: Contact Form (6/12 Width) -->
        <div class="lg:col-span-6 bg-card border border-border rounded-xl p-6 sm:p-8 shadow-sm space-y-6">
          <div>
            <h2 class="text-xl font-bold text-foreground">Send Us a Message</h2>
            <p class="text-xs text-muted-foreground mt-1 font-roboto">Fill out the form below and our team will get back to you within 24 hours.</p>
          </div>

          <form @submit.prevent="handleSubmit" class="space-y-4 font-sans">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <!-- Full Name -->
              <div>
                <label class="block text-xs font-semibold text-foreground mb-1">Full Name *</label>
                <input 
                  v-model="form.name" 
                  type="text" 
                  required 
                  placeholder="John Doe" 
                  class="w-full bg-background border border-border rounded-lg px-3.5 py-2 text-xs text-foreground focus:outline-none focus:border-jack-blue transition-colors"
                />
              </div>

              <!-- Corporate Email -->
              <div>
                <label class="block text-xs font-semibold text-foreground mb-1">Corporate Email *</label>
                <input 
                  v-model="form.email" 
                  type="email" 
                  required 
                  placeholder="john@company.com" 
                  class="w-full bg-background border border-border rounded-lg px-3.5 py-2 text-xs text-foreground focus:outline-none focus:border-jack-blue transition-colors"
                />
              </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <!-- Phone -->
              <div>
                <label class="block text-xs font-semibold text-foreground mb-1">Phone Number</label>
                <input 
                  v-model="form.phone" 
                  type="tel" 
                  placeholder="+880 1700-000000" 
                  class="w-full bg-background border border-border rounded-lg px-3.5 py-2 text-xs text-foreground focus:outline-none focus:border-jack-blue transition-colors"
                />
              </div>

              <!-- Subject -->
              <div>
                <label class="block text-xs font-semibold text-foreground mb-1">Inquiry Type</label>
                <select 
                  v-model="form.subject" 
                  class="w-full bg-background border border-border rounded-lg px-3.5 py-2 text-xs text-foreground focus:outline-none focus:border-jack-blue transition-colors"
                >
                  <option value="" disabled>Select inquiry type</option>
                  <option value="sales">Machine Purchase / Quote</option>
                  <option value="parts">Spare Parts & Accessories</option>
                  <option value="service">Technical Support & Warranty</option>
                  <option value="dealership">Become a Dealer</option>
                </select>
              </div>
            </div>

            <!-- Message -->
            <div>
              <label class="block text-xs font-semibold text-foreground mb-1">Message *</label>
              <textarea 
                v-model="form.message" 
                rows="4" 
                required 
                placeholder="Details about your factory requirements or machine models..." 
                class="w-full bg-background border border-border rounded-lg px-3.5 py-2 text-xs text-foreground focus:outline-none focus:border-jack-blue transition-colors resize-none"
              ></textarea>
            </div>

            <button 
              type="submit" 
              class="w-full bg-jack-blue text-white font-roboto font-bold text-xs py-3 rounded-lg shadow hover:bg-jack-blue/90 transition-all cursor-pointer"
            >
              Submit Inquiry
            </button>
          </form>
        </div>

        <!-- RIGHT COLUMN: Google Map Location & Info (6/12 Width) -->
        <div class="lg:col-span-6 space-y-6">
          
          <!-- Map Container -->
          <div class="bg-card border border-border rounded-xl p-2 shadow-sm overflow-hidden h-[380px] lg:h-[420px] relative">
            <iframe 
              class="w-full h-full rounded-lg border-0"
              src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d58409.86977763151!2d90.2816104888916!3d23.796654142306412!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3755c100578b8da5%3A0x1c3853ded9a64299!2sJack%20Bangladesh%20Ltd%20(Corporate%20Office)!5e0!3m2!1sen!2sbd!4v1785300492341!5m2!1sen!2sbd" 
              allowfullscreen 
              loading="lazy" 
              referrerpolicy="no-referrer-when-downgrade"
            ></iframe>
          </div>

          <!-- Direct Map Action Link -->
          <div class="bg-muted/30 border border-border rounded-xl p-4 flex items-center justify-between">
            <div class="flex items-center space-x-3">
              <div class="p-2 bg-jack-blue/10 text-jack-blue rounded-lg">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
              </div>
              <div>
                <h4 class="text-xs font-bold text-foreground">Open in Google Maps</h4>
                <p class="text-[11px] text-muted-foreground font-roboto">View location pin directly in Google Maps application</p>
              </div>
            </div>

            <a 
              href="https://maps.app.goo.gl/EjxWtjUD2PY7Na628" 
              target="_blank" 
              rel="noopener noreferrer"
              class="text-xs font-bold text-jack-blue bg-white border border-border px-3.5 py-2 rounded-md hover:bg-neutral-50 transition-colors"
            >
              Open Map ↗
            </a>
          </div>

        </div>

      </div>

      <!-- ========================================== -->
      <!-- SECTION 2: QUICK CONTACT CARDS            -->
      <!-- ========================================== -->
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 font-roboto">
        <div class="bg-card border border-border rounded-xl p-6 text-center space-y-2 shadow-sm">
          <div class="w-10 h-10 mx-auto bg-jack-blue/10 text-jack-blue rounded-full flex items-center justify-center">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
            </svg>
          </div>
          <h3 class="text-sm font-bold text-foreground">Phone Hotline</h3>
          <p class="text-xs text-muted-foreground">+880 (2) 987-6543 / +880 1700-000000</p>
        </div>

        <div class="bg-card border border-border rounded-xl p-6 text-center space-y-2 shadow-sm">
          <div class="w-10 h-10 mx-auto bg-jack-blue/10 text-jack-blue rounded-full flex items-center justify-center">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
          </div>
          <h3 class="text-sm font-bold text-foreground">Email Inquiries</h3>
          <p class="text-xs text-muted-foreground">info@jacksewing.com / sales@jacksewing.com</p>
        </div>

        <div class="bg-card border border-border rounded-xl p-6 text-center space-y-2 shadow-sm">
          <div class="w-10 h-10 mx-auto bg-jack-blue/10 text-jack-blue rounded-full flex items-center justify-center">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <h3 class="text-sm font-bold text-foreground">Operating Hours</h3>
          <p class="text-xs text-muted-foreground">Mon - Sat: 9:00 AM - 6:00 PM</p>
        </div>
      </div>

    </div>
  </div>
  <Footer />
</template>