<!-- FooterSection.vue -->
<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { Phone } from '@lucide/vue';

const form = useForm({ email: '', message: '', source: 'footer' });
const handleSubmit = () => form.post('/enquiries', { preserveScroll: true, onSuccess: () => form.reset('email', 'message') });
</script>

<template>
  <footer class="bg-jack-blue text-white font-sans border-t border-white/10">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8 lg:py-10">
      
      <!-- Main Layout Grid -->
      <div class="grid grid-cols-1 md:grid-cols-12 gap-6 lg:gap-8">
        
        <!-- ========================================== -->
        <!-- LEFT COLUMN: Logo & Branding (3/12 Width)  -->
        <!-- ========================================== -->
        <div class="md:col-span-3 space-y-2">
          <a href="#" class="inline-block">
            <img 
              src="/jbl_logo.png" 
              alt="Jack Logo" 
              class="h-9 w-auto object-contain brightness-0 invert" 
            />
          </a>

          <div class="flex items-center space-x-4 text-white/50">
            <span class="text-[11px] uppercase tracking-wider font-semibold font-roboto">Innovation • Quality • Service</span>
          </div>

          <p class="text-sm text-white/90 font-sans leading-relaxed max-w-sm">
            <span class="font-bold">Corporate Office:</span><br>
            House No 8/6/1, Road No: 1, Shyamoli, Sher-E-Bangla Nagar, Dhaka-1207.<br>
            <span class="flex items-center gap-2"><Phone class="size-3"/> +880 1971-564600</span>
          </p>

          <p class="text-sm text-white/90 font-sans leading-relaxed max-w-sm">
            <span class="font-bold">Head Office:</span><br>
            4/10, Sobuj Chaya Housing, Goran Charbari, (Mirpur Beribadh) Besides Water Development Board, Mirpur-1, Dhaka-1216.<br>
            <span class="flex items-center gap-2"><Phone class="size-3"/> +88 01971-564595</span>
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
                v-model="form.email"
                type="email" 
                required
                placeholder="Your corporate email address" 
                class="w-full bg-white/10 text-white placeholder-white/70 border border-white/20 rounded-lg px-3.5 py-2 text-xs focus:outline-none focus:bg-white focus:text-foreground focus:placeholder-muted-foreground transition-all duration-150"
              />
            </div>
            
            <!-- Message Field -->
            <div>
              <textarea 
                v-model="form.message"
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
              {{ form.processing ? 'Sending...' : 'Send Message' }}
            </button>
            <p v-if="form.errors.email || form.errors.message" class="text-xs text-orange-200">{{ form.errors.email || form.errors.message }}</p>
            <p v-if="form.recentlySuccessful" class="text-xs text-green-200">Thank you — your message has been sent.</p>
          </form>
        </div>

      </div>

      <!-- Bottom Copyright Strip -->
      <div class="mt-12 lg:mt-16 pt-6 border-t border-white/10 text-center md:flex md:items-center md:justify-between text-[13px] text-white/90 font-roboto">
        <p>&copy; 2026 Jack Bangladesh Ltd. All rights reserved.</p>
        <div class="mt-2 md:mt-0 space-x-4">
          <a href="#" class="hover:text-white transition-colors">Privacy Policy</a>
          <a href="#" class="hover:text-white transition-colors">Terms of Service</a>
        </div>
      </div>

    </div>
  </footer>
</template>
