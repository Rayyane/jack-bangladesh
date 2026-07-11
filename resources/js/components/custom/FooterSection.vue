<!-- FooterSection.vue -->
<script setup lang="ts">
import { ref } from 'vue';

const email = ref('');
const message = ref('');

const handleSubmit = () => {
  // Handle form submission logic here
  console.log({ email: email.value, message: message.value });
};
</script>

<template>
  <footer class="bg-jack-blue text-white font-sans border-t border-white/10">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
      
      <!-- Main Layout Grid -->
      <div class="grid grid-cols-1 md:grid-cols-12 gap-8 lg:gap-12">
        
        <!-- ========================================== -->
        <!-- LEFT COLUMN: Logo & Branding (3/12 Width)  -->
        <!-- ========================================== -->
        <div class="md:col-span-3 space-y-4">
          <a href="#" class="inline-block">
            <img 
              src="/jacklogo.png" 
              alt="Jack Logo" 
              class="h-9 w-auto object-contain brightness-0 invert" 
            />
          </a>
          <p class="text-sm text-white/90 font-sans leading-relaxed max-w-sm">
            Empowering the global apparel manufacturing industry with intelligent, high-precision sewing technology and advanced automation frameworks.
          </p>
          <!-- Social Icons / Trust Badges placeholder -->
          <div class="pt-2 flex items-center space-x-4 text-white/50">
            <span class="text-[11px] uppercase tracking-wider font-semibold font-roboto">Innovation • Quality • Service</span>
          </div>
        </div>

        <!-- ========================================== -->
        <!-- MIDDLE COLUMN: Generic Links (4/12 Width)  -->
        <!-- ========================================== -->
        <div class="md:col-span-4 grid grid-cols-2 gap-6 sm:gap-8 font-roboto">
          <!-- Link Group 1 -->
          <div>
            <h4 class="text-xs font-bold uppercase tracking-wider text-orange-300 mb-4">
              Products
            </h4>
            <ul class="space-y-3.5 text-sm text-white/90">
              <li><a href="#" class="hover:text-white transition-colors">Lockstitch Machines</a></li>
              <li><a href="#" class="hover:text-white transition-colors">Overlock Series</a></li>
              <li><a href="#" class="hover:text-white transition-colors">Heavy Duty</a></li>
              <li><a href="#" class="hover:text-white transition-colors">Automatic Templates</a></li>
            </ul>
          </div>
          <!-- Link Group 2 -->
          <div>
            <h4 class="text-xs font-bold uppercase tracking-wider text-orange-300 mb-4">
              Support
            </h4>
            <ul class="space-y-3.5 text-sm text-white/90">
              <li><a href="#" class="hover:text-white transition-colors">Download Leaflets</a></li>
              <li><a href="#" class="hover:text-white transition-colors">Find a Dealer</a></li>
              <li><a href="#" class="hover:text-white transition-colors">Spare Parts</a></li>
              <li><a href="#" class="hover:text-white transition-colors">Contact Us</a></li>
            </ul>
          </div>
        </div>

        <!-- ========================================== -->
        <!-- RIGHT COLUMN: Contact Form (5/12 Width)    -->
        <!-- ========================================== -->
        <div class="md:col-span-5 space-y-4">
          <div>
            <h4 class="text-lg font-bold text-white tracking-normal font-sans">
              Quick Inquiry
            </h4>
            <p class="text-sm text-white/90 mt-1 font-roboto">
              Have questions about our machines? Drop us a quick message.
            </p>
          </div>

          <form @submit.prevent="handleSubmit" class="space-y-3 font-sans">
            <!-- Email Field -->
            <div>
              <input 
                v-model="email"
                type="email" 
                required
                placeholder="Your corporate email address" 
                class="w-full bg-white/10 text-white placeholder-white/70 border border-white/20 rounded-lg px-3.5 py-2 text-xs focus:outline-none focus:bg-white focus:text-foreground focus:placeholder-muted-foreground transition-all duration-150"
              />
            </div>
            
            <!-- Message Field -->
            <div>
              <textarea 
                v-model="message"
                rows="3"
                required
                placeholder="Describe your production line requirements..." 
                class="w-full bg-white/10 text-white placeholder-white/70 border border-white/20 rounded-lg px-3.5 py-2 text-xs focus:outline-none focus:bg-white focus:text-foreground focus:placeholder-muted-foreground transition-all duration-150 resize-none"
              ></textarea>
            </div>

            <!-- Submit Button -->
            <button 
              type="submit"
              class="w-full sm:w-auto px-5 py-2 bg-white text-jack-blue hover:bg-neutral-100 font-roboto font-bold text-xs rounded-lg shadow-sm transition-colors duration-150 cursor-pointer"
            >
              Send Message
            </button>
          </form>
        </div>

      </div>

      <!-- Bottom Copyright Strip -->
      <div class="mt-12 lg:mt-16 pt-6 border-t border-white/10 text-center md:flex md:items-center md:justify-between text-[13px] text-white/90 font-roboto">
        <p>&copy; 2026 Jack Sewing Machine Co. All rights reserved.</p>
        <div class="mt-2 md:mt-0 space-x-4">
          <a href="#" class="hover:text-white transition-colors">Privacy Policy</a>
          <a href="#" class="hover:text-white transition-colors">Terms of Service</a>
        </div>
      </div>

    </div>
  </footer>
</template>