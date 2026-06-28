<!-- HeroSection.vue -->
<script setup lang="ts">
// No backend logic needed. Placeholder content & Unsplash images are hardcoded below.
</script>

<template>
  <section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-6 font-sans">
    <!-- Main 2-Column Grid Wrapper (2/3 and 1/3 split) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 lg:gap-6 items-stretch">
      
      <!-- ========================================== -->
      <!-- LEFT COLUMN: 2/3 Width, Single Tall Poster -->
      <!-- ========================================== -->
      <div class="md:col-span-2 relative group overflow-hidden rounded-xl border border-border bg-card shadow-md h-[400px] md:h-[550px]">
        <!-- Background Image -->
        <img 
          src="https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?auto=format&fit=crop&w=1200&q=80" 
          alt="Main Promo Poster" 
          class="absolute inset-0 w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
        />
        <!-- Dark Overlay Gradient -->
        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent"></div>
        
        <!-- Content Area -->
        <div class="absolute inset-0 p-6 sm:p-10 flex flex-col justify-end text-white">
          <span class="inline-block bg-jack-blue font-roboto text-xs font-bold uppercase tracking-wider px-2.5 py-1 rounded mb-3 self-start">
            New Arrival
          </span>
          <h2 class="text-2xl sm:text-4xl font-extrabold tracking-tight mb-2 max-w-xl">
            Next-Gen Smart Automation Systems
          </h2>
          <p class="text-sm sm:text-base text-white/80 max-w-md mb-6">
            Boost your industrial production line precision with our latest AI-driven sewing frameworks.
          </p>
          <div>
            <a href="#" class="inline-block bg-white text-foreground font-roboto font-semibold text-sm px-6 py-2.5 rounded-lg shadow hover:bg-neutral-100 transition-colors duration-150">
              Explore Collection
            </a>
          </div>
        </div>
      </div>

      <!-- ========================================== -->
      <!-- RIGHT COLUMN: 1/3 Width, 2 Stacked Posters -->
      <!-- ========================================== -->
      <!-- flex-col makes them share the exact height bounds of the row -->
      <div class="md:col-span-1 flex flex-col gap-4 lg:gap-6 justify-between h-auto">
        
        <!-- Top Poster -->
        <div class="relative group flex-1 overflow-hidden rounded-xl border border-border bg-card shadow-md min-h-[190px] md:min-h-0">
          <img 
            src="https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?auto=format&fit=crop&w=600&q=80" 
            alt="Top Mini Promo" 
            class="absolute inset-0 w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
          />
          <div class="absolute inset-0 bg-gradient-to-t from-black/75 via-black/30 to-transparent"></div>
          
          <div class="absolute inset-0 p-6 flex flex-col justify-end text-white">
            <h3 class="text-lg font-bold mb-1">Heavy Duty Series</h3>
            <p class="text-xs text-white/80 mb-3">Engineered for leather & canvas.</p>
            <div>
              <a href="#" class="inline-block text-xs font-roboto font-bold text-jack-blue bg-white px-3 py-1.5 rounded hover:bg-neutral-100 transition-colors">
                View Deals
              </a>
            </div>
          </div>
        </div>

        <!-- Bottom Poster -->
        <div class="relative group flex-1 overflow-hidden rounded-xl border border-border bg-card shadow-md min-h-[190px] md:min-h-0">
          <img 
            src="https://images.unsplash.com/photo-1441986300917-64674bd600d8?auto=format&fit=crop&w=600&q=80" 
            alt="Bottom Mini Promo" 
            class="absolute inset-0 w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
          />
          <div class="absolute inset-0 bg-gradient-to-t from-black/75 via-black/30 to-transparent"></div>
          
          <div class="absolute inset-0 p-6 flex flex-col justify-end text-white">
            <span class="text-orange-400 text-xs font-bold uppercase tracking-wider mb-1 block">Up to 30% Off</span>
            <h3 class="text-lg font-bold mb-3">Genuine Spare Parts</h3>
            <div>
              <a href="#" class="inline-block text-xs font-roboto font-bold text-white border border-white/50 px-3 py-1.5 rounded hover:bg-white hover:text-foreground transition-all">
                Shop Parts
              </a>
            </div>
          </div>
        </div>

      </div>

    </div>
  </section>
</template>