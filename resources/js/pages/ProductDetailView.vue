<!-- ProductDetailView.vue -->
<script setup lang="ts">
import { ref } from 'vue';
import Navbar from '@/components/custom/Navbar.vue';
import Footer from '@/components/custom/FooterSection.vue';

// Mock Product Meta Core
const product = {
  name: 'Jack E4S',
  tagline: 'Smart Overlock Industrial System',
  subText: 'Advanced high-speed overlock machine integrated with a physical L/M/H (Light / Medium / Heavy) material selector switch, designed to instantly adapt torque and feeding characteristics across variable fabric weight profiles.',
};

// Deep dive alternating feature sections
const dynamicFeatures = [
  {
    id: 1,
    title: 'Improved performance',
    body: 'It can sew 7% thicker material, for example it can easily sew 15 layers of jeans thanks to the AMH1 sewing organs.',
    imgSrc: '/card1.jpg',
  },
  {
    id: 2,
    title: 'Highly reliable electronics',
    body: 'It can continue sewing under unstable currents from 110V to 295V without slowing down on thicknesses. It also withstands static current peaks of up to 12,000V, which is the primary cause of damage to electronic components.',
    imgSrc: '/card2.jpg',
  }
];
</script>

<template>
    <Navbar />
  <div class="min-h-screen bg-background font-sans">
    
    <!-- ========================================== -->
    <!-- ZONE 1: PRODUCT HERO SECTION               -->
    <!-- ========================================== -->
    <section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-12">
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-16 items-center">
        
        <!-- Left: Floating Machine Stage (7/12 Width) -->
        <div class="lg:col-span-7 bg-muted/30 border border-border/40 rounded-2xl p-8 lg:p-12 aspect-[4/3] flex items-center justify-center relative overflow-hidden group">
          <!-- Ambient structural grid graphics for visual polish -->
          <div class="absolute inset-0 bg-[linear-gradient(to_right,#8080800a_1px,transparent_1px),linear-gradient(to_bottom,#8080800a_1px,transparent_1px)] bg-[size:14px_24px]"></div>
          
          <img 
            src="/E4S.png" 
            alt="Jack E4S Product Display" 
            class="relative z-10 max-h-full max-w-full object-contain drop-shadow-2xl transition-transform duration-500 group-hover:scale-[1.02]"
          />
        </div>

        <!-- Right: Purchase Hook Specifications (5/12 Width) -->
        <div class="lg:col-span-5 space-y-6">
          <div class="space-y-2">
            <span class="text-xs font-bold uppercase tracking-widest text-jack-blue font-roboto">High-Speed Overlock</span>
            <h1 class="text-4xl lg:text-5xl font-extrabold tracking-tight text-foreground">{{ product.name }}</h1>
            <p class="text-lg font-medium text-muted-foreground font-roboto">{{ product.tagline }}</p>
          </div>

          <p class="text-sm text-muted-foreground leading-relaxed font-sans">
            {{ product.subText }}
          </p>

          <!-- Core Call To Action Panel -->
          <div class="pt-4 flex flex-col sm:flex-row gap-3 font-roboto">
            <a href="#" class="flex-1 text-center font-bold text-xs text-white bg-jack-blue rounded-lg py-3.5 shadow-sm hover:bg-jack-blue/90 transition-all">
              Locate Nearest Dealer
            </a>
            <a href="#" class="flex-1 text-center font-bold text-xs text-foreground bg-card border border-border rounded-lg py-3.5 hover:bg-muted transition-all">
              Download Official Leaflet
            </a>
          </div>
        </div>

      </div>
    </section>

    <!-- ========================================== -->
    <!-- ZONE 2: ALTERNATING TECHNICAL FEATURES     -->
    <!-- ========================================== -->
    <section class="border-t border-border/40 bg-muted/10 py-20">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 space-y-24">
        
        <div 
          v-for="(feature, idx) in dynamicFeatures" 
          :key="feature.id"
          class="grid grid-cols-1 md:grid-cols-2 gap-8 lg:gap-16 items-center"
        >
          <!-- Alternating Layout Sorting Mechanics using conditional flex orders -->
          <div :class="['space-y-4', idx % 2 === 1 ? 'md:order-2' : '']">
            <h2 class="text-2xl sm:text-3xl font-extrabold text-foreground tracking-normal">
              {{ feature.title }}
            </h2>
            <p class="text-lg text-muted-foreground leading-relaxed font-sans">
              {{ feature.body }}
            </p>
          </div>

          <div :class="['overflow-hidden rounded-xl border border-border shadow-md aspect-[16/10]', idx % 2 === 1 ? 'md:order-1' : '']">
            <img 
              :src="feature.imgSrc" 
              :alt="feature.title" 
              class="w-full h-full object-cover"
            />
          </div>
        </div>

      </div>
    </section>

    <!-- ========================================== -->
    <!-- ZONE 3: PRESTIGE YOUTUBE MEDIA THEATER    -->
    <!-- ========================================== -->
    <section class="bg-neutral-950 text-white py-20">
      <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8 text-center space-y-8">
        <div class="max-w-2xl mx-auto space-y-2">
          <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight">See the Machine in Action</h2>
          <p class="text-sm text-neutral-400 font-roboto">Watch real-world fabric tracking speed tests and operational adjustments.</p>
        </div>

        <!-- Fully Responsive Video Container Frame wrapper maintaining a crisp 16:9 box -->
        <div class="w-full aspect-video rounded-xl overflow-hidden border border-neutral-800 shadow-2xl bg-neutral-900">
          <iframe 
            class="w-full h-full"
            src="https://www.youtube.com/embed/8_ympqgXpK0?si=fg29ftG11Pyfdjrp" 
            title="YouTube video player" 
            frameborder="0" 
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
            referrerpolicy="strict-origin-when-cross-origin" 
            allowfullscreen
          ></iframe>
        </div>
      </div>
    </section>

    <!-- ========================================== -->
    <!-- ZONE 4: FULL-WIDTH TECHNICAL SPEC SHEET    -->
    <!-- ========================================== -->
    <section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-20">
      <div class="mb-8 border-b border-border pb-4">
        <h2 class="text-2xl font-extrabold text-foreground tracking-normal">Technical Specifications</h2>
        <p class="text-lg text-muted-foreground leading-relaxed mt-0.5">Comprehensive engineering reference blueprints and operational limits.</p>
      </div>

      <!-- Full-Width Spec Sheet Canvas Frame -->
      <div class="w-full border border-border rounded-xl p-2 bg-card shadow-sm overflow-x-auto">
        <!-- Leave the source string handling to you -->
        <img 
          src="/specs.jpg" 
          alt="Technical Specification Matrix Blueprint" 
          class="w-full h-auto min-w-[768px] rounded-lg"
        />
      </div>
    </section>

  </div>
  <Footer />
</template>